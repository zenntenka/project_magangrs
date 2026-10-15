<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('teknisi.tickets.index') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition" title="Kembali ke Kelola Tiket IT">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-sm font-bold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded border border-indigo-200">
                            {{ $ticket->kode }}
                        </span>
                        <span class="text-xs text-gray-400">&bull;</span>
                        <span class="text-xs text-gray-500">Masuk {{ $ticket->created_at->translatedFormat('d F Y, H:i') }}</span>
                    </div>
                    <h2 class="font-semibold text-xl text-gray-900 mt-1 leading-tight">
                        {{ $ticket->judul }}
                    </h2>
                </div>
            </div>

            <!-- Badges Status & Prioritas -->
            <div class="flex items-center gap-2">
                <!-- Prioritas Badge -->
                @if ($ticket->prioritas === 'tinggi')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                        <span class="w-1.5 h-1.5 me-1.5 rounded-full bg-red-600"></span>
                        Prioritas Tinggi
                    </span>
                @elseif ($ticket->prioritas === 'sedang')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                        <span class="w-1.5 h-1.5 me-1.5 rounded-full bg-amber-500"></span>
                        Prioritas Sedang
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                        <span class="w-1.5 h-1.5 me-1.5 rounded-full bg-slate-400"></span>
                        Prioritas Rendah
                    </span>
                @endif

                <!-- Status Badge -->
                @if ($ticket->status === 'baru')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                        Baru
                    </span>
                @elseif ($ticket->status === 'diproses')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                        Diproses
                    </span>
                @elseif ($ticket->status === 'selesai')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Selesai
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-300">
                        Ditutup
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg shadow-sm flex items-start space-x-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm text-emerald-800 font-medium">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm flex items-start space-x-3">
                    <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm text-red-800 font-medium">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <!-- Banner Tindakan Jika Tiket Masih "Baru" -->
            @if ($ticket->status === 'baru' && $ticket->teknisi_id === null)
                <div class="p-5 bg-blue-50 rounded-xl border border-blue-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-sm">
                    <div class="flex items-start space-x-3">
                        <div class="p-2 bg-blue-100 text-blue-700 rounded-lg shrink-0 mt-0.5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-base font-semibold text-blue-900">Tiket Ini Belum Ada Teknisi Penanggung Jawab</h4>
                            <p class="text-sm text-blue-700 mt-0.5">
                                Klik tombol di samping untuk mengambil tiket ini. Status otomatis berubah menjadi "Diproses" dan tiket tercatat sebagai tanggung jawab Anda.
                            </p>
                        </div>
                    </div>
                    <form action="{{ route('teknisi.tickets.take', $ticket) }}" method="POST" class="shrink-0">
                        @csrf
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                            <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Ambil Tiket Ini
                        </button>
                    </form>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Kolom Kiri: Deskripsi, Lampiran, & Utas Komentar (2 Kolom) -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Kartu Informasi Detail -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 p-6">
                        <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-4">
                            Deskripsi Kendala dari Pelapor
                        </h3>
                        <div class="text-sm text-gray-800 leading-relaxed whitespace-pre-line bg-gray-50 p-4 rounded-lg border border-gray-100">
                            {{ $ticket->deskripsi }}
                        </div>

                        <!-- Lampiran Foto -->
                        @if ($ticket->lampiran)
                            <div class="mt-6 pt-5 border-t border-gray-100">
                                <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                                    <svg class="w-4 h-4 me-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Lampiran Foto Kendala
                                </h4>
                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 inline-block">
                                    <a href="{{ asset('storage/' . $ticket->lampiran) }}" target="_blank" class="block group relative overflow-hidden rounded-md">
                                        <img src="{{ asset('storage/' . $ticket->lampiran) }}" alt="Lampiran {{ $ticket->kode }}" class="max-h-72 w-auto object-contain rounded-md transition duration-200 group-hover:opacity-90">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition duration-200 flex items-center justify-center">
                                            <span class="opacity-0 group-hover:opacity-100 bg-white text-gray-800 text-xs font-semibold px-2.5 py-1 rounded shadow">
                                                Klik untuk perbesar
                                            </span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Kartu Riwayat Komentar & Tindak Lanjut -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 p-6 space-y-6">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <h3 class="text-base font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 me-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Riwayat Diskusi & Catatan Penanganan
                            </h3>
                            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-0.5 rounded-full">
                                {{ $ticket->comments->count() }} Catatan
                            </span>
                        </div>

                        <!-- Daftar Komentar -->
                        @if ($ticket->comments->isEmpty())
                            <div class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                                <p class="text-sm font-medium text-gray-600">Belum ada catatan penanganan pada tiket ini.</p>
                                <p class="text-xs text-gray-400 mt-0.5">Gunakan formulir di bawah untuk mencatat progres atau konfirmasi kendala.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach ($ticket->comments as $comment)
                                    <div class="p-4 rounded-lg border {{ $comment->user->role === 'teknisi' ? 'bg-indigo-50/50 border-indigo-100' : ($comment->user->role === 'admin' ? 'bg-amber-50/50 border-amber-100' : 'bg-gray-50 border-gray-200') }}">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-sm font-semibold text-gray-900">
                                                    {{ $comment->user->name }}
                                                </span>
                                                @if ($comment->user->role === 'admin')
                                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-amber-100 text-amber-800">Admin IT</span>
                                                @elseif ($comment->user->role === 'teknisi')
                                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-indigo-100 text-indigo-800">Teknisi IT</span>
                                                @else
                                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-gray-200 text-gray-700">Pelapor</span>
                                                @endif
                                            </div>
                                            <span class="text-xs text-gray-400">
                                                {{ $comment->created_at->diffForHumans() }} ({{ $comment->created_at->format('d/m/Y H:i') }})
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-800 whitespace-pre-line leading-relaxed">
                                            {{ $comment->isi }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Form Tambah Komentar oleh Teknisi -->
                        <div class="pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-800 mb-2">Tambah Catatan / Respon Teknisi</h4>
                            <form action="{{ route('teknisi.tickets.comments.store', $ticket) }}" method="POST" class="space-y-3">
                                @csrf
                                <textarea name="isi" rows="3" placeholder="Tuliskan catatan teknis, komponen yang diganti, atau konfirmasi hasil perbaikan kepada pelapor..." class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('isi') border-red-300 @enderror" required>{{ old('isi') }}</textarea>
                                @error('isi')
                                    <p class="text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                                <div class="flex justify-end">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                        <svg class="w-3.5 h-3.5 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                        Kirim Catatan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

                <!-- Kolom Kanan: Panel Kontrol Status & Meta Data (1 Kolom) -->
                <div class="space-y-6">

                    <!-- Panel Ubah Status (Khusus Teknisi Penanggung Jawab / Admin) -->
                    @if ($ticket->teknisi_id === Auth::id() || Auth::user()->role === 'admin')
                        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-indigo-200 p-6 space-y-4">
                            <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 flex items-center">
                                <svg class="w-5 h-5 me-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Kelola Status Tiket
                            </h3>

                            <form action="{{ route('teknisi.tickets.status', $ticket) }}" method="POST" class="space-y-4">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <label for="status" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                        Perbarui Status Ke:
                                    </label>
                                    <select name="status" id="status" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        <option value="diproses" {{ $ticket->status === 'diproses' ? 'selected' : '' }}>
                                            Sedang Diproses (Pengerjaan)
                                        </option>
                                        <option value="selesai" {{ $ticket->status === 'selesai' ? 'selected' : '' }}>
                                            Selesai (Perbaikan Tuntas)
                                        </option>
                                        <option value="ditutup" {{ $ticket->status === 'ditutup' ? 'selected' : '' }}>
                                            Ditutup (Tutup Tiket)
                                        </option>
                                    </select>
                                    <p class="text-[11px] text-gray-500 mt-1">
                                        * Memilih status <strong>Selesai</strong> atau <strong>Ditutup</strong> otomatis mencatat waktu selesai saat ini.
                                    </p>
                                </div>

                                <div>
                                    <label for="catatan" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                        Catatan Perubahan (Opsional):
                                    </label>
                                    <textarea name="catatan" id="catatan" rows="2" placeholder="Contoh: Kabel LAN telah diganti, koneksi internet normal kembali." class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>

                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                                    Simpan Perubahan Status
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- Kartu Informasi Pelapor & Perangkat -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 p-6 space-y-4">
                        <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">
                            Informasi Tiket
                        </h3>

                        <!-- Pelapor -->
                        <div>
                            <span class="text-xs text-gray-400 uppercase font-semibold">Pelapor</span>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $ticket->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $ticket->user->email }}</p>
                        </div>

                        <!-- Unit -->
                        <div>
                            <span class="text-xs text-gray-400 uppercase font-semibold">Unit / Lokasi</span>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $ticket->unit->nama ?? '-' }}</p>
                        </div>

                        <!-- Kategori -->
                        <div>
                            <span class="text-xs text-gray-400 uppercase font-semibold">Kategori Masalah</span>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $ticket->category->nama ?? '-' }}</p>
                        </div>

                        <!-- Teknisi Penanggung Jawab -->
                        <div class="pt-3 border-t border-gray-100">
                            <span class="text-xs text-gray-400 uppercase font-semibold">Teknisi Penanggung Jawab</span>
                            @if ($ticket->teknisi)
                                <div class="mt-1 flex items-center space-x-2">
                                    <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">
                                        {{ substr($ticket->teknisi->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $ticket->teknisi->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $ticket->teknisi->email }}</p>
                                    </div>
                                </div>
                            @else
                                <p class="text-xs text-amber-600 bg-amber-50 px-2.5 py-1.5 rounded-md mt-1 font-medium inline-block">
                                    Belum ada penugasan teknisi
                                </p>
                            @endif
                        </div>

                        <!-- Linimasa Waktu -->
                        <div class="pt-3 border-t border-gray-100 text-xs text-gray-500 space-y-1">
                            <div class="flex justify-between">
                                <span>Dibuat:</span>
                                <span class="font-medium text-gray-700">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @if ($ticket->waktu_selesai)
                                <div class="flex justify-between text-emerald-700">
                                    <span>Diselesaikan:</span>
                                    <span class="font-medium">{{ $ticket->waktu_selesai->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Tombol Kembali -->
                    <div>
                        <a href="{{ route('teknisi.tickets.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg shadow-sm hover:bg-gray-50 transition">
                            &larr; Kembali ke Kelola Tiket IT
                        </a>
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>