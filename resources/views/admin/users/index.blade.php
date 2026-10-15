<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Kelola Master Data Pengguna') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Daftar akun admin, teknisi IT, dan staf pelapor rumah sakit.
                </p>
            </div>
            <div>
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Tambah Pengguna Baru
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

            <!-- Panel Filter & Pencarian -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                    <!-- Filter Role -->
                    <div>
                        <label for="role" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Peran (Role)
                        </label>
                        <select name="role" id="role" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Peran</option>
                            <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="teknisi" {{ $role === 'teknisi' ? 'selected' : '' }}>Teknisi</option>
                            <option value="pelapor" {{ $role === 'pelapor' ? 'selected' : '' }}>Pelapor</option>
                        </select>
                    </div>

                    <!-- Filter Unit -->
                    <div>
                        <label for="unit_id" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Unit / Ruangan RS
                        </label>
                        <select name="unit_id" id="unit_id" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Unit</option>
                            @foreach ($units as $u)
                                <option value="{{ $u->id }}" {{ (string) $unitId === (string) $u->id ? 'selected' : '' }}>
                                    {{ $u->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kata Kunci Pencarian -->
                    <div>
                        <label for="search" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Cari Nama / Email
                        </label>
                        <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Nama atau email..."
                               class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <!-- Tombol Filter -->
                    <div class="flex items-center space-x-2">
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                            <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter
                        </button>
                        @if ($role || $unitId || $search)
                            <a href="{{ route('admin.users.index') }}" class="px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition" title="Reset Filter">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabel Pengguna -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                @if ($users->isEmpty())
                    <div class="text-center py-16 px-4">
                        <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900">Pengguna tidak ditemukan</h3>
                        <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">
                            Tidak ada data akun yang cocok dengan kriteria pencarian Anda.
                        </p>
                    </div>
                @else
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3.5">Nama & Email</th>
                                <th class="px-6 py-3.5">Peran (Role)</th>
                                <th class="px-6 py-3.5">Unit Rumah Sakit</th>
                                <th class="px-6 py-3.5">Bergabung</th>
                                <th class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($users as $user)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($user->role === 'admin')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                                Admin
                                            </span>
                                        @elseif ($user->role === 'teknisi')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                Teknisi IT
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                Pelapor
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-700">
                                        {{ $user->unit?->nama ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                        {{ $user->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-2.5 py-1.5 bg-white border border-gray-300 rounded text-xs font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
                                            Edit
                                        </a>

                                        @if ($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun &quot;{{ $user->name }}&quot;?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-2.5 py-1.5 bg-red-50 border border-red-200 rounded text-xs font-medium text-red-600 hover:bg-red-100 transition">
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="inline-block px-2.5 py-1.5 text-xs text-gray-400 italic">
                                                Akun Anda
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

