<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Tiket Pengaduan Saya') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Pantau status dan perkembangan tindak lanjut kendala IT yang Anda laporkan.
                </p>
            </div>
            <div>
                <a href="{{ route('tickets.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-medium rounded-lg shadow-sm transition duration-150 ease-in-out">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Tiket Baru
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

            <!-- Daftar Tiket -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                @if ($tickets->isEmpty())
                    <div class="text-center py-16 px-4">
                        <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900">Belum ada tiket pengaduan</h3>
                        <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">
                            Anda belum pernah membuat laporan tiket kendala IT. Jika ada gangguan sistem atau perangkat, silakan buat tiket baru.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('tickets.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm">
                                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Buat Tiket Sekarang
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Tampilan Desktop (Tabel) -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode Tiket</th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Judul & Unit</th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Prioritas</th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu Buat</th>
                                    <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($tickets as $ticket)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-semibold text-indigo-600">
                                            <a href="{{ route('tickets.show', $ticket) }}" class="hover:underline">
                                                {{ $ticket->kode }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900 line-clamp-1">
                                                <a href="{{ route('tickets.show', $ticket) }}" class="hover:text-indigo-600">
                                                    {{ $ticket->judul }}
                                                </a>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-0.5">
                                                Unit: {{ $ticket->unit->nama ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $ticket->category->nama ?? '-' }}
                                            </span>
                                        </td>
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
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                            {{ $ticket->created_at->translatedFormat('d M Y, H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('tickets.show', $ticket) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 text-xs font-semibold bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-md transition">
                                                Lihat Detail
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

                    <!-- Tampilan Mobile (Cards) -->
                    <div class="block md:hidden divide-y divide-gray-200">
                        @foreach ($tickets as $ticket)
                            <div class="p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="font-mono text-xs font-bold text-indigo-600">
                                        {{ $ticket->kode }}
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        {{ $ticket->created_at->translatedFormat('d M Y, H:i') }}
                                    </span>
                                </div>
                                <div>
                                    <a href="{{ route('tickets.show', $ticket) }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600 block">
                                        {{ $ticket->judul }}
                                    </a>
                                    <div class="text-xs text-gray-500 mt-1 flex flex-wrap gap-x-2">
                                        <span>Unit: {{ $ticket->unit->nama ?? '-' }}</span>
                                        <span>&bull;</span>
                                        <span>Kategori: {{ $ticket->category->nama ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-1">
                                    <div class="flex items-center space-x-2">
                                        <!-- Prioritas -->
                                        @if ($ticket->prioritas === 'tinggi')
                                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-red-100 text-red-700">Tinggi</span>
                                        @elseif ($ticket->prioritas === 'sedang')
                                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-100 text-amber-700">Sedang</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700">Rendah</span>
                                        @endif

                                        <!-- Status -->
                                        @if ($ticket->status === 'baru')
                                            <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-blue-100 text-blue-800">Baru</span>
                                        @elseif ($ticket->status === 'diproses')
                                            <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-amber-100 text-amber-800">Diproses</span>
                                        @elseif ($ticket->status === 'selesai')
                                            <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-100 text-emerald-800">Selesai</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-700">Ditutup</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('tickets.show', $ticket) }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                                        Detail &rarr;
                                    </a>
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
