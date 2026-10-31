<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    Halo, {{ $user->name }}
                </h2>
                <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500 mt-1">
                    <span class="inline-flex items-center text-brand-800 font-semibold bg-brand-50 border border-brand-200 px-2.5 py-0.5 rounded-md text-xs">
                        <svg class="w-3.5 h-3.5 me-1 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Unit: {{ $user->unit?->nama ?? 'Umum / Belum Diatur' }}
                    </span>
                    <span class="text-gray-300">•</span>
                    <span class="text-xs sm:text-sm text-gray-600">Helpdesk IT RS Aisyiyah Kudus</span>
                </div>
            </div>
            <div>
                <a href="{{ route('tickets.create') }}" class="inline-flex items-center justify-center px-5 py-3 bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-semibold rounded-xl shadow-sm hover:shadow transition duration-150 ease-in-out gap-2 text-sm w-full sm:w-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Tiket Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl shadow-sm flex items-start space-x-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm text-emerald-800 font-medium">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <!-- 4 Kartu Ringkasan Status Tiket (2 kolom di HP, 4 kolom di layar lebar) -->
            <div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    <!-- Kartu 1: Total Tiket -->
                    <div class="bg-white p-4 sm:p-5 rounded-xl border border-brand-200 shadow-sm flex flex-col justify-between hover:border-brand-300 transition">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-brand-800 uppercase tracking-wider">Total Tiket</span>
                            <div class="w-9 h-9 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="text-2xl sm:text-3xl font-extrabold text-brand-900">{{ number_format($stats['total']) }}</div>
                            <p class="text-[11px] sm:text-xs text-gray-500 mt-1">Semua laporan diajukan</p>
                        </div>
                    </div>

                    <!-- Kartu 2: Menunggu (baru) -->
                    <div class="bg-white p-4 sm:p-5 rounded-xl border border-blue-200 shadow-sm flex flex-col justify-between hover:border-blue-300 transition">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">Menunggu</span>
                            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="text-2xl sm:text-3xl font-extrabold text-blue-700">{{ number_format($stats['baru']) }}</div>
                            <p class="text-[11px] sm:text-xs text-gray-500 mt-1">Tiket baru di antrean</p>
                        </div>
                    </div>

                    <!-- Kartu 3: Diproses -->
                    <div class="bg-white p-4 sm:p-5 rounded-xl border border-amber-200 shadow-sm flex flex-col justify-between hover:border-amber-300 transition">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-amber-700 uppercase tracking-wider">Diproses</span>
                            <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="text-2xl sm:text-3xl font-extrabold text-amber-700">{{ number_format($stats['diproses']) }}</div>
                            <p class="text-[11px] sm:text-xs text-gray-500 mt-1">Ditangani teknisi IT</p>
                        </div>
                    </div>

                    <!-- Kartu 4: Selesai (selesai + ditutup) -->
                    <div class="bg-white p-4 sm:p-5 rounded-xl border border-emerald-200 shadow-sm flex flex-col justify-between hover:border-emerald-300 transition">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Selesai</span>
                            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="text-2xl sm:text-3xl font-extrabold text-emerald-700">{{ number_format($stats['selesai']) }}</div>
                            <p class="text-[11px] sm:text-xs text-gray-500 mt-1">Selesai & ditutup</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Tiket Terbaru -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Tiket Terbaru
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">5 laporan pengaduan kendala IT terakhir yang Anda ajukan.</p>
                    </div>
                    <div>
                        <a href="{{ route('tickets.index') }}" class="inline-flex items-center text-xs font-semibold text-brand-700 hover:text-brand-900 transition">
                            Lihat semua
                            <svg class="w-4 h-4 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                @if ($recentTickets->isEmpty())
                    <div class="text-center py-12 px-4">
                        <div class="w-14 h-14 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h4 class="text-base font-semibold text-gray-900">Belum ada tiket pengaduan</h4>
                        <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">
                            Anda belum pernah membuat laporan kendala IT. Jika ada gangguan perangkat atau sistem, silakan buat tiket baru.
                        </p>
                        <div class="mt-5">
                            <a href="{{ route('tickets.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Buat Tiket Pertama
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Tampilan Tabel Desktop & Tablet -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-[11px] font-semibold text-gray-500 uppercase border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3.5">Kode Tiket</th>
                                    <th class="px-6 py-3.5">Judul Kendala</th>
                                    <th class="px-6 py-3.5">Kategori</th>
                                    <th class="px-6 py-3.5">Status</th>
                                    <th class="px-6 py-3.5">Tanggal</th>
                                    <th class="px-6 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-xs">
                                @foreach ($recentTickets as $ticket)
                                    <tr class="hover:bg-gray-50/80 transition">
                                        <td class="px-6 py-4 whitespace-nowrap font-mono font-bold text-brand-700">
                                            <a href="{{ route('tickets.show', $ticket) }}" class="hover:underline">
                                                {{ $ticket->kode }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('tickets.show', $ticket) }}" class="font-medium text-gray-900 hover:text-brand-700 block max-w-md truncate" title="{{ $ticket->judul }}">
                                                {{ $ticket->judul }}
                                            </a>
                                            @if ($ticket->teknisi)
                                                <div class="text-[11px] text-gray-500 mt-0.5">
                                                    Teknisi: <span class="text-gray-700 font-medium">{{ $ticket->teknisi->name }}</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                {{ $ticket->category?->nama ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($ticket->status === 'baru')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                                    Baru
                                                </span>
                                            @elseif ($ticket->status === 'diproses')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                                    Diproses
                                                </span>
                                            @elseif ($ticket->status === 'selesai')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    Selesai
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-300">
                                                    Ditutup
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-[11px]">
                                            {{ $ticket->created_at ? $ticket->created_at->translatedFormat('d M Y, H:i') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <a href="{{ route('tickets.show', $ticket) }}" class="inline-flex items-center text-xs font-semibold text-brand-700 hover:text-brand-900 bg-brand-50 hover:bg-brand-100 border border-brand-200 px-3 py-1.5 rounded-lg transition shadow-sm">
                                                Detail
                                                <svg class="w-3.5 h-3.5 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Tampilan Kartu di Layar HP (Mobile) -->
                    <div class="block md:hidden divide-y divide-gray-100">
                        @foreach ($recentTickets as $ticket)
                            <div class="p-4 space-y-2 hover:bg-gray-50 transition">
                                <div class="flex items-center justify-between">
                                    <a href="{{ route('tickets.show', $ticket) }}" class="font-mono font-bold text-xs text-brand-700">
                                        {{ $ticket->kode }}
                                    </a>
                                    @if ($ticket->status === 'baru')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                            Baru
                                        </span>
                                    @elseif ($ticket->status === 'diproses')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                            Diproses
                                        </span>
                                    @elseif ($ticket->status === 'selesai')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-700 border border-gray-300">
                                            Ditutup
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('tickets.show', $ticket) }}" class="text-sm font-semibold text-gray-900 block line-clamp-2">
                                        {{ $ticket->judul }}
                                    </a>
                                </div>
                                <div class="flex items-center justify-between text-xs text-gray-500 pt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 text-gray-700 text-[11px]">
                                        {{ $ticket->category?->nama ?? '-' }}
                                    </span>
                                    <a href="{{ route('tickets.show', $ticket) }}" class="font-semibold text-brand-700 hover:text-brand-900 inline-flex items-center text-xs">
                                        Lihat Detail
                                        <svg class="w-3.5 h-3.5 ms-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Panduan Cara Melapor & Kotak Kontak IT -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Kotak Cara Melapor (2 Kolom di Desktop) -->
                <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-6">
                    <div class="border-b border-gray-100 pb-3">
                        <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            Panduan Cara Melapor Kendala IT
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Ikuti alur pelaporan berikut agar kendala di unit Anda dapat ditangani dengan cepat dan tepat.
                        </p>
                    </div>

                    <!-- 3 Langkah Bernomor -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">
                            3 Langkah Mudah Melapor:
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Langkah 1 -->
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200/80 flex flex-col justify-between">
                                <div>
                                    <div class="w-7 h-7 rounded-full bg-brand-600 text-white font-bold text-xs flex items-center justify-center mb-2 shadow-sm">
                                        1
                                    </div>
                                    <h5 class="text-sm font-semibold text-gray-900">Buat Tiket Baru</h5>
                                    <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                                        Klik tombol "Buat Tiket Baru", lalu pilih unit kerja dan kategori masalah (SIMRS, Jaringan, Komputer, atau Printer).
                                    </p>
                                </div>
                            </div>

                            <!-- Langkah 2 -->
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200/80 flex flex-col justify-between">
                                <div>
                                    <div class="w-7 h-7 rounded-full bg-brand-600 text-white font-bold text-xs flex items-center justify-center mb-2 shadow-sm">
                                        2
                                    </div>
                                    <h5 class="text-sm font-semibold text-gray-900">Jelaskan Kendala</h5>
                                    <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                                        Tuliskan judul dan rincian masalah, serta lampirkan foto/screenshot pesan error bila ada untuk mempermudah analisa.
                                    </p>
                                </div>
                            </div>

                            <!-- Langkah 3 -->
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200/80 flex flex-col justify-between">
                                <div>
                                    <div class="w-7 h-7 rounded-full bg-brand-600 text-white font-bold text-xs flex items-center justify-center mb-2 shadow-sm">
                                        3
                                    </div>
                                    <h5 class="text-sm font-semibold text-gray-900">Pantau Penanganan</h5>
                                    <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                                        Pantau respons dan catatan teknisi secara berkala di halaman detail tiket hingga status berubah menjadi selesai.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3 Tips Menulis Laporan yang Jelas -->
                    <div class="bg-brand-50/60 p-4 rounded-xl border border-brand-100">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-brand-800 mb-2.5 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            3 Tips Menulis Laporan yang Jelas:
                        </h4>
                        <ul class="space-y-2 text-xs text-gray-700">
                            <li class="flex items-start gap-2">
                                <span class="text-brand-600 font-bold mt-0.5">•</span>
                                <div>
                                    <strong class="text-gray-900">Sertakan Identitas & Lokasi Alat:</strong> Sebutkan nama ruangan/poli, nomor meja, atau nomor seri/tipe perangkat yang mengalami kendala.
                                </div>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-brand-600 font-bold mt-0.5">•</span>
                                <div>
                                    <strong class="text-gray-900">Catat Pesan Error Secara Tepat:</strong> Jika muncul jendela dialog error di layar, salin kalimatnya atau lampirkan foto agar teknisi langsung tahu penyebabnya.
                                </div>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-brand-600 font-bold mt-0.5">•</span>
                                <div>
                                    <strong class="text-gray-900">Pilih Prioritas Sesuai Kebutuhan:</strong> Gunakan prioritas "Tinggi" jika kendala menghentikan pelayanan darurat pasien secara langsung (seperti IGD, Farmasi, atau Kasir).
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Kotak Kontak IT (1 Kolom di Desktop) -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="border-b border-gray-100 pb-3 mb-4">
                            <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                                <span class="w-7 h-7 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a11.042 11.042 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </span>
                                Kontak Helpdesk IT
                            </h3>
                            <p class="text-xs text-gray-500 mt-1">
                                Butuh respon darurat? Hubungi tim IT RS Aisyiyah Kudus.
                            </p>
                        </div>

                        <div class="space-y-4">
                            <!-- Ekstensi Telepon -->
                            <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 border border-gray-100">
                                <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-800 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28a1 1 0 00-.684-.948l-4.493-1.498a1 1 0 00-1.21.502l-1.13 2.257a11.042 11.042 0 01-5.516-5.517l2.257-1.128a1 1 0 00.502-1.21L9.228 3.683A1 1 0 008.279 3H5z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Telepon Internal (PABX)</span>
                                    <p class="text-sm font-bold text-gray-900 mt-0.5">
                                        ext. [isi nomor]
                                    </p>
                                    <span class="text-[11px] text-gray-500">Ruang Server & Helpdesk IT</span>
                                </div>
                            </div>

                            <!-- Jam Layanan -->
                            <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 border border-gray-100">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-800 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Jam Operasional Layanan</span>
                                    <p class="text-sm font-bold text-gray-900 mt-0.5">
                                        jam layanan [isi jam]
                                    </p>
                                    <span class="text-[11px] text-gray-500">Siaga On-Call 24 Jam IGD / Rawat Inap</span>
                                </div>
                            </div>

                            <!-- Lokasi Kantor IT -->
                            <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 border border-gray-100">
                                <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-800 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Lokasi Kantor</span>
                                    <p class="text-xs font-semibold text-gray-800 mt-0.5">
                                        Instalasi SIMRS & TI
                                    </p>
                                    <span class="text-[11px] text-gray-500">Gedung Administrasi Lt. 2, RS Aisyiyah Kudus</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan Darurat -->
                    <div class="mt-4 pt-3 border-t border-gray-100 text-[11px] text-gray-500 leading-relaxed bg-amber-50/70 p-3 rounded-lg border border-amber-200 text-amber-900">
                        <strong class="font-bold text-amber-950">Catatan Darurat:</strong> Untuk kendala sistem yang menghentikan tindakan gawat darurat pasien, segera buat tiket lalu hubungi ext. [isi nomor] agar dapat diprioritaskan saat itu juga.
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
