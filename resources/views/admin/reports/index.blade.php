<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Laporan & Rekapitulasi Tiket IT') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Analisis data keluhan, rekapitulasi performa teknisi, serta ekspor dokumen resmi.
                </p>
            </div>

            <!-- Tombol Ekspor Data -->
            <div class="flex items-center space-x-2.5">
                <a href="{{ route('admin.laporan.pdf', request()->query()) }}" target="_blank"
                   class="inline-flex items-center px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Ekspor PDF
                </a>

                <a href="{{ route('admin.laporan.excel', request()->query()) }}"
                   class="inline-flex items-center px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Ekspor Excel
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Kartu Ringkasan Metrik (Berdasarkan Filter Aktif) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                <!-- Total Tiket -->
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Total Tiket</span>
                    <div class="text-2xl font-bold text-gray-900 mt-1.5">{{ number_format($summary['total']) }}</div>
                    <span class="text-[10px] text-gray-400">Sesuai filter aktif</span>
                </div>

                <!-- Status Baru -->
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <span class="text-[11px] font-semibold text-blue-600 uppercase tracking-wider block">Baru</span>
                    <div class="text-2xl font-bold text-blue-600 mt-1.5">{{ number_format($summary['baru']) }}</div>
                    <span class="text-[10px] text-gray-400">Belum diproses</span>
                </div>

                <!-- Status Diproses -->
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <span class="text-[11px] font-semibold text-amber-600 uppercase tracking-wider block">Diproses</span>
                    <div class="text-2xl font-bold text-amber-600 mt-1.5">{{ number_format($summary['diproses']) }}</div>
                    <span class="text-[10px] text-gray-400">Dalam pengerjaan</span>
                </div>

                <!-- Status Selesai -->
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <span class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider block">Selesai</span>
                    <div class="text-2xl font-bold text-emerald-600 mt-1.5">{{ number_format($summary['selesai']) }}</div>
                    <span class="text-[10px] text-gray-400">Tuntas diperbaiki</span>
                </div>

                <!-- Status Ditutup -->
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <span class="text-[11px] font-semibold text-gray-600 uppercase tracking-wider block">Ditutup</span>
                    <div class="text-2xl font-bold text-gray-600 mt-1.5">{{ number_format($summary['ditutup']) }}</div>
                    <span class="text-[10px] text-gray-400">Arsip laporan</span>
                </div>

                <!-- Rata-rata Durasi -->
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <span class="text-[11px] font-semibold text-purple-600 uppercase tracking-wider block">Rata-rata Waktu</span>
                    <div class="text-sm font-bold text-purple-700 mt-2 leading-tight">{{ $summary['avg_duration'] }}</div>
                    <span class="text-[10px] text-gray-400">Lapor s/d Selesai</span>
                </div>
            </div>

            <!-- Panel Filter Komprehensif -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <form action="{{ route('admin.laporan.index') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">

                        <!-- Tanggal Mulai -->
                        <div>
                            <label for="tanggal_mulai" class="block text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                Tanggal Mulai
                            </label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                                   class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Tanggal Selesai -->
                        <div>
                            <label for="tanggal_selesai" class="block text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                Tanggal Selesai
                            </label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ request('tanggal_selesai') }}"
                                   class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Unit Rumah Sakit -->
                        <div>
                            <label for="unit_id" class="block text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                Unit RS
                            </label>
                            <select name="unit_id" id="unit_id" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Unit</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}" {{ (string) request('unit_id') === (string) $unit->id ? 'selected' : '' }}>
                                        {{ $unit->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Kategori Kendala -->
                        <div>
                            <label for="category_id" class="block text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                Kategori
                            </label>
                            <select name="category_id" id="category_id" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (string) request('category_id') === (string) $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Tiket -->
                        <div>
                            <label for="status" class="block text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                Status
                            </label>
                            <select name="status" id="status" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Status</option>
                                <option value="baru" {{ request('status') === 'baru' ? 'selected' : '' }}>Baru</option>
                                <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Diproses</option>
                                <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="ditutup" {{ request('status') === 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                            </select>
                        </div>

                        <!-- Teknisi -->
                        <div>
                            <label for="teknisi_id" class="block text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                Teknisi
                            </label>
                            <select name="teknisi_id" id="teknisi_id" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Teknisi</option>
                                <option value="unassigned" {{ request('teknisi_id') === 'unassigned' ? 'selected' : '' }}>Belum Ditugaskan</option>
                                @foreach ($teknisiList as $tek)
                                    <option value="{{ $tek->id }}" {{ (string) request('teknisi_id') === (string) $tek->id ? 'selected' : '' }}>
                                        {{ $tek->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <!-- Tombol Filter -->
                    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                        <div class="text-xs text-gray-500">
                            Menampilkan hasil rekapitulasi data tiket berdasarkan filter yang dipilih.
                        </div>
                        <div class="flex items-center space-x-2">
                            @if (request()->hasAny(['tanggal_mulai', 'tanggal_selesai', 'unit_id', 'category_id', 'status', 'teknisi_id']))
                                <a href="{{ route('admin.laporan.index') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition">
                                    Reset Filter
                                </a>
                            @endif
                            <button type="submit" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Terapkan Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabel Data Laporan -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                @if ($tickets->isEmpty())
                    <div class="text-center py-16 px-4">
                        <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900">Tidak ada tiket pada kriteria laporan ini</h3>
                        <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">
                            Coba ubah rentang tanggal atau bersihkan opsi filter untuk melihat lebih banyak data.
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-[11px] font-semibold text-gray-500 uppercase border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-center">No</th>
                                    <th class="px-4 py-3">Kode & Tanggal</th>
                                    <th class="px-4 py-3">Pelapor & Unit</th>
                                    <th class="px-4 py-3">Kendala & Kategori</th>
                                    <th class="px-4 py-3">Prioritas</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Teknisi</th>
                                    <th class="px-4 py-3">Waktu Selesai</th>
                                    <th class="px-4 py-3">Durasi</th>
                                    <th class="px-4 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-xs">
                                @foreach ($tickets as $index => $ticket)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-center text-gray-400">
                                            {{ $tickets->firstItem() + $index }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <a href="{{ route('admin.tickets.show', $ticket) }}" class="font-mono font-semibold text-indigo-600 hover:underline">
                                                {{ $ticket->kode }}
                                            </a>
                                            <div class="text-[10px] text-gray-400">
                                                {{ $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : '-' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="font-medium text-gray-900">{{ $ticket->user?->name ?? '-' }}</div>
                                            <div class="text-[11px] text-gray-500">{{ $ticket->unit?->nama ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-900 max-w-xs truncate" title="{{ $ticket->judul }}">
                                                {{ $ticket->judul }}
                                            </div>
                                            <div class="text-[11px] text-gray-500">{{ $ticket->category?->nama ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if ($ticket->prioritas === 'tinggi')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-red-100 text-red-800">Tinggi</span>
                                            @elseif ($ticket->prioritas === 'sedang')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800">Sedang</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-blue-800">Rendah</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if ($ticket->status === 'baru')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-100 text-blue-800">Baru</span>
                                            @elseif ($ticket->status === 'diproses')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-100 text-amber-800">Diproses</span>
                                            @elseif ($ticket->status === 'selesai')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-800">Selesai</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-700">Ditutup</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if ($ticket->teknisi)
                                                <span class="font-medium text-gray-900">{{ $ticket->teknisi->name }}</span>
                                            @else
                                                <span class="text-gray-400 italic">Belum ditugaskan</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-600">
                                            {{ $ticket->waktu_selesai ? $ticket->waktu_selesai->format('d/m/Y H:i') : '-' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap font-medium text-purple-700">
                                            {{ $ticket->durasi_penyelesaian }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right">
                                            <a href="{{ route('admin.tickets.show', $ticket) }}" class="inline-flex items-center px-2 py-1 bg-white border border-gray-300 rounded text-[11px] font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

