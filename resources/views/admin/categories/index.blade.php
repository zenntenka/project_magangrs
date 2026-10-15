<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Kelola Master Data Kategori Kendala IT') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Klasifikasi kendala teknis (Komputer, Jaringan, Printer, SIMRS, dll).
                </p>
            </div>
            <div>
                <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Kategori Baru
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

            <!-- Pencarian -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                <form action="{{ route('admin.categories.index') }}" method="GET" class="flex items-center gap-3 w-full sm:w-80">
                    <div class="relative w-full">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama kategori..."
                               class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pl-9">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <button type="submit" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                        Cari
                    </button>
                    @if ($search)
                        <a href="{{ route('admin.categories.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline whitespace-nowrap">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Tabel Kategori -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                @if ($categories->isEmpty())
                    <div class="text-center py-16 px-4">
                        <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900">Belum ada kategori kendala</h3>
                        <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">
                            Tambahkan jenis kategori gangguan untuk memudahkan pelaporan dan analisis tiket IT.
                        </p>
                    </div>
                @else
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3.5">Nama Kategori</th>
                                <th class="px-6 py-3.5 text-center">Jumlah Tiket Terkait</th>
                                <th class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($categories as $category)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        {{ $category->nama }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->tickets_count > 0 ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $category->tickets_count }} tiket
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="inline-flex items-center px-2.5 py-1.5 bg-white border border-gray-300 rounded text-xs font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
                                            Edit
                                        </a>

                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori &quot;{{ $category->nama }}&quot;?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-2.5 py-1.5 bg-red-50 border border-red-200 rounded text-xs font-medium text-red-600 hover:bg-red-100 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

