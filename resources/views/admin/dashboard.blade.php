<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Dashboard Administrator') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Ringkasan performa dan pemantauan sistem Helpdesk IT Rumah Sakit.
                </p>
            </div>

            <!-- Filter Periode -->
            <div class="inline-flex rounded-lg border border-gray-200 bg-white p-1 shadow-sm">
                <a href="{{ route('admin.dashboard', ['periode' => 'bulan_ini']) }}"
                   class="px-3.5 py-1.5 text-xs font-medium rounded-md transition {{ $periode === 'bulan_ini' ? 'bg-indigo-600 text-white shadow' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    Bulan Ini
                </a>
                <a href="{{ route('admin.dashboard', ['periode' => '30_hari']) }}"
                   class="px-3.5 py-1.5 text-xs font-medium rounded-md transition {{ $periode === '30_hari' ? 'bg-indigo-600 text-white shadow' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    30 Hari Terakhir
                </a>
                <a href="{{ route('admin.dashboard', ['periode' => 'semua']) }}"
                   class="px-3.5 py-1.5 text-xs font-medium rounded-md transition {{ $periode === 'semua' ? 'bg-indigo-600 text-white shadow' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    Semua
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

            <!-- Kartu Statistik Utama -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-5">
                <!-- Total Tiket -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Tiket</span>
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</div>
                        <p class="text-xs text-gray-500 mt-1">Pada periode ini</p>
                    </div>
                </div>

                <!-- Tiket Baru -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Tiket Baru</span>
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['baru']) }}</div>
                        <p class="text-xs text-gray-500 mt-1">Menunggu penugasan</p>
                    </div>
                </div>

                <!-- Diproses -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Diproses</span>
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-2xl font-bold text-amber-600">{{ number_format($stats['diproses']) }}</div>
                        <p class="text-xs text-gray-500 mt-1">Ditangani teknisi</p>
                    </div>
                </div>

                <!-- Selesai -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Selesai</span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-2xl font-bold text-emerald-600">{{ number_format($stats['selesai']) }}</div>
                        <p class="text-xs text-gray-500 mt-1">Telah diselesaikan</p>
                    </div>
                </div>

                <!-- Ditutup -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Ditutup</span>
                        <div class="w-8 h-8 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-2xl font-bold text-gray-600">{{ number_format($stats['ditutup']) }}</div>
                        <p class="text-xs text-gray-500 mt-1">Arsip tiket tuntas</p>
                    </div>
                </div>

                <!-- Rata-rata Waktu Penyelesaian -->
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
                        <div class="text-lg font-bold text-purple-700 leading-snug">{{ $avgFormatted }}</div>
                        <p class="text-xs text-gray-500 mt-1">Lapor s/d Selesai</p>
                    </div>
                </div>
            </div>

            <!-- Bagian Grafik Chart.js (via npm) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Grafik Kategori Tiket -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                                </svg>
                                Distribusi Tiket Berdasarkan Kategori
                            </h3>
                            <span class="text-xs text-gray-400">Chart.js npm</span>
                        </div>
                        <div class="relative h-64 flex items-center justify-center">
                            @if ($categoryData->isEmpty())
                                <div class="text-center text-sm text-gray-400 py-12">
                                    Belum ada data tiket untuk kategori pada periode ini.
                                </div>
                            @else
                                <canvas id="categoryChart"></canvas>
                            @endif
                        </div>
                    </div>
                    @if ($categoryData->isNotEmpty())
                        <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap gap-2 text-xs">
                            @foreach ($categoryData as $cat)
                                <span class="inline-flex items-center px-2 py-1 rounded bg-gray-50 text-gray-700 border border-gray-200">
                                    {{ $cat->nama }}: <strong class="ml-1 text-gray-900">{{ $cat->total }}</strong>
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Grafik Tiket per Unit Rumah Sakit -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Tiket Berdasarkan Unit / Ruangan RS
                            </h3>
                            <span class="text-xs text-gray-400">Chart.js npm</span>
                        </div>
                        <div class="relative h-64 flex items-center justify-center">
                            @if ($unitData->isEmpty())
                                <div class="text-center text-sm text-gray-400 py-12">
                                    Belum ada data tiket untuk unit pada periode ini.
                                </div>
                            @else
                                <canvas id="unitChart"></canvas>
                            @endif
                        </div>
                    </div>
                    @if ($unitData->isNotEmpty())
                        <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap gap-2 text-xs">
                            @foreach ($unitData as $u)
                                <span class="inline-flex items-center px-2 py-1 rounded bg-gray-50 text-gray-700 border border-gray-200">
                                    {{ $u->nama }}: <strong class="ml-1 text-gray-900">{{ $u->total }}</strong>
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            <!-- Tabel 5 Tiket Terbaru -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">5 Tiket Laporan Terbaru</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Daftar laporan pengaduan IT terakhir yang masuk ke sistem.</p>
                    </div>
                    <a href="{{ route('admin.tickets.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition">
                        Lihat Semua Tiket &rarr;
                    </a>
                </div>

                @if ($latestTickets->isEmpty())
                    <div class="text-center py-12 px-4 text-sm text-gray-400">
                        Tidak ada tiket yang terdaftar pada periode ini.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase border-b border-gray-200">
                                <tr>
                                    <th class="px-5 py-3">Kode Tiket</th>
                                    <th class="px-5 py-3">Pelapor & Unit</th>
                                    <th class="px-5 py-3">Kendala & Kategori</th>
                                    <th class="px-5 py-3">Prioritas</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3">Teknisi</th>
                                    <th class="px-5 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($latestTickets as $ticket)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-5 py-3.5 whitespace-nowrap font-mono text-xs font-semibold text-indigo-600">
                                            <a href="{{ route('admin.tickets.show', $ticket) }}" class="hover:underline">
                                                {{ $ticket->kode }}
                                            </a>
                                            <div class="text-[10px] text-gray-400 font-sans font-normal">{{ $ticket->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            <div class="font-medium text-gray-900">{{ $ticket->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $ticket->unit->nama }}</div>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <div class="font-medium text-gray-900 max-w-xs truncate" title="{{ $ticket->judul }}">{{ $ticket->judul }}</div>
                                            <div class="text-xs text-gray-500">{{ $ticket->category->nama }}</div>
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            @if ($ticket->prioritas === 'tinggi')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Tinggi</span>
                                            @elseif ($ticket->prioritas === 'sedang')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Sedang</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Rendah</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            @if ($ticket->status === 'baru')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Baru</span>
                                            @elseif ($ticket->status === 'diproses')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Diproses</span>
                                            @elseif ($ticket->status === 'selesai')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Selesai</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Ditutup</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap text-xs">
                                            @if ($ticket->teknisi)
                                                <span class="text-gray-900 font-medium">{{ $ticket->teknisi->name }}</span>
                                            @else
                                                <span class="text-red-500 italic">Belum ditugaskan</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap text-right text-xs">
                                            <a href="{{ route('admin.tickets.show', $ticket) }}" class="inline-flex items-center px-2.5 py-1 bg-white border border-gray-300 rounded text-xs font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Script Inisialisasi Chart.js (via npm bundle window.Chart) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function renderDashboardCharts() {
                if (typeof window.Chart === 'undefined') {
                    console.warn('Chart.js belum dimuat.');
                    return;
                }

                // 1. Inisialisasi Chart Kategori (Doughnut)
                const categoryCanvas = document.getElementById('categoryChart');
                if (categoryCanvas) {
                    const categoryLabels = @json($categoryData->pluck('nama'));
                    const categoryCounts = @json($categoryData->pluck('total'));

                    if (categoryLabels.length > 0) {
                        new window.Chart(categoryCanvas, {
                            type: 'doughnut',
                            data: {
                                labels: categoryLabels,
                                datasets: [{
                                    data: categoryCounts,
                                    backgroundColor: [
                                        '#6366F1', // Indigo
                                        '#06B6D4', // Cyan
                                        '#F59E0B', // Amber
                                        '#10B981', // Emerald
                                        '#EC4899', // Pink
                                        '#8B5CF6', // Purple
                                        '#64748B'  // Slate
                                    ],
                                    borderWidth: 2,
                                    borderColor: '#ffffff'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            boxWidth: 12,
                                            padding: 15,
                                            font: { size: 11 }
                                        }
                                    }
                                }
                            }
                        });
                    }
                }

                // 2. Inisialisasi Chart Unit (Bar)
                const unitCanvas = document.getElementById('unitChart');
                if (unitCanvas) {
                    const unitLabels = @json($unitData->pluck('nama'));
                    const unitCounts = @json($unitData->pluck('total'));

                    if (unitLabels.length > 0) {
                        new window.Chart(unitCanvas, {
                            type: 'bar',
                            data: {
                                labels: unitLabels,
                                datasets: [{
                                    label: 'Jumlah Tiket',
                                    data: unitCounts,
                                    backgroundColor: '#4F46E5',
                                    borderRadius: 6,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            precision: 0,
                                            font: { size: 11 }
                                        },
                                        grid: {
                                            color: '#F3F4F6'
                                        }
                                    },
                                    x: {
                                        ticks: {
                                            font: { size: 10 }
                                        },
                                        grid: { display: false }
                                    }
                                }
                            }
                        });
                    }
                }
            }

            renderDashboardCharts();
        });
    </script>
</x-app-layout>

