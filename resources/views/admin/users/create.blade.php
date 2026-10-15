<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Tambah Pengguna Baru') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Buat akun baru untuk admin, teknisi IT, atau staf pelapor unit rumah sakit.
                </p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-6 sm:p-8 rounded-xl border border-gray-200 shadow-sm">
                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                               placeholder="Contoh: dr. Ahmad, Siti Rahma (Perawat IGD)"
                               class="mt-1 block w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700">
                            Alamat Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               placeholder="user@rs.local"
                               class="mt-1 block w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-semibold text-gray-700">
                            Peran Pengguna (Role) <span class="text-red-500">*</span>
                        </label>
                        <select name="role" id="role" required
                                class="mt-1 block w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Pilih Peran Pengguna</option>
                            <option value="pelapor" {{ old('role') === 'pelapor' ? 'selected' : '' }}>Pelapor (Staf/Nakes Rumah Sakit)</option>
                            <option value="teknisi" {{ old('role') === 'teknisi' ? 'selected' : '' }}>Teknisi (Staf Unit IT)</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator (Akses Penuh)</option>
                        </select>
                        @error('role')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Unit Rumah Sakit -->
                    <div>
                        <label for="unit_id" class="block text-sm font-semibold text-gray-700">
                            Unit / Ruangan Penempatan
                        </label>
                        <select name="unit_id" id="unit_id"
                                class="mt-1 block w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Tidak terikat unit / Bebas</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" {{ (string) old('unit_id') === (string) $unit->id ? 'selected' : '' }}>
                                    {{ $unit->nama }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Wajib untuk pelapor ruangan (IGD, Rawat Inap, dsb).</p>
                        @error('unit_id')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700">
                            Password Akun
                        </label>
                        <input type="password" name="password" id="password"
                               placeholder="Kosongkan untuk menggunakan password default: password123"
                               class="mt-1 block w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">Jika dikosongkan, password otomatis diatur ke <strong class="text-gray-700 font-mono">password123</strong>.</p>
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                            Batal
                        </a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                            Simpan Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

