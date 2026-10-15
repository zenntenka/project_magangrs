<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Dashboard Teknisi IT') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Selamat bertugas, <span class="font-medium text-gray-700">{{ Auth::user()->name }}</span>. Pantau dan tangani kendala IT rumah sakit dengan cepat.
                </p>
            </div>
            <div>
                <a href="{{ route('teknisi.tickets.index') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-medium rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Buka Antrean & Kelola Tiket
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

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

            <!-- Ringkasan Statistik -->
            <div>
                <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 me-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Ringkasan Tugas & Antrean
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Card 1: Tiket Baru Siap Diambil -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-blue-200 p-5 relative">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold uppercase text-blue-600 tracking-wider">Tiket Baru</span>
                            <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                        </div>
                        <div class="mt-2 flex items-baseline justify-between">
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['tiket_baru'] }}</p>
                            <span class="text-xs text-blue-600 font-medium">Antrean umum</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Siap diambil alih</p>
                    </div>

                    <!-- Card 2: Sedang Diproses Saya -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-amber-200 p-5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold uppercase text-amber-600 tracking-wider">Sedang Diproses</span>
                            <span class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </span>
                        </div>
                        <div class="mt-2 flex items-baseline justify-between">
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['diproses'] }}</p>
                            <span class="text-xs text-amber-600 font-medium">Tugas Anda</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Perlu penyelesaian</p>
                    </div>

                    <!-- Card 3: Selesai Ditangani Saya -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-emerald-200 p-5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold uppercase text-emerald-600 tracking-wider">Tiket Selesai</span>
                            <span class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                        </div>
                        <div class="mt-2 flex items-baseline justify-between">
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['selesai'] }}</p>
                            <span class="text-xs text-emerald-600 font-medium">Oleh Anda</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Berhasil dituntaskan</p>
                    </div>

                    <!-- Card 4: Tiket Ditutup -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold uppercase text-gray-600 tracking-wider">Tiket Ditutup</span>
                            <span class="p-2 bg-gray-100 text-gray-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                        </div>
                        <div class="mt-2 flex items-baseline justify-between">
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['ditutup'] }}</p>
                            <span class="text-xs text-gray-600 font-medium">Arsip</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Selesai & terkonfirmasi</p>
                    </div>

                    <!-- Card 5: Total Ditugaskan ke Saya -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-indigo-200 p-5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold uppercase text-indigo-600 tracking-wider">Total Tugas</span>
                            <span class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </span>
                        </div>
                        <div class="mt-2 flex items-baseline justify-between">
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_ditangani'] }}</p>
                            <span class="text-xs text-indigo-600 font-medium">Sepanjang waktu</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Riwayat penanganan</p>
                    </div>
                </div>
            </div>

            <!-- Bagian 2 Kolom: Tiket Aktif Saya & Tiket Baru Masuk -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- Kolom 1: Tiket yang Sedang Diproses Oleh Teknisi Ini -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 flex items-center">
                                <span class="w-2.5 h-2.5 bg-amber-500 rounded-full me-2"></span>
                                Sedang Saya Tangani (Diproses)
                            </h4>
                            <p class="text-xs text-gray-500 mt-0.5">Tiket aktif yang menjadi tanggung jawab Anda saat ini</p>
                        </div>
                        <a href="{{ route('teknisi.tickets.index', ['status' => 'diproses']) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                            Lihat Semua &rarr;
                        </a>
                    </div>

                    @if ($myActiveTickets->isEmpty())
                        <div class="text-center py-10 px-4">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                            </svg>
                            <p class="text-sm font-medium text-gray-700">Tidak ada tiket aktif yang sedang Anda proses</p>
                            <p class="text-xs text-gray-400 mt-0.5">Silakan ambil tiket dari antrean tiket baru di sebelah kanan.</p>
                        </div>
                    @else
                        <div class="divide-y divide-gray-100">
                            @foreach ($myActiveTickets as $t)
                                <div class="p-4 hover:bg-gray-50 transition flex items-center justify-between gap-4">
                                    <div class="space-y-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono text-xs font-bold text-indigo-600">{{ $t->kode }}</span>
                                            @if ($t->prioritas === 'tinggi')
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-red-100 text-red-800">Tinggi</span>
                                            @elseif ($t->prioritas === 'sedang')
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-amber-100 text-amber-800">Sedang</span>
                                            @else
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-slate-100 text-slate-700">Rendah</span>
                                            @endif
                                            <span class="text-xs text-gray-400">&bull;</span>
                                            <span class="text-xs text-gray-500 truncate">{{ $t->unit->nama ?? '-' }}</span>
                                        </div>
                                        <h5 class="text-sm font-semibold text-gray-900 truncate">
                                            {{ $t->judul }}
                                        </h5>
                                        <p class="text-xs text-gray-400">
                                            Pelapor: {{ $t->user->name }} &bull; {{ $t->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <a href="{{ route('teknisi.tickets.show', $t) }}" class="shrink-0 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg transition">
                                        Perbarui &rarr;
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Kolom 2: Tiket Baru yang Belum Diambil -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 flex items-center">
                                <span class="w-2.5 h-2.5 bg-blue-500 rounded-full me-2"></span>
                                Antrean Tiket Baru Masuk
                            </h4>
                            <p class="text-xs text-gray-500 mt-0.5">Tiket belum ada teknisi penanggung jawab</p>
                        </div>
                        <a href="{{ route('teknisi.tickets.index', ['status' => 'baru']) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                            Lihat Semua &rarr;
                        </a>
                    </div>

                    @if ($incomingTickets->isEmpty())
                        <div class="text-center py-10 px-4">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm font-medium text-gray-700">Semua tiket baru sudah diambil</p>
                            <p class="text-xs text-gray-400 mt-0.5">Tidak ada laporan kendala baru yang menunggu penugasan saat ini.</p>
                        </div>
                    @else
                        <div class="divide-y divide-gray-100">
                            @foreach ($incomingTickets as $t)
                                <div class="p-4 hover:bg-gray-50 transition flex items-center justify-between gap-4">
                                    <div class="space-y-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono text-xs font-bold text-indigo-600">{{ $t->kode }}</span>
                                            @if ($t->prioritas === 'tinggi')
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-red-100 text-red-800">Tinggi</span>
                                            @elseif ($t->prioritas === 'sedang')
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-amber-100 text-amber-800">Sedang</span>
                                            @else
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-slate-100 text-slate-700">Rendah</span>
                                            @endif
                                            <span class="text-xs text-gray-400">&bull;</span>
                                            <span class="text-xs text-gray-500 truncate">{{ $t->unit->nama ?? '-' }}</span>
                                        </div>
                                        <h5 class="text-sm font-semibold text-gray-900 truncate">
                                            {{ $t->judul }}
                                        </h5>
                                        <p class="text-xs text-gray-400">
                                            Pelapor: {{ $t->user->name }} &bull; {{ $t->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="shrink-0 flex items-center space-x-2">
                                        <form action="{{ route('teknisi.tickets.take', $t) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                                Ambil Tiket
                                            </button>
                                        </form>
                                        <a href="{{ route('teknisi.tickets.show', $t) }}" class="p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-md transition" title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
</x-app-layout>