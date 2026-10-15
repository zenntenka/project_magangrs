<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Semua Tiket Pengaduan IT') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Pantau seluruh tiket sistem, filter berdasarkan berbagai kriteria, dan tugaskan tiket ke teknisi.
                </p>
            </div>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 bg-white border border-gray-300 px-3.5 py-2 rounded-lg shadow-sm hover:bg-gray-50 transition">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard Admin
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

            <!-- Filter Panel Lengkap -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <form action="{{ route('admin.tickets.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 items-end">

                    <!-- Filter Status -->
                    <div>
                        <label for="status" class="block text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Status
                        </label>
                        <select name="status" id="status" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Status</option>
                            <option value="baru" {{ $status === 'baru' ? 'selected' : '' }}>Baru</option>
                            <option value="diproses" {{ $status === 'diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="selesai" {{ $status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="ditutup" {{ $status === 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                        </select>
                    </div>

                    <!-- Filter Kategori -->
                    <div>
                        <label for="category_id" class="block text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Kategori
                        </label>
                        <select name="category_id" id="category_id" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (string) $categoryId === (string) $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Unit -->
                    <div>
                        <label for="unit_id" class="block text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Unit RS
                        </label>
                        <select name="unit_id" id="unit_id" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Unit</option>
                            @foreach ($units as $u)
                                <option value="{{ $u->id }}" {{ (string) $unitId === (string) $u->id ? 'selected' : '' }}>
                                    {{ $u->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Prioritas -->
                    <div>
                        <label for="prioritas" class="block text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Prioritas
                        </label>
                        <select name="prioritas" id="prioritas" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Prioritas</option>
                            <option value="tinggi" {{ $prioritas === 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                            <option value="sedang" {{ $prioritas === 'sedang' ? 'selected' : '' }}>Sedang</option>
                            <option value="rendah" {{ $prioritas === 'rendah' ? 'selected' : '' }}>Rendah</option>
                        </select>
                    </div>

                    <!-- Filter Teknisi -->
                    <div>
                        <label for="teknisi_id" class="block text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Teknisi
                        </label>
                        <select name="teknisi_id" id="teknisi_id" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Teknisi</option>
                            <option value="unassigned" {{ $teknisiId === 'unassigned' ? 'selected' : '' }}>Belum Ditugaskan</option>
                            @foreach ($teknisiList as $tek)
                                <option value="{{ $tek->id }}" {{ (string) $teknisiId === (string) $tek->id ? 'selected' : '' }}>
                                    {{ $tek->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search & Tombol -->
                    <div>
                        <label for="q" class="block text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Cari Kode / Judul
                        </label>
                        <div class="flex items-center space-x-1">
                            <input type="text" name="q" id="q" value="{{ $q }}" placeholder="Kata kunci..."
                                   class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <button type="submit" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm transition" title="Terapkan Filter">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                            @if ($status || $categoryId || $unitId || $prioritas || $teknisiId || $q)
                                <a href="{{ route('admin.tickets.index') }}" class="px-2 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs rounded-lg transition" title="Reset Filter">
                                    &times;
                                </a>
                            @endif
                        </div>
                    </div>

                </form>
            </div>

            <!-- Tabel Tiket -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                @if ($tickets->isEmpty())
                    <div class="text-center py-16 px-4">
                        <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900">Tidak ada tiket ditemukan</h3>
                        <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">
                            Tidak ada tiket yang sesuai dengan filter atau pencarian Anda.
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase border-b border-gray-200">
                                <tr>
                                    <th class="px-5 py-3.5">Kode & Tanggal</th>
                                    <th class="px-5 py-3.5">Pelapor & Unit</th>
                                    <th class="px-5 py-3.5">Kendala & Kategori</th>
                                    <th class="px-5 py-3.5">Prioritas</th>
                                    <th class="px-5 py-3.5">Status</th>
                                    <th class="px-5 py-3.5 min-w-[200px]">Penugasan Teknisi</th>
                                    <th class="px-5 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($tickets as $ticket)
                                    <tr class="hover:bg-gray-50 transition">
                                        <!-- Kode -->
                                        <td class="px-5 py-4 whitespace-nowrap">
                                            <a href="{{ route('admin.tickets.show', $ticket) }}" class="font-mono text-xs font-semibold text-indigo-600 hover:underline">
                                                {{ $ticket->kode }}
                                            </a>
                                            <div class="text-[11px] text-gray-400 font-sans mt-0.5">
                                                {{ $ticket->created_at->format('d/m/Y H:i') }}
                                            </div>
                                        </td>

                                        <!-- Pelapor & Unit -->
                                        <td class="px-5 py-4 whitespace-nowrap">
                                            <div class="font-medium text-gray-900">{{ $ticket->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $ticket->unit->nama }}</div>
                                        </td>

                                        <!-- Kendala -->
                                        <td class="px-5 py-4">
                                            <div class="font-medium text-gray-900 max-w-xs truncate" title="{{ $ticket->judul }}">
                                                {{ $ticket->judul }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $ticket->category->nama }}</div>
                                        </td>

                                        <!-- Prioritas -->
                                        <td class="px-5 py-4 whitespace-nowrap">
                                            @if ($ticket->prioritas === 'tinggi')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                    Tinggi
                                                </span>
                                            @elseif ($ticket->prioritas === 'sedang')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                                                    Sedang
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    Rendah
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Status -->
                                        <td class="px-5 py-4 whitespace-nowrap">
                                            @if ($ticket->status === 'baru')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Baru</span>
                                            @elseif ($ticket->status === 'diproses')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Diproses</span>
                                            @elseif ($ticket->status === 'selesai')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Selesai</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Ditutup</span>
                                            @endif
                                        </td>

                                        <!-- Quick Assign Dropdown -->
                                        <td class="px-5 py-4 whitespace-nowrap">
                                            <form action="{{ route('admin.tickets.assign', $ticket) }}" method="POST" class="flex items-center space-x-1.5">
                                                @csrf
                                                <select name="teknisi_id" required
                                                        class="text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1 pl-2 pr-6">
                                                    <option value="">Pilih Teknisi...</option>
                                                    @foreach ($teknisiList as $tek)
                                                        <option value="{{ $tek->id }}" {{ (int) $ticket->teknisi_id === (int) $tek->id ? 'selected' : '' }}>
                                                            {{ $tek->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded shadow-sm transition" title="Tugaskan Tiket">
                                                    {{ $ticket->teknisi_id ? 'Ubah' : 'Tugaskan' }}
                                                </button>
                                            </form>
                                        </td>

                                        <!-- Aksi Detail -->
                                        <td class="px-5 py-4 whitespace-nowrap text-right">
                                            <a href="{{ route('admin.tickets.show', $ticket) }}" class="inline-flex items-center px-2.5 py-1 bg-white border border-gray-300 rounded text-xs font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
                                                Detail &rarr;
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

