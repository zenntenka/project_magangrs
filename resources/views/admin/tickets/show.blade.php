<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-3">
                    <span class="font-mono text-xl font-bold text-indigo-600">{{ $ticket->kode }}</span>
                    @if ($ticket->prioritas === 'tinggi')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">Prioritas Tinggi</span>
                    @elseif ($ticket->prioritas === 'sedang')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">Prioritas Sedang</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Prioritas Rendah</span>
                    @endif

                    @if ($ticket->status === 'baru')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Baru</span>
                    @elseif ($ticket->status === 'diproses')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">Diproses</span>
                    @elseif ($ticket->status === 'selesai')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Selesai</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Ditutup</span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-1">
                    Detail tiket helpdesk dan kontrol penugasan administrator.
                </p>
            </div>
            <div>
                <a href="{{ route('admin.tickets.index') }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 bg-white border border-gray-300 px-3.5 py-2 rounded-lg shadow-sm hover:bg-gray-50 transition">
                    &larr; Kembali ke Daftar Tiket
                </a>
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Kolom Utama: Isi Tiket & Diskusi Komentar -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Kartu Informasi Kendala -->
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
                        <div>
                            <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Judul Pengaduan</span>
                            <h1 class="text-xl font-bold text-gray-900 mt-1">{{ $ticket->judul }}</h1>
                        </div>

                        <div>
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Deskripsi Kendala</span>
                            <div class="mt-2 text-sm text-gray-800 bg-gray-50 p-4 rounded-lg whitespace-pre-line border border-gray-100">
                                {{ $ticket->deskripsi }}
                            </div>
                        </div>

                        <!-- Lampiran Foto/File -->
                        @if ($ticket->lampiran)
                            <div>
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Lampiran Foto Kendala</span>
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $ticket->lampiran) }}" target="_blank" class="inline-block group">
                                        <img src="{{ asset('storage/' . $ticket->lampiran) }}" alt="Lampiran Kendala"
                                             class="max-h-60 rounded-lg border border-gray-200 shadow-sm group-hover:opacity-90 transition">
                                        <p class="text-xs text-indigo-600 group-hover:underline mt-1 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            Buka gambar ukuran penuh
                                        </p>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Riwayat Aktivitas & Komentar -->
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-5">
                        <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Riwayat Penanganan & Catatan Diskusi ({{ $ticket->comments->count() }})
                        </h3>

                        @if ($ticket->comments->isEmpty())
                            <div class="text-center py-8 text-sm text-gray-400 bg-gray-50 rounded-lg">
                                Belum ada catatan aktivitas pada tiket ini.
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach ($ticket->comments as $comment)
                                    <div class="p-4 rounded-lg border {{ $comment->user->role === 'admin' ? 'bg-purple-50/60 border-purple-200' : ($comment->user->role === 'teknisi' ? 'bg-blue-50/60 border-blue-200' : 'bg-gray-50 border-gray-200') }}">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <div class="flex items-center space-x-2">
                                                <span class="font-semibold text-xs text-gray-900">{{ $comment->user->name }}</span>
                                                <span class="text-[10px] uppercase tracking-wider font-bold px-1.5 py-0.5 rounded {{ $comment->user->role === 'admin' ? 'bg-purple-200 text-purple-800' : ($comment->user->role === 'teknisi' ? 'bg-blue-200 text-blue-800' : 'bg-gray-200 text-gray-700') }}">
                                                    {{ $comment->user->role }}
                                                </span>
                                            </div>
                                            <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="text-sm text-gray-700 whitespace-pre-line">
                                            {{ $comment->isi }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Form Tambah Catatan Admin -->
                        <form action="{{ route('admin.tickets.comments.store', $ticket) }}" method="POST" class="pt-4 border-t border-gray-100 space-y-3">
                            @csrf
                            <label for="isi" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Tambah Catatan Tanggapan Administrator
                            </label>
                            <textarea name="isi" id="isi" rows="3" required placeholder="Tulis instruksi atau informasi tambahan..."
                                      class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            <div class="flex justify-end">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                    Kirim Tanggapan
                                </button>
                            </div>
                        </form>
                    </div>

                </div>

                <!-- Kolom Sidebar Kanan: Kontrol Penugasan & Perubahan Status -->
                <div class="space-y-6">

                    <!-- Panel Penugasan Teknisi -->
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-4">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z" />
                            </svg>
                            Penugasan Teknisi
                        </h3>

                        <div class="p-3 bg-gray-50 rounded-lg text-xs space-y-1">
                            <span class="text-gray-500">Teknisi Penanggung Jawab:</span>
                            <div class="font-bold text-gray-900 text-sm">
                                {{ $ticket->teknisi ? $ticket->teknisi->name : 'Belum Ditugaskan' }}
                            </div>
                            @if ($ticket->teknisi)
                                <span class="text-gray-400 block">{{ $ticket->teknisi->email }}</span>
                            @endif
                        </div>

                        <form action="{{ route('admin.tickets.assign', $ticket) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label for="teknisi_id" class="block text-xs font-semibold text-gray-700 mb-1">
                                    Pilih Teknisi IT
                                </label>
                                <select name="teknisi_id" id="teknisi_id" required
                                        class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Pilih Teknisi --</option>
                                    @foreach ($teknisiList as $tek)
                                        <option value="{{ $tek->id }}" {{ (int) $ticket->teknisi_id === (int) $tek->id ? 'selected' : '' }}>
                                            {{ $tek->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                {{ $ticket->teknisi_id ? 'Ganti Teknisi' : 'Tugaskan ke Teknisi' }}
                            </button>
                        </form>
                    </div>

                    <!-- Panel Ubah Status Tiket -->
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-4">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Ubah Status Tiket
                        </h3>

                        <form action="{{ route('admin.tickets.status', $ticket) }}" method="POST" class="space-y-3">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="status" class="block text-xs font-semibold text-gray-700 mb-1">
                                    Status Baru
                                </label>
                                <select name="status" id="status" required
                                        class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="baru" {{ $ticket->status === 'baru' ? 'selected' : '' }}>Baru</option>
                                    <option value="diproses" {{ $ticket->status === 'diproses' ? 'selected' : '' }}>Diproses</option>
                                    <option value="selesai" {{ $ticket->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="ditutup" {{ $ticket->status === 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                                </select>
                            </div>

                            <div>
                                <label for="catatan" class="block text-xs font-semibold text-gray-700 mb-1">
                                    Catatan Tambahan (Opsional)
                                </label>
                                <textarea name="catatan" id="catatan" rows="2" placeholder="Alasan perubahan status..."
                                          class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            </div>

                            <button type="submit" class="w-full px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                Perbarui Status
                            </button>
                        </form>
                    </div>

                    <!-- Informasi Metadata Tiket -->
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-3 text-xs">
                        <h4 class="font-semibold text-gray-900 uppercase tracking-wider text-[11px]">Informasi Laporan</h4>

                        <div class="flex justify-between py-1.5 border-b border-gray-100">
                            <span class="text-gray-500">Pelapor:</span>
                            <span class="font-medium text-gray-900">{{ $ticket->user->name }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-100">
                            <span class="text-gray-500">Unit RS:</span>
                            <span class="font-medium text-gray-900">{{ $ticket->unit->nama }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-100">
                            <span class="text-gray-500">Kategori:</span>
                            <span class="font-medium text-gray-900">{{ $ticket->category->nama }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-100">
                            <span class="text-gray-500">Dibuat:</span>
                            <span class="font-medium text-gray-900">{{ $ticket->created_at->format('d M Y H:i') }}</span>
                        </div>
                        @if ($ticket->waktu_selesai)
                            <div class="flex justify-between py-1.5 border-b border-gray-100">
                                <span class="text-gray-500">Waktu Selesai:</span>
                                <span class="font-medium text-emerald-700">{{ $ticket->waktu_selesai->format('d M Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between py-1.5">
                                <span class="text-gray-500">Durasi Penyelesaian:</span>
                                <span class="font-semibold text-purple-700">
                                    {{ $ticket->created_at->diffForHumans($ticket->waktu_selesai, true) }}
                                </span>
                            </div>
                        @endif
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>

