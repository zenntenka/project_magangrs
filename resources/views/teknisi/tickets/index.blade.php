<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Kelola Tiket IT') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Daftar antrean tiket baru dan tiket yang sedang Anda tangani.
                </p>
            </div>
            <div>
                <a href="{{ route('teknisi.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 bg-white border border-gray-300 px-3.5 py-2 rounded-lg shadow-sm hover:bg-gray-50 transition">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard Teknisi
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

            <!-- Filter Panel -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <form action="{{ route('teknisi.tickets.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">

                    <!-- Filter Status -->
                    <div>
                        <label for="status" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Status Tiket
                        </label>
                        <select name="status" id="status" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Status</option>
                            <option value="baru" {{ request('status') === 'baru' ? 'selected' : '' }}>Baru (Antrean)</option>
                            <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Diproses (Tugas Saya)</option>
                            <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="ditutup" {{ request('status') === 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                        </select>
                    </div>

                    <!-- Filter Kategori -->
                    <div>
                        <label for="category_id" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Kategori Kendala
                        </label>
                        <select name="category_id" id="category_id" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (string) request('category_id') === (string) $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Prioritas -->
                    <div>
                        <label for="prioritas" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Tingkat Prioritas
                        </label>
                        <select name="prioritas" id="prioritas" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Prioritas</option>
                            <option value="tinggi" {{ request('prioritas') === 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                            <option value="sedang" {{ request('prioritas') === 'sedang' ? 'selected' : '' }}>Sedang</option>
                            <option value="rendah" {{ request('prioritas') === 'rendah' ? 'selected' : '' }}>Rendah</option>
                        </select>
                    </div>

                    <!-- Tombol Aksi Filter -->
                    <div class="flex items-center space-x-2">
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                            <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter
                        </button>
                        @if (request()->filled('status') || request()->filled('category_id') || request()->filled('prioritas'))
                            <a href="{{ route('teknisi.tickets.index') }}" class="px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition" title="Reset Filter">
                                Reset
                            </a>
                        @endif
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
                        <h3 class="text-base font-semibold text-gray-900">Tidak ada tiket yang cocok</h3>
                        <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">
                            Tidak ditemukan tiket dengan filter yang dipilih, atau belum ada tiket baru yang masuk.
                        </p>
                    </div>
                @else
                    <!-- Tampilan Desktop (Tabel) -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelapor & Unit</th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kendala & Kategori</th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Prioritas</th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Teknisi</th>
                                    <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($tickets as $ticket)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <!-- Kode -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-semibold text-indigo-600">
                                            <a href="{{ route('teknisi.tickets.show', $ticket) }}" class="hover:underline">
                                                {{ $ticket->kode }}
                                            </a>
                                            <div class="text-[11px] text-gray-400 font-sans mt-0.5">
                                                {{ $ticket->created_at->diffForHumans() }}
                                            </div>
                                        </td>

                                        <!-- Pelapor & Unit -->
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $ticket->user->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 mt-0.5 flex items-center">
                                                <svg class="w-3.5 h-3.5 me-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                {{ $ticket->unit->nama ?? '-' }}
                                            </div>
                                        </td>

                                        <!-- Kendala & Kategori -->
                                        <td class="px-6 py-4 max-w-xs">
                                            <div class="text-sm font-semibold text-gray-900 truncate">
                                                <a href="{{ route('teknisi.tickets.show', $ticket) }}" class="hover:text-indigo-600">
                                                    {{ $ticket->judul }}
                                                </a>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-700">
                                                    {{ $ticket->category->nama ?? '-' }}
                                                </span>
                                            </div>
                                        </td>

                                        <!-- Prioritas -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($ticket->prioritas === 'tinggi')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                                                    <span class="w-1.5 h-1.5 me-1.5 rounded-full bg-red-600"></span>
                                                    Tinggi
                                                </span>
                                            @elseif ($ticket->prioritas === 'sedang')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                                    <span class="w-1.5 h-1.5 me-1.5 rounded-full bg-amber-500"></span>
                                                    Sedang
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                    <span class="w-1.5 h-1.5 me-1.5 rounded-full bg-slate-400"></span>
                                                    Rendah
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Status -->
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

                                        <!-- Teknisi -->
                                        <td class="px-6 py-4 whitespace-nowrap text-xs">
                                            @if ($ticket->teknisi_id === Auth::id())
                                                <span class="inline-flex items-center font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded">
                                                    <svg class="w-3.5 h-3.5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    Ditangani Saya
                                                </span>
                                            @elseif ($ticket->teknisi)
                                                <span class="text-gray-600">{{ $ticket->teknisi->name }}</span>
                                            @else
                                                <span class="text-amber-600 italic">Belum Ada</span>
                                            @endif
                                        </td>

                                        <!-- Aksi -->
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                @if ($ticket->status === 'baru' && $ticket->teknisi_id === null)
                                                    <form action="{{ route('teknisi.tickets.take', $ticket) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                                            <svg class="w-3.5 h-3.5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            Ambil Tiket
                                                        </button>
                                                    </form>
                                                @endif
                                                <a href="{{ route('teknisi.tickets.show', $ticket) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 text-xs font-semibold bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition">
                                                    Detail
                                                    <svg class="w-3.5 h-3.5 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Tampilan Mobile (Cards) -->
                    <div class="block md:hidden divide-y divide-gray-200">
                        @foreach ($tickets as $ticket)
                            <div class="p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="font-mono text-xs font-bold text-indigo-600">{{ $ticket->kode }}</span>
                                    <span class="text-xs text-gray-400">{{ $ticket->created_at->diffForHumans() }}</span>
                                </div>
                                <div>
                                    <a href="{{ route('teknisi.tickets.show', $ticket) }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600 block">
                                        {{ $ticket->judul }}
                                    </a>
                                    <div class="text-xs text-gray-500 mt-1 flex flex-wrap gap-x-2">
                                        <span>Pelapor: {{ $ticket->user->name }}</span>
                                        <span>&bull;</span>
                                        <span>Unit: {{ $ticket->unit->nama ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-1">
                                    <div class="flex items-center space-x-1.5">
                                        <!-- Prioritas -->
                                        @if ($ticket->prioritas === 'tinggi')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-red-100 text-red-700">Tinggi</span>
                                        @elseif ($ticket->prioritas === 'sedang')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-700">Sedang</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">Rendah</span>
                                        @endif

                                        <!-- Status -->
                                        @if ($ticket->status === 'baru')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">Baru</span>
                                        @elseif ($ticket->status === 'diproses')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-amber-100 text-amber-800">Diproses</span>
                                        @elseif ($ticket->status === 'selesai')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-100 text-emerald-800">Selesai</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-700">Ditutup</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        @if ($ticket->status === 'baru' && $ticket->teknisi_id === null)
                                            <form action="{{ route('teknisi.tickets.take', $ticket) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 bg-blue-600 text-white text-xs font-semibold rounded shadow-sm">
                                                    Ambil
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('teknisi.tickets.show', $ticket) }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                                            Detail &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Paginasi -->
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>