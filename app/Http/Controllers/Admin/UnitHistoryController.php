<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UnitHistoryController extends Controller
{
    /**
     * Menampilkan halaman ringkasan rekapitulasi riwayat tiket per unit rumah sakit.
     * Menggunakan query agregat efisien (groupBy & count) tanpa looping query di Blade.
     */
    public function index(Request $request): View
    {
        $periode = $request->input('periode', 'semua');
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');

        // 1. Siapkan query dasar tiket terfilter periode
        $ticketQuery = $this->buildDateFilteredQuery($periode, $tanggalMulai, $tanggalSelesai);

        // 2. Ambil data agregat per unit: Total tiket & tiket terbuka (status 'baru' + 'diproses')
        $unitCounts = (clone $ticketQuery)
            ->select(
                'tickets.unit_id',
                DB::raw('count(*) as total_tickets'),
                DB::raw("sum(case when tickets.status in ('baru', 'diproses') then 1 else 0 end) as open_tickets"),
                DB::raw("sum(case when tickets.status = 'baru' then 1 else 0 end) as baru_tickets"),
                DB::raw("sum(case when tickets.status = 'diproses' then 1 else 0 end) as diproses_tickets"),
                DB::raw("sum(case when tickets.status = 'selesai' then 1 else 0 end) as selesai_tickets"),
                DB::raw("sum(case when tickets.status = 'ditutup' then 1 else 0 end) as ditutup_tickets")
            )
            ->groupBy('tickets.unit_id')
            ->get()
            ->keyBy('unit_id');

        // 3. Ambil data agregat rata-rata waktu penyelesaian tiket per unit (waktu_selesai - created_at)
        // Kompatibel dengan SQLite (testing) dan MySQL (production)
        if (DB::getDriverName() === 'sqlite') {
            $avgResolutionByUnit = (clone $ticketQuery)
                ->whereNotNull('tickets.waktu_selesai')
                ->select(
                    'tickets.unit_id',
                    DB::raw('AVG(strftime("%s", tickets.waktu_selesai) - strftime("%s", tickets.created_at)) as avg_seconds')
                )
                ->groupBy('tickets.unit_id')
                ->pluck('avg_seconds', 'unit_id');
        } else {
            $avgResolutionByUnit = (clone $ticketQuery)
                ->whereNotNull('tickets.waktu_selesai')
                ->select(
                    'tickets.unit_id',
                    DB::raw('AVG(TIMESTAMPDIFF(SECOND, tickets.created_at, tickets.waktu_selesai)) as avg_seconds')
                )
                ->groupBy('tickets.unit_id')
                ->pluck('avg_seconds', 'unit_id');
        }

        // 4. Ambil kategori kendala terbanyak untuk tiap unit (1 query agregat join categories)
        $topCategoriesByUnit = (clone $ticketQuery)
            ->join('categories', 'tickets.category_id', '=', 'categories.id')
            ->select(
                'tickets.unit_id',
                'categories.nama as category_nama',
                DB::raw('count(tickets.id) as category_count')
            )
            ->groupBy('tickets.unit_id', 'categories.id', 'categories.nama')
            ->orderByDesc('category_count')
            ->get()
            ->groupBy('unit_id')
            ->map(function ($items) {
                $top = $items->first();
                return [
                    'nama' => $top->category_nama,
                    'count' => (int) $top->category_count,
                ];
            });

        // 5. Gabungkan semua metrik ke setiap unit RS
        $units = Unit::orderBy('nama')->get();

        $unitSummaries = $units->map(function ($unit) use ($unitCounts, $avgResolutionByUnit, $topCategoriesByUnit) {
            $stats = $unitCounts->get($unit->id);
            $total = $stats ? (int) $stats->total_tickets : 0;
            $open = $stats ? (int) $stats->open_tickets : 0;
            $avgSec = $avgResolutionByUnit->get($unit->id);
            $topCat = $topCategoriesByUnit->get($unit->id);

            return (object) [
                'id' => $unit->id,
                'nama' => $unit->nama,
                'total_tickets' => $total,
                'open_tickets' => $open,
                'baru_tickets' => $stats ? (int) $stats->baru_tickets : 0,
                'diproses_tickets' => $stats ? (int) $stats->diproses_tickets : 0,
                'selesai_tickets' => $stats ? (int) $stats->selesai_tickets : 0,
                'ditutup_tickets' => $stats ? (int) $stats->ditutup_tickets : 0,
                'avg_duration' => $this->formatResolutionTime($avgSec),
                'top_category' => $topCat ? "{$topCat['nama']} ({$topCat['count']})" : '-',
                'raw_top_category' => $topCat,
            ];
        });

        // 6. Ringkasan global untuk kartu atas
        $totalTicketsAll = $unitSummaries->sum('total_tickets');
        $totalOpenAll = $unitSummaries->sum('open_tickets');
        $unitsWithTicketsCount = $unitSummaries->filter(fn ($u) => $u->total_tickets > 0)->count();

        // Rata-rata waktu penyelesaian global
        if (DB::getDriverName() === 'sqlite') {
            $globalAvgSeconds = (clone $ticketQuery)
                ->whereNotNull('tickets.waktu_selesai')
                ->selectRaw('AVG(strftime("%s", tickets.waktu_selesai) - strftime("%s", tickets.created_at)) as avg_sec')
                ->value('avg_sec');
        } else {
            $globalAvgSeconds = (clone $ticketQuery)
                ->whereNotNull('tickets.waktu_selesai')
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, tickets.created_at, tickets.waktu_selesai)) as avg_sec')
                ->value('avg_sec');
        }

        $globalSummary = [
            'total_units' => $units->count(),
            'active_units' => $unitsWithTicketsCount,
            'total_tickets' => $totalTicketsAll,
            'open_tickets' => $totalOpenAll,
            'avg_duration' => $this->formatResolutionTime($globalAvgSeconds),
        ];

        return view('admin.unit_history.index', compact(
            'unitSummaries',
            'globalSummary',
            'periode',
            'tanggalMulai',
            'tanggalSelesai'
        ));
    }

    /**
     * Menampilkan detail riwayat tiket untuk satu unit tertentu:
     * - Kartu ringkasan unit
     * - Grafik distribusi tiket per kategori (Chart.js via npm)
     * - Daftar tiket unit berpaginasi dengan filter status & kategori
     */
    public function show(Unit $unit, Request $request): View
    {
        $periode = $request->input('periode', 'semua');
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
        $status = $request->input('status');
        $categoryId = $request->input('category_id');

        // Query dasar tiket untuk unit ini dengan filter periode
        $baseQuery = $this->buildDateFilteredQuery($periode, $tanggalMulai, $tanggalSelesai)
            ->where('tickets.unit_id', $unit->id);

        // 1. Data ringkasan metrik unit
        $statusCounts = (clone $baseQuery)
            ->select('tickets.status', DB::raw('count(*) as count'))
            ->groupBy('tickets.status')
            ->pluck('count', 'tickets.status')
            ->toArray();

        $totalUnitTickets = (clone $baseQuery)->count();
        $openTickets = ($statusCounts['baru'] ?? 0) + ($statusCounts['diproses'] ?? 0);
        $resolvedTickets = ($statusCounts['selesai'] ?? 0) + ($statusCounts['ditutup'] ?? 0);

        if (DB::getDriverName() === 'sqlite') {
            $avgSeconds = (clone $baseQuery)
                ->whereNotNull('tickets.waktu_selesai')
                ->selectRaw('AVG(strftime("%s", tickets.waktu_selesai) - strftime("%s", tickets.created_at)) as avg_sec')
                ->value('avg_sec');
        } else {
            $avgSeconds = (clone $baseQuery)
                ->whereNotNull('tickets.waktu_selesai')
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, tickets.created_at, tickets.waktu_selesai)) as avg_sec')
                ->value('avg_sec');
        }

        $unitStats = [
            'total' => $totalUnitTickets,
            'open' => $openTickets,
            'baru' => $statusCounts['baru'] ?? 0,
            'diproses' => $statusCounts['diproses'] ?? 0,
            'selesai' => $statusCounts['selesai'] ?? 0,
            'ditutup' => $statusCounts['ditutup'] ?? 0,
            'avg_duration' => $this->formatResolutionTime($avgSeconds),
        ];

        // 2. Data distribusi tiket per kategori unit untuk Chart.js (agregat efisien)
        $categoryChartData = (clone $baseQuery)
            ->join('categories', 'tickets.category_id', '=', 'categories.id')
            ->select('categories.nama', DB::raw('count(tickets.id) as total'))
            ->groupBy('categories.id', 'categories.nama')
            ->orderByDesc('total')
            ->get();

        // 3. Daftar tiket unit dengan filter tambahan (status, kategori) & paginasi
        $tickets = (clone $baseQuery)
            ->when(!empty($status), function ($q) use ($status) {
                $q->where('tickets.status', $status);
            })
            ->when(!empty($categoryId), function ($q) use ($categoryId) {
                $q->where('tickets.category_id', $categoryId);
            })
            ->with(['user', 'category', 'teknisi'])
            ->latest('tickets.created_at')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('nama')->get();

        return view('admin.unit_history.show', compact(
            'unit',
            'unitStats',
            'categoryChartData',
            'tickets',
            'categories',
            'periode',
            'tanggalMulai',
            'tanggalSelesai',
            'status',
            'categoryId'
        ));
    }

    /**
     * Membangun query tiket dasar dengan filter rentang waktu.
     */
    protected function buildDateFilteredQuery(string $periode, ?string $tanggalMulai, ?string $tanggalSelesai)
    {
        $query = Ticket::query();

        if (!empty($tanggalMulai) && !empty($tanggalSelesai)) {
            $query->whereDate('tickets.created_at', '>=', $tanggalMulai)
                  ->whereDate('tickets.created_at', '<=', $tanggalSelesai);
        } elseif (!empty($tanggalMulai)) {
            $query->whereDate('tickets.created_at', '>=', $tanggalMulai);
        } elseif (!empty($tanggalSelesai)) {
            $query->whereDate('tickets.created_at', '<=', $tanggalSelesai);
        } else {
            if ($periode === 'bulan_ini') {
                $query->whereBetween('tickets.created_at', [now()->startOfMonth(), now()->endOfMonth()]);
            } elseif ($periode === '30_hari') {
                $query->where('tickets.created_at', '>=', now()->subDays(30));
            }
        }

        return $query;
    }

    /**
     * Format durasi detik menjadi representasi waktu ramah pengguna dalam bahasa Indonesia.
     */
    protected function formatResolutionTime(?float $seconds): string
    {
        if ($seconds === null || $seconds <= 0) {
            return 'Belum ada data';
        }

        $seconds = (int) round($seconds);
        $days = intdiv($seconds, 86400);
        $hours = intdiv($seconds % 86400, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        $parts = [];
        if ($days > 0) {
            $parts[] = "{$days} hari";
        }
        if ($hours > 0) {
            $parts[] = "{$hours} jam";
        }
        if ($minutes > 0 || empty($parts)) {
            $parts[] = "{$minutes} menit";
        }

        return implode(' ', $parts);
    }
}

