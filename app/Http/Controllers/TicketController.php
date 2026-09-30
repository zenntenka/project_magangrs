<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * Menampilkan daftar tiket milik pelapor yang sedang login.
     * Tiket diurutkan dari yang terbaru dengan paginasi 10 data.
     */
    public function index(Request $request): View
    {
        $tickets = Ticket::with(['category', 'unit', 'teknisi'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('tickets.index', compact('tickets'));
    }

    /**
     * Menampilkan formulir pembuatan tiket pengaduan IT baru.
     */
    public function create(Request $request): View
    {
        $units = Unit::orderBy('nama')->get();
        $categories = Category::orderBy('nama')->get();
        $defaultUnitId = $request->user()->unit_id;

        return view('tickets.create', compact('units', 'categories', 'defaultUnitId'));
    }

    /**
     * Menyimpan tiket baru dengan kode otomatis TCK-YYYYMMDD-XXXX.
     * Menggunakan DB::transaction dan lockForUpdate untuk menjaga keunikan nomor urut harian.
     */
    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $ticket = DB::transaction(function () use ($request) {
            $prefix = 'TCK-' . now()->format('Ymd') . '-';

            // Kunci baris tiket terakhir pada hari ini untuk mencegah race condition nomor urut
            $lastTicket = Ticket::where('kode', 'like', $prefix . '%')
                ->orderByDesc('kode')
                ->lockForUpdate()
                ->first();

            if ($lastTicket) {
                $lastNumber = (int) substr($lastTicket->kode, -4);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            $kode = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Simpan file lampiran jika ada ke storage/app/public/attachments
            $attachmentPath = null;
            if ($request->hasFile('lampiran')) {
                $attachmentPath = $request->file('lampiran')->store('attachments', 'public');
            }

            return Ticket::create([
                'kode' => $kode,
                'user_id' => $request->user()->id,
                'unit_id' => $request->validated('unit_id'),
                'category_id' => $request->validated('category_id'),
                'judul' => $request->validated('judul'),
                'deskripsi' => $request->validated('deskripsi'),
                'prioritas' => $request->validated('prioritas'),
                'status' => 'baru',
                'lampiran' => $attachmentPath,
                'waktu_selesai' => null,
            ]);
        });

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Tiket baru berhasil dibuat dengan nomor referensi {$ticket->kode}.");
    }

    /**
     * Menampilkan detail tiket beserta daftar komentar/progres penanganan.
     * Diproteksi oleh TicketPolicy agar pelapor tidak dapat melihat tiket milik pengguna lain (403).
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

        return view('tickets.show', compact('ticket'));
    }
}
