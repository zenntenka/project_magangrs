<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * Menampilkan semua tiket di sistem untuk dipantau dan dikelola oleh Admin.
     * Dilengkapi filter status, kategori, unit, prioritas, teknisi, serta pencarian kata kunci.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status');
        $categoryId = $request->input('category_id');
        $unitId = $request->input('unit_id');
        $prioritas = $request->input('prioritas');
        $teknisiId = $request->input('teknisi_id');
        $q = $request->input('q');

        $query = Ticket::with(['user', 'unit', 'category', 'teknisi'])
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($categoryId, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($unitId, function ($query, $unitId) {
                $query->where('unit_id', $unitId);
            })
            ->when($prioritas, function ($query, $prioritas) {
                $query->where('prioritas', $prioritas);
            })
            ->when($teknisiId, function ($query, $teknisiId) {
                if ($teknisiId === 'unassigned') {
                    $query->whereNull('teknisi_id');
                } else {
                    $query->where('teknisi_id', $teknisiId);
                }
            })
            ->when($q, function ($query, $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('kode', 'like', "%{$q}%")
                        ->orWhere('judul', 'like', "%{$q}%")
                        ->orWhere('deskripsi', 'like', "%{$q}%");
                });
            });

        // Urutkan prioritas dan status aktif di atas, lalu tiket terbaru
        $tickets = $query->orderByRaw("CASE status WHEN 'baru' THEN 1 WHEN 'diproses' THEN 2 WHEN 'selesai' THEN 3 WHEN 'ditutup' THEN 4 ELSE 5 END")
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('nama')->get();
        $units = Unit::orderBy('nama')->get();
        $teknisiList = User::where('role', 'teknisi')->orderBy('name')->get();

        return view('admin.tickets.index', compact(
            'tickets',
            'categories',
            'units',
            'teknisiList',
            'status',
            'categoryId',
            'unitId',
            'prioritas',
            'teknisiId',
            'q'
        ));
    }

    /**
     * Menampilkan detail lengkap tiket, riwayat komentar, dan form penugasan teknisi.
     */
    public function show(Ticket $ticket): View
    {
        $ticket->load(['user', 'unit', 'category', 'teknisi', 'comments.user']);
        $teknisiList = User::where('role', 'teknisi')->orderBy('name')->get();

        return view('admin.tickets.show', compact('ticket', 'teknisiList'));
    }

    /**
     * Menugaskan (assign) tiket kepada teknisi tertentu lewat dropdown.
     */
    public function assign(Request $request, Ticket $ticket): RedirectResponse
    {
        $request->validate([
            'teknisi_id' => ['required', 'exists:users,id'],
        ], [
            'teknisi_id.required' => 'Pilih teknisi yang akan ditugaskan.',
            'teknisi_id.exists' => 'Teknisi yang dipilih tidak valid.',
        ]);

        $teknisi = User::where('role', 'teknisi')->findOrFail($request->teknisi_id);

        DB::transaction(function () use ($ticket, $teknisi, $request) {
            $isStatusChange = false;
            $oldStatus = $ticket->status;

            // Jika status tiket masih 'baru', ubah otomatis ke 'diproses'
            if ($ticket->status === 'baru') {
                $ticket->status = 'diproses';
                $isStatusChange = true;
            }

            $ticket->teknisi_id = $teknisi->id;
            $ticket->save();

            // Rekam jejak di komentar tiket
            $catatan = "Tiket ditugaskan kepada teknisi {$teknisi->name} oleh Administrator ({$request->user()->name}).";
            if ($isStatusChange) {
                $catatan .= " Status tiket diperbarui dari \"{$oldStatus}\" menjadi \"diproses\".";
            }

            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $request->user()->id,
                'isi' => $catatan,
            ]);
        });

        return back()->with('success', "Tiket {$ticket->kode} berhasil ditugaskan kepada {$teknisi->name}.");
    }

    /**
     * Mengubah status tiket oleh Admin (diproses, selesai, ditutup).
     */
    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        $request->validate([
            'status' => ['required', Rule::in(['baru', 'diproses', 'selesai', 'ditutup'])],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldStatus = $ticket->status;
        $newStatus = $request->status;

        DB::transaction(function () use ($ticket, $newStatus, $oldStatus, $request) {
            $ticket->status = $newStatus;

            // Jika status selesai, catat waktu penyelesaian
            if ($newStatus === 'selesai' && !$ticket->waktu_selesai) {
                $ticket->waktu_selesai = now();
            } elseif ($newStatus === 'baru' || $newStatus === 'diproses') {
                $ticket->waktu_selesai = null;
            }

            $ticket->save();

            $isiKomentar = "Status tiket diperbarui dari \"{$oldStatus}\" menjadi \"{$newStatus}\" oleh Admin ({$request->user()->name}).";
            if (!empty($request->catatan)) {
                $isiKomentar .= "\n\nCatatan: " . $request->catatan;
            }

            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $request->user()->id,
                'isi' => $isiKomentar,
            ]);
        });

        return back()->with('success', "Status tiket {$ticket->kode} berhasil diperbarui menjadi \"{$newStatus}\".");
    }

    /**
     * Menambahkan komentar / respon pada tiket oleh admin.
     */
    public function storeComment(Request $request, Ticket $ticket): RedirectResponse
    {
        $request->validate([
            'isi' => ['required', 'string', 'max:2000'],
        ], [
            'isi.required' => 'Pesan catatan tanggapan tidak boleh kosong.',
        ]);

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'isi' => $request->isi,
        ]);

        return back()->with('success', 'Catatan admin berhasil ditambahkan.');
    }
}

