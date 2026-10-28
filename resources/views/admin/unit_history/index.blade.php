<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Riwayat & Rekapitulasi Tiket per Unit') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Analisis frekuensi kendala, performa penanganan IT, dan distribusi masalah pada setiap unit kerja RS.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.laporan.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition">
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Laporan Lengkap
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Periode Riwayat -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <form action="{{ route('admin.unit-riwayat.index') }}" method="GET" class="space-y-4">
                    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
                        <!-- Pilihan Preset Cepat -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">
                                Filter Cepat Periode
                            </label>
                            <div class="inline-flex rounded-lg border border-gray-200 bg-gray-50 p-1 text-xs">
                                <a href="{{ route('admin.unit-riwayat.index', ['periode' => 'semua']) }}"
                                   class="px-3 py-1.5 rounded-md font-medium transition {{ $periode === 'semua' && empty($tanggalMulai) && empty($tanggalSelesai) ? 'bg-white text-indigo-600 shadow-sm font-semibold' : 'text-gray-600 hover:text-gray-900' }}">
                                    Semua Waktu
                                </a>
                                <a href="{{ route('admin.unit-riwayat.index', ['periode' => '30_hari']) }}"
                                   class="px-3 py-1.5 rounded-md font-medium transition {{ $periode === '30_hari' && empty($tanggalMulai) && empty($tanggalSelesai) ? 'bg-white text-indigo-600 shadow-sm font-semibold' : 'text-gray-600 hover:text-gray-900' }}">
                                    30 Hari Terakhir
                                </a>
                                <a href="{{ route('admin.unit-riwayat.index', ['periode' => 'bulan_ini']) }}"
                                   class="px-3 py-1.5 rounded-md font-medium transition {{ $periode === 'bulan_ini' && empty($tanggalMulai) && empty($tanggalSelesai) ? 'bg-white text-indigo-600 shadow-sm font-semibold' : 'text-gray-600 hover:text-gray-900' }}">
                                    Bulan Ini
                                </a>
                            </div>
                        </div>

                        <!-- Rentang Tanggal Manual -->
                        <div class="flex flex-wrap sm:flex-nowrap items-center gap-3">
                            <div>
                                <label for="tanggal_mulai" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">
                                    Dari Tanggal
                                </label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ $tanggalMulai }}"
                                       class="text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label for="tanggal_selesai" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">
                                    Sampai Tanggal
                                </label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ $tanggalSelesai }}"
                                       class="text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="pt-5 flex items-center gap-2">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-xs text-white uppercase tracking-wider hover:bg-indigo-700 shadow-sm transition">
                                    Terapkan
                                </button>
                                @if (!empty($tanggalMulai) || !empty($tanggalSelesai) || $periode !== 'semua')
                                    <a href="{{ route('admin.unit-riwayat.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium transition" title="Reset Filter">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Kartu Ringkasan Global -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Total Unit RS -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Unit RS</span>
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($globalSummary['total_units']) }}</div>
                        <p class="text-xs text-gray-500 mt-1">{{ $globalSummary['active_units'] }} unit memiliki tiket</p>
                    </div>
                </div>

                <!-- Total Tiket Periode Ini -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Tiket</span>
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-2xl font-bold text-blue-600">{{ number_format($globalSummary['total_tickets']) }}</div>
                        <p class="text-xs text-gray-500 mt-1">Pada filter periode aktif</p>
                    </div>
                </div>

                <!-- Tiket Masih Terbuka -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Tiket Terbuka</span>
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-2xl font-bold text-amber-600">{{ number_format($globalSummary['open_tickets']) }}</div>
                        <p class="text-xs text-gray-500 mt-1">Status baru & diproses</p>
                    </div>
                </div>

                <!-- Rata-rata Waktu Penyelesaian Global -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Rata-rata Waktu</span>
                        <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-lg font-bold text-purple-700 leading-snug">{{ $globalSummary['avg_duration'] }}</div>
                        <p class="text-xs text-gray-500 mt-1">Lapor s/d selesai per tiket</p>
                    </div>
                </div>
            </div>

            <!-- Tabel Rekapitulasi per Unit RS -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Tabel Ringkasan Kinerja & Kendala per Unit</h3>
                        <p class="text-xs text-gray-500">Klik nama unit atau tombol aksi untuk melihat histori tiket dan grafik rincian per kategori.</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">
                        {{ $unitSummaries->count() }} Unit Terdaftar
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-[11px] font-semibold text-gray-500 uppercase border-b border-gray-200">
                            <tr>
                                <th class="px-5 py-3 text-center w-12">No</th>
                                <th class="px-5 py-3">Nama Unit Rumah Sakit</th>
                                <th class="px-5 py-3 text-center">Total Tiket</th>
                                <th class="px-5 py-3">Kategori Terbanyak</th>
                                <th class="px-5 py-3">Rata-rata Waktu Selesai</th>
                                <th class="px-5 py-3 text-center">Tiket Masih Terbuka</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs">
                            @forelse ($unitSummaries as $index => $summary)
                                <tr class="hover:bg-gray-50/80 transition group">
                                    <td class="px-5 py-3.5 text-center text-gray-400 font-mono">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <a href="{{ route('admin.unit-riwayat.show', ['unit' => $summary->id, 'periode' => $periode, 'tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai]) }}"
                                           class="font-semibold text-gray-900 hover:text-indigo-600 flex items-center gap-1.5 transition">
                                            <span>{{ $summary->nama }}</span>
                                            <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-indigo-600 opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            Selesai: {{ $summary->selesai_tickets }} | Ditutup: {{ $summary->ditutup_tickets }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold {{ $summary->total_tickets > 0 ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $summary->total_tickets }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if ($summary->raw_top_category)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">
                                                {{ $summary->top_category }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 italic">-</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-purple-700 font-medium">
                                        {{ $summary->avg_duration }}
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        @if ($summary->open_tickets > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                                {{ $summary->open_tickets }} Terbuka
                                                <span class="ml-1 text-[10px] font-normal text-amber-700">({{ $summary->baru_tickets }} baru, {{ $summary->diproses_tickets }} proses)</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                                Tuntas (0)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                        <a href="{{ route('admin.unit-riwayat.show', ['unit' => $summary->id, 'periode' => $periode, 'tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai]) }}"
                                           class="inline-flex items-center px-3 py-1.5 bg-indigo-50 border border-indigo-200 rounded-lg text-xs font-semibold text-indigo-700 hover:bg-indigo-100 transition shadow-sm">
                                            Detail Unit
                                            <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-10 text-gray-400">
                                        Belum ada data unit rumah sakit yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

