<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TeknisiController extends Controller
{
    /**
     * Dashboard ringkas untuk teknisi:
     * Menampilkan statistik jumlah tiket per status yang ditugaskan ke teknisi login,
     * jumlah tiket baru yang siap diambil, serta tiket yang memerlukan respon cepat.
     */
    public function dashboard(Request $request): View
    {
        $user = $request->user();

        // Statistik tiket yang ditugaskan kepada teknisi login
        $stats = [
            'diproses' => Ticket::where('teknisi_id', $user->id)->where('status', 'diproses')->count(),
            'selesai' => Ticket::where('teknisi_id', $user->id)->where('status', 'selesai')->count(),
            'ditutup' => Ticket::where('teknisi_id', $user->id)->where('status', 'ditutup')->count(),
            'total_ditangani' => Ticket::where('teknisi_id', $user->id)->count(),
            'tiket_baru' => Ticket::where('status', 'baru')->whereNull('teknisi_id')->count(),
        ];

        // 5 Tiket aktif yang sedang ditangani teknisi ini
        $myActiveTickets = Ticket::with(['user', 'unit', 'category'])
            ->where('teknisi_id', $user->id)
            ->where('status', 'diproses')
            ->orderByRaw("CASE prioritas WHEN 'tinggi' THEN 1 WHEN 'sedang' THEN 2 WHEN 'rendah' THEN 3 ELSE 4 END")
            ->latest()
            ->take(5)
            ->get();

        // 5 Tiket baru yang belum diambil
        $incomingTickets = Ticket::with(['user', 'unit', 'category'])
            ->where('status', 'baru')
            ->whereNull('teknisi_id')
            ->orderByRaw("CASE prioritas WHEN 'tinggi' THEN 1 WHEN 'sedang' THEN 2 WHEN 'rendah' THEN 3 ELSE 4 END")
            ->latest()
            ->take(5)
            ->get();

        return view('teknisi.dashboard', compact('stats', 'myActiveTickets', 'incomingTickets'));
    }

    /**
     * Halaman daftar tiket untuk teknisi:
     * Menampilkan semua tiket berstatus "baru" (belum ditugaskan)
     * DAN tiket yang sedang ditangani oleh teknisi yang sedang login.
     * Dilengkapi filter berdasarkan status dan kategori.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Ticket::with(['user', 'unit', 'category', 'teknisi'])
            // Kondisi dasar: tiket berstatus "baru" (belum ditugaskan) ATAU tiket yang ditugaskan kepada teknisi login
            ->where(function ($q) use ($user) {
                $q->where(function ($subQ) {
                    $subQ->where('status', 'baru')
                         ->whereNull('teknisi_id');
                })->orWhere('teknisi_id', $user->id);
            })
            // Filter status
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            // Filter kategori
            ->when($request->filled('category_id'), function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            })
            // Filter prioritas
            ->when($request->filled('prioritas'), function ($q) use ($request) {
                $q->where('prioritas', $request->prioritas);
            });

        // Urutkan: prioritas tinggi dan status aktif diutamakan, lalu terbaru
        $tickets = $query->orderByRaw("CASE status WHEN 'baru' THEN 1 WHEN 'diproses' THEN 2 WHEN 'selesai' THEN 3 WHEN 'ditutup' THEN 4 ELSE 5 END")
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('nama')->get();

        return view('teknisi.tickets.index', compact('tickets', 'categories'));
    }

    /**
     * Detail tiket panel teknisi:
     * Menampilkan informasi lengkap tiket, riwayat komentar, kontrol status, dan form komentar.
     */
    public function show(Ticket $ticket): View
    {
        Gate::authorize('view', $ticket);

        $ticket->load([
            'user.unit',
            'unit',
            'category',
            'teknisi',
            'comments.user',
        ]);

        return view('teknisi.tickets.show', compact('ticket'));
    }

    /**
     * Aksi "Ambil Tiket" untuk tiket berstatus "baru":
     * Menetapkan teknisi_id ke teknisi yang login dan mengubah status tiket menjadi "diproses".
     * Menggunakan DB::transaction dan lockForUpdate untuk mencegah pengambilan ganda serentak.
     */
    public function take(Request $request, Ticket $ticket): RedirectResponse
    {
        Gate::authorize('take', $ticket);

        try {
            DB::transaction(function () use ($ticket, $request) {
                $lockedTicket = Ticket::where('id', $ticket->id)->lockForUpdate()->firstOrFail();

                if ($lockedTicket->status !== 'baru' || $lockedTicket->teknisi_id !== null) {
                    throw new \Exception('Tiket ini sudah diambil alih oleh teknisi lain atau statusnya sudah berubah.');
                }

                $lockedTicket->update([
                    'teknisi_id' => $request->user()->id,
                    'status' => 'diproses',
                ]);

                // Buat log komentar otomatis
                $lockedTicket->comments()->create([
                    'user_id' => $request->user()->id,
                    'isi' => 'Tiket telah diambil alih oleh Teknisi ' . $request->user()->name . ' dan status diubah menjadi "Diproses".',
                ]);
            });

            return redirect()->route('teknisi.tickets.show', $ticket)
                ->with('success', "Tiket {$ticket->kode} berhasil diambil alih dan status diubah menjadi 'Diproses'.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Memperbarui status tiket oleh teknisi (diproses, selesai, ditutup):
     * Otomatis mengisi kolom waktu_selesai saat status diubah menjadi "selesai" atau "ditutup".
     */
    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        Gate::authorize('update', $ticket);

        $validated = $request->validate([
            'status' => ['required', 'in:diproses,selesai,ditutup'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ], [
            'status.required' => 'Status tiket wajib dipilih.',
            'status.in' => 'Pilihan status tidak valid.',
        ]);

        $statusLama = $ticket->status;
        $statusBaru = $validated['status'];

        // Menentukan waktu_selesai
        $waktuSelesai = $ticket->waktu_selesai;
        if ($statusBaru === 'selesai' || $statusBaru === 'ditutup') {
            $waktuSelesai = $waktuSelesai ?? now();
        } elseif ($statusBaru === 'diproses') {
            $waktuSelesai = null;
        }

        $ticket->update([
            'status' => $statusBaru,
            'waktu_selesai' => $waktuSelesai,
        ]);

        // Catat riwayat perubahan status sebagai komentar
        $catatanTambahan = ! empty($validated['catatan']) ? "\nCatatan: " . $validated['catatan'] : '';
        $ticket->comments()->create([
            'user_id' => $request->user()->id,
            'isi' => "Status tiket diperbarui dari '{$statusLama}' menjadi '{$statusBaru}' oleh Teknisi {$request->user()->name}.{$catatanTambahan}",
        ]);

        return redirect()->route('teknisi.tickets.show', $ticket)
            ->with('success', "Status tiket {$ticket->kode} berhasil diperbarui menjadi '{$statusBaru}'.");
    }

    /**
     * Menambahkan komentar / catatan tindak lanjut oleh teknisi.
     */
    public function storeComment(Request $request, Ticket $ticket): RedirectResponse
    {
        Gate::authorize('comment', $ticket);

        $validated = $request->validate([
            'isi' => ['required', 'string', 'min:2', 'max:3000'],
        ], [
            'isi.required' => 'Komentar tidak boleh kosong.',
            'isi.min' => 'Komentar minimal 2 karakter.',
        ]);

        $ticket->comments()->create([
            'user_id' => $request->user()->id,
            'isi' => $validated['isi'],
        ]);

        return redirect()->route('teknisi.tickets.show', $ticket)
            ->with('success', 'Komentar berhasil ditambahkan ke tiket.');
    }
}