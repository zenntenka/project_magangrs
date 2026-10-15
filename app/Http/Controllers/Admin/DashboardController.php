<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama administrator:
     * - Kartu ringkasan jumlah tiket berdasarkan status
     * - Rata-rata durasi penyelesaian tiket
     * - Grafik tiket per kategori dan per unit (agregat efisien)
     * - 5 tiket laporan terbaru
     * - Filter periode (bulan ini, 30 hari terakhir, semua)
     */
    public function index(Request $request): View
    {
        $periode = $request->input('periode', 'bulan_ini');

        $query = Ticket::query();

        // Terapkan filter rentang waktu
        if ($periode === 'bulan_ini') {
            $query->whereBetween('tickets.created_at', [now()->startOfMonth(), now()->endOfMonth()]);
        } elseif ($periode === '30_hari') {
            $query->where('tickets.created_at', '>=', now()->subDays(30));
        }

        // 1. Kartu Ringkasan: Hitung status agregat tanpa looping di Blade
        $statusCounts = (clone $query)
            ->select('tickets.status', DB::raw('count(*) as count'))
            ->groupBy('tickets.status')
            ->pluck('count', 'tickets.status')
            ->toArray();

        $stats = [
            'total' => (clone $query)->count(),
            'baru' => $statusCounts['baru'] ?? 0,
            'diproses' => $statusCounts['diproses'] ?? 0,
            'selesai' => $statusCounts['selesai'] ?? 0,
            'ditutup' => $statusCounts['ditutup'] ?? 0,
        ];

        // 2. Rata-rata waktu penyelesaian tiket (selisih created_at dan waktu_selesai)
        // Dihitung efisien langsung di level query database (kompatibel MySQL dan SQLite)
        if (DB::getDriverName() === 'sqlite') {
            $avgSeconds = (clone $query)
                ->whereNotNull('tickets.waktu_selesai')
                ->selectRaw('AVG(strftime("%s", tickets.waktu_selesai) - strftime("%s", tickets.created_at)) as avg_sec')
                ->value('avg_sec');
        } else {
            $avgSeconds = (clone $query)
                ->whereNotNull('tickets.waktu_selesai')
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, tickets.created_at, tickets.waktu_selesai)) as avg_sec')
                ->value('avg_sec');
        }

        $avgFormatted = $this->formatResolutionTime($avgSeconds);

        // 3. Agregat tiket per kategori (untuk Chart.js)
        $categoryData = (clone $query)
            ->join('categories', 'tickets.category_id', '=', 'categories.id')
            ->select('categories.nama', DB::raw('count(tickets.id) as total'))
            ->groupBy('categories.id', 'categories.nama')
            ->orderByDesc('total')
            ->get();

        // 4. Agregat tiket per unit rumah sakit (untuk Chart.js)
        $unitData = (clone $query)
            ->join('units', 'tickets.unit_id', '=', 'units.id')
            ->select('units.nama', DB::raw('count(tickets.id) as total'))
            ->groupBy('units.id', 'units.nama')
            ->orderByDesc('total')
            ->get();

        // 5. 5 Tiket terbaru pada periode terpilih
        $latestTickets = (clone $query)
            ->with(['user', 'unit', 'category', 'teknisi'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'avgFormatted',
            'categoryData',
            'unitData',
            'latestTickets',
            'periode'
        ));
    }

    /**
     * Format detik menjadi representasi waktu yang ramah pengguna.
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

