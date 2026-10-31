<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama untuk pengguna.
     * Jika role admin atau teknisi, alihkan langsung ke panel dashboard perannya.
     * Jika role pelapor, tampilkan ringkasan tiket dan panduan layanan Helpdesk IT.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // 1. Logika redirect untuk admin dan teknisi (dipertahankan sesuai aturan)
        if ($user && $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user && $user->role === 'teknisi') {
            return redirect()->route('teknisi.dashboard');
        }

        // 2. Muat relasi unit user jika belum di-load untuk ditampilkan di sapaan header
        $user->loadMissing('unit');

        // 3. Hitung jumlah tiket per status hanya milik user yang login
        // Query efisien: satu kali query menggunakan groupBy status
        $statusCounts = Ticket::where('user_id', $user->id)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'total' => (int) $statusCounts->sum(),
            'baru' => (int) ($statusCounts['baru'] ?? 0),
            'diproses' => (int) ($statusCounts['diproses'] ?? 0),
            'selesai' => (int) (($statusCounts['selesai'] ?? 0) + ($statusCounts['ditutup'] ?? 0)),
        ];

        // 4. 5 tiket terbaru milik user yang login dengan eager loading category dan teknisi
        $recentTickets = Ticket::where('user_id', $user->id)
            ->with(['category', 'teknisi'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('user', 'stats', 'recentTickets'));
    }
}

