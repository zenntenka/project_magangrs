<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TicketsReportExport;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\Unit;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Halaman laporan tiket helpdesk IT:
     * Menampilkan formulir filter, kartu ringkasan kinerja, dan tabel data tiket berpaginasi.
     */
    public function index(Request $request): View
    {
        $filteredQuery = $this->getFilteredQuery($request);

        // Ringkasan statistik dihitung dari query yang telah difilter
        $summary = $this->getSummaryStats($filteredQuery);

        // Data tiket untuk tabel berpaginasi (10 per halaman dengan mempertahankan query string)
        $tickets = (clone $filteredQuery)
            ->with(['user', 'unit', 'category', 'teknisi'])
            ->orderByRaw("CASE status WHEN 'baru' THEN 1 WHEN 'diproses' THEN 2 WHEN 'selesai' THEN 3 WHEN 'ditutup' THEN 4 ELSE 5 END")
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $units = Unit::orderBy('nama')->get();
        $categories = Category::orderBy('nama')->get();
        $teknisiList = User::where('role', 'teknisi')->orderBy('name')->get();

        return view('admin.reports.index', compact(
            'tickets',
            'summary',
            'units',
            'categories',
            'teknisiList'
        ));
    }

    /**
     * Ekspor hasil filter laporan tiket ke dokumen PDF format A4.
     */
    public function exportPdf(Request $request)
    {
        $filteredQuery = $this->getFilteredQuery($request);
        $summary = $this->getSummaryStats($filteredQuery);

        $tickets = (clone $filteredQuery)
            ->with(['user', 'unit', 'category', 'teknisi'])
            ->latest()
            ->get();

        $activeFilters = $this->getActiveFilterLabels($request);

        $pdf = Pdf::loadView('admin.reports.pdf', compact('tickets', 'summary', 'activeFilters'))
            ->setPaper('a4', 'landscape');

        $filename = 'Laporan-Tiket-IT-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Ekspor hasil filter laporan tiket ke file Excel (.xlsx).
     * Jika terjadi kendala pada environment, tersedia fallback ke format CSV.
     */
    public function exportExcel(Request $request)
    {
        $filteredQuery = $this->getFilteredQuery($request);

        $tickets = (clone $filteredQuery)
            ->with(['user', 'unit', 'category', 'teknisi'])
            ->latest()
            ->get();

        $filename = 'Laporan-Tiket-IT-' . now()->format('Ymd-His') . '.xlsx';

        try {
            return Excel::download(new TicketsReportExport($tickets), $filename);
        } catch (\Throwable $e) {
            // Fallback ke CSV bawaan PHP jika ada masalah library Excel
            return $this->exportCsvFallback($tickets);
        }
    }

    /**
     * Membangun query dasar tiket terfilter dengan memanfaatkan scope model.
     * Mencegah duplikasi kode query filter antara web, PDF, dan Excel.
     */
    protected function getFilteredQuery(Request $request)
    {
        return Ticket::query()->filterReport($request->only([
            'tanggal_mulai',
            'tanggal_selesai',
            'unit_id',
            'category_id',
            'status',
            'teknisi_id',
        ]));
    }

    /**
     * Menghitung metrik ringkasan agregat dari query tiket yang aktif.
     */
    protected function getSummaryStats($query): array
    {
        $statusCounts = (clone $query)
            ->select('tickets.status', DB::raw('count(*) as count'))
            ->groupBy('tickets.status')
            ->pluck('count', 'tickets.status')
            ->toArray();

        $total = (clone $query)->count();

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

        return [
            'total' => $total,
            'baru' => $statusCounts['baru'] ?? 0,
            'diproses' => $statusCounts['diproses'] ?? 0,
            'selesai' => $statusCounts['selesai'] ?? 0,
            'ditutup' => $statusCounts['ditutup'] ?? 0,
            'avg_duration' => $this->formatResolutionTime($avgSeconds),
        ];
    }

    /**
     * Mengambil label teks filter yang sedang aktif untuk dicetak pada kop laporan.
     */
    protected function getActiveFilterLabels(Request $request): array
    {
        $unit = $request->filled('unit_id') ? Unit::find($request->unit_id)?->nama : 'Semua Unit';
        $category = $request->filled('category_id') ? Category::find($request->category_id)?->nama : 'Semua Kategori';
        $status = $request->filled('status') ? ucfirst($request->status) : 'Semua Status';

        $teknisi = 'Semua Teknisi';
        if ($request->teknisi_id === 'unassigned') {
            $teknisi = 'Belum Ditugaskan';
        } elseif ($request->filled('teknisi_id')) {
            $teknisi = User::find($request->teknisi_id)?->name ?? 'Semua Teknisi';
        }

        $periode = 'Semua Tanggal';
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $periode = date('d/m/Y', strtotime($request->tanggal_mulai)) . ' s/d ' . date('d/m/Y', strtotime($request->tanggal_selesai));
        } elseif ($request->filled('tanggal_mulai')) {
            $periode = 'Mulai ' . date('d/m/Y', strtotime($request->tanggal_mulai));
        } elseif ($request->filled('tanggal_selesai')) {
            $periode = 'Sampai ' . date('d/m/Y', strtotime($request->tanggal_selesai));
        }

        return [
            'periode' => $periode,
            'unit' => $unit,
            'category' => $category,
            'status' => $status,
            'teknisi' => $teknisi,
        ];
    }

    /**
     * Fallback ekspor CSV murni menggunakan stream PHP.
     */
    protected function exportCsvFallback($tickets): StreamedResponse
    {
        $filename = 'Laporan-Tiket-IT-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($tickets) {
            $handle = fopen('php://output', 'w');
            // Menulis UTF-8 BOM agar terbaca rapi di Microsoft Excel
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No',
                'Kode Tiket',
                'Tanggal Lapor',
                'Nama Pelapor',
                'Unit RS',
                'Kategori Kendala',
                'Judul Kendala',
                'Prioritas',
                'Status',
                'Teknisi Penanggung Jawab',
                'Waktu Selesai',
                'Durasi Penyelesaian',
            ]);

            $no = 1;
            foreach ($tickets as $ticket) {
                fputcsv($handle, [
                    $no++,
                    $ticket->kode,
                    $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : '-',
                    $ticket->user?->name ?? '-',
                    $ticket->unit?->nama ?? '-',
                    $ticket->category?->nama ?? '-',
                    $ticket->judul,
                    ucfirst($ticket->prioritas),
                    ucfirst($ticket->status),
                    $ticket->teknisi?->name ?? 'Belum Ditugaskan',
                    $ticket->waktu_selesai ? $ticket->waktu_selesai->format('d/m/Y H:i') : '-',
                    $ticket->durasi_penyelesaian,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Format detik menjadi deskripsi waktu ramah pengguna.
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

