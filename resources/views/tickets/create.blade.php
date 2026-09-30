<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Buat Tiket Pengaduan IT') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Laporkan gangguan hardware, jaringan, printer, atau sistem SIMRS untuk penanganan tim IT.
                </p>
            </div>
            <a href="{{ route('tickets.index') }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 bg-white border border-gray-300 px-3.5 py-2 rounded-lg shadow-sm hover:bg-gray-50 transition">
                <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Tiket Saya
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
                    @csrf

                    <!-- Baris 1: Unit & Kategori -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Unit -->
                        <div>
                            <label for="unit_id" class="block text-sm font-medium text-gray-700">
                                Unit / Lokasi Kendala <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-500 mb-1.5">Lokasi ruangan atau instalasi tempat kendala terjadi</p>
                            <select id="unit_id" name="unit_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('unit_id') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror" required>
                                <option value="" disabled>-- Pilih Unit --</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}" {{ (string) old('unit_id', $defaultUnitId) === (string) $unit->id ? 'selected' : '' }}>
                                        {{ $unit->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unit_id')
                                <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">
                                Kategori Kendala <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-500 mb-1.5">Pilih jenis permasalahan yang dihadapi</p>
                            <select id="category_id" name="category_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('category_id') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror" required>
                                <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>-- Pilih Kategori Kendala --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ (string) old('category_id') === (string) $category->id ? 'selected' : '' }}>
                                        {{ $category->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Baris 2: Judul & Prioritas -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Judul -->
                        <div class="md:col-span-2">
                            <label for="judul" class="block text-sm font-medium text-gray-700">
                                Judul Kendala <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-500 mb-1.5">Ringkasan singkat kendala yang dialami</p>
                            <input type="text" id="judul" name="judul" value="{{ old('judul') }}" placeholder="Contoh: Printer label rekam medis tidak mau cetak" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('judul') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror" required>
                            @error('judul')
                                <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Prioritas -->
                        <div>
                            <label for="prioritas" class="block text-sm font-medium text-gray-700">
                                Tingkat Urgensi <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-500 mb-1.5">Pengaruh terhadap pelayanan</p>
                            <select id="prioritas" name="prioritas" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('prioritas') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror" required>
                                <option value="rendah" {{ old('prioritas') === 'rendah' ? 'selected' : '' }}>Rendah (Tidak mengganggu pelayanan utama)</option>
                                <option value="sedang" {{ old('prioritas', 'sedang') === 'sedang' ? 'selected' : '' }}>Sedang (Mengganggu sebagian alur kerja)</option>
                                <option value="tinggi" {{ old('prioritas') === 'tinggi' ? 'selected' : '' }}>Tinggi (Layanan krusial/pasien terhenti)</option>
                            </select>
                            @error('prioritas')
                                <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Baris 3: Deskripsi -->
                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700">
                            Deskripsi Rinci Kendala <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-gray-500 mb-1.5">Jelaskan kronologi, pesan error, nomor PC / tipe perangkat, dan tindakan yang sudah dicoba.</p>
                        <textarea id="deskripsi" name="deskripsi" rows="5" placeholder="Jelaskan secara detail kendala yang dialami agar teknisi IT dapat menyiapkan peralatan yang tepat..." class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('deskripsi') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror" required>{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Baris 4: Lampiran Gambar (Opsional) -->
                    <div x-data="{ previewUrl: null }">
                        <label for="lampiran" class="block text-sm font-medium text-gray-700">
                            Lampiran Foto / Screenshot <span class="text-gray-400 font-normal">(Opsional)</span>
                        </label>
                        <p class="text-xs text-gray-500 mb-2">Unggah tangkapan layar pesan error atau foto kondisi fisik perangkat (Format: JPG, JPEG, PNG, Maks: 2MB).</p>

                        <div class="mt-1 flex items-center gap-4">
                            <input type="file" id="lampiran" name="lampiran" accept="image/png, image/jpeg, image/jpg"
                                @change="const file = $event.target.files[0]; if (file) { previewUrl = URL.createObjectURL(file); }"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-gray-300 rounded-lg p-1.5">
                        </div>

                        <!-- Preview Image via AlpineJS -->
                        <template x-if="previewUrl">
                            <div class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-200 inline-block">
                                <p class="text-xs font-medium text-gray-600 mb-1.5">Preview Lampiran:</p>
                                <img :src="previewUrl" alt="Preview Lampiran" class="h-32 rounded-md object-contain border border-gray-300 shadow-sm">
                            </div>
                        </template>

                        @error('lampiran')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="pt-4 border-t border-gray-200 flex items-center justify-end space-x-3">
                        <a href="{{ route('tickets.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 hover:text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 shadow-sm transition">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-medium rounded-lg shadow-sm transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Kirim Tiket Pengaduan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
