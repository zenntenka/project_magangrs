<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('tickets.index') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition" title="Kembali ke Tiket Saya">
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
                        <span class="text-xs text-gray-500">Diajukan {{ $ticket->created_at->translatedFormat('d F Y, H:i') }}</span>
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Kolom Kiri: Detail Laporan & Lampiran (2 Kolom) -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Kartu Informasi Detail -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 p-6">
                        <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-4">
                            Deskripsi Kendala
                        </h3>
                        <div class="text-sm text-gray-800 leading-relaxed whitespace-pre-line bg-gray-50 p-4 rounded-lg border border-gray-100">
                            {{ $ticket->deskripsi }}
                        </div>

                        <!-- Lampiran Gambar -->
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
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 p-6">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                            <h3 class="text-base font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 me-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Komentar & Riwayat Penanganan
                            </h3>
                            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                                {{ $ticket->comments->count() }} Komentar
                            </span>
                        </div>

                        @if ($ticket->comments->isEmpty())
                            <div class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                                <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                                <p class="text-sm font-medium text-gray-600">Belum ada komentar pada tiket ini</p>
                                <p class="text-xs text-gray-400 mt-0.5">Tim IT akan segera meninjau dan memperbarui progres tiket Anda di sini.</p>
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
                    </div>

                </div>

                <!-- Kolom Kanan: Meta Data & Ringkasan Status (1 Kolom) -->
                <div class="space-y-6">

                    <!-- Kartu Ringkasan Informasi -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 p-6 space-y-4">
                        <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">
                            Informasi Pelapor & Unit
                        </h3>

                        <!-- Pelapor -->
                        <div>
                            <span class="text-xs text-gray-400 uppercase font-semibold">Pelapor</span>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $ticket->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $ticket->user->email }}</p>
                        </div>

                        <!-- Unit Terkait -->
                        <div>
                            <span class="text-xs text-gray-400 uppercase font-semibold">Unit / Ruangan</span>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $ticket->unit->nama ?? '-' }}</p>
                        </div>

                        <!-- Kategori -->
                        <div>
                            <span class="text-xs text-gray-400 uppercase font-semibold">Kategori</span>
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
                                    Menunggu penugasan teknisi
                                </p>
                            @endif
                        </div>

                        <!-- Waktu Pelaporan -->
                        <div class="pt-3 border-t border-gray-100 text-xs text-gray-500 space-y-1">
                            <div class="flex justify-between">
                                <span>Waktu Dibuat:</span>
                                <span class="font-medium text-gray-700">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @if ($ticket->waktu_selesai)
                                <div class="flex justify-between text-emerald-700">
                                    <span>Waktu Selesai:</span>
                                    <span class="font-medium">{{ $ticket->waktu_selesai->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Tombol Navigasi Bawah -->
                    <div>
                        <a href="{{ route('tickets.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg shadow-sm hover:bg-gray-50 transition">
                            &larr; Kembali ke Daftar Tiket
                        </a>
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
