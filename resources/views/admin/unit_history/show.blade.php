<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Riwayat Unit: {{ $unit->nama }}</h2>
                <p class="text-sm text-gray-500 mt-1">Rekap tiket, distribusi kategori, dan daftar tiket unit ini.</p>
            </div>
            <a href="{{ route('admin.unit-riwayat.index') }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Kartu ringkasan --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $unitStats['total'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <p class="text-xs font-semibold text-blue-600 uppercase">Baru</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $unitStats['baru'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <p class="text-xs font-semibold text-amber-600 uppercase">Diproses</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ $unitStats['diproses'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <p class="text-xs font-semibold text-emerald-600 uppercase">Selesai</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $unitStats['selesai'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Ditutup</p>
                    <p class="text-2xl font-bold text-gray-700 mt-1">{{ $unitStats['ditutup'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <p class="text-xs font-semibold text-purple-600 uppercase">Rata-rata Waktu</p>
                    <p class="text-sm font-bold text-purple-700 mt-2">{{ $unitStats['avg_duration'] }}</p>
                </div>
            </div>

            {{-- Grafik per kategori --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4">Tiket per Kategori</h3>
                @if ($categoryChartData->isEmpty())
                    <p class="text-sm text-gray-500">Belum ada data.</p>
                @else
                    <div class="relative h-64">
                        <canvas id="categoryChart"></canvas>
                    </div>
                @endif
            </div>

            {{-- Filter --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <form method="GET" action="{{ route('admin.unit-riwayat.show', $unit) }}">
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Periode</label>
                            <select name="periode" class="w-full rounded-lg border-gray-300 text-sm">
                                <option value="semua" @selected($periode === 'semua')>Semua</option>
                                <option value="bulan_ini" @selected($periode === 'bulan_ini')>Bulan ini</option>
                                <option value="30_hari" @selected($periode === '30_hari')>30 hari terakhir</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}"
                                   class="w-full rounded-lg border-gray-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}"
                                   class="w-full rounded-lg border-gray-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Status</label>
                            <select name="status" class="w-full rounded-lg border-gray-300 text-sm">
                                <option value="">Semua Status</option>
                                @foreach (['baru' => 'Baru', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditutup' => 'Ditutup'] as $val => $label)
                                    <option value="{{ $val }}" @selected($status === $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Kategori</label>
                            <select name="category_id" class="w-full rounded-lg border-gray-300 text-sm">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected((string) $categoryId === (string) $cat->id)>{{ $cat->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-4">
                        <p class="text-xs text-gray-500">Jika tanggal mulai/selesai diisi, pilihan periode diabaikan.</p>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.unit-riwayat.show', $unit) }}"
                               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium transition">Reset</a>
                            <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-medium transition">Terapkan Filter</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Tabel tiket --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr class="text-left text-xs font-semibold text-gray-500 uppercase">
                                <th class="px-4 py-3">Kode & Tanggal</th>
                                <th class="px-4 py-3">Pelapor</th>
                                <th class="px-4 py-3">Judul & Kategori</th>
                                <th class="px-4 py-3">Prioritas</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Teknisi</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($tickets as $ticket)
                                @php
                                    $prioClass = match ($ticket->prioritas) {
                                        'tinggi' => 'bg-red-100 text-red-700',
                                        'sedang' => 'bg-yellow-100 text-yellow-700',
                                        default => 'bg-gray-100 text-gray-600',
                                    };
                                    $statusClass = match ($ticket->status) {
                                        'baru' => 'bg-blue-100 text-blue-700',
                                        'diproses' => 'bg-amber-100 text-amber-700',
                                        'selesai' => 'bg-emerald-100 text-emerald-700',
                                        default => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <p class="font-mono text-xs text-indigo-600">{{ $ticket->kode }}</p>
                                        <p class="text-xs text-gray-400">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $ticket->user->name ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-800">{{ $ticket->judul }}</p>
                                        <p class="text-xs text-gray-500">{{ $ticket->category->nama ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $prioClass }}">{{ ucfirst($ticket->prioritas) }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">{{ ucfirst($ticket->status) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $ticket->teknisi->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        @if (Route::has('admin.tickets.show'))
                                            <a href="{{ route('admin.tickets.show', $ticket) }}"
                                               class="px-3 py-1 border border-gray-300 rounded text-xs text-gray-700 hover:bg-gray-50">Detail</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                                        Tidak ada tiket yang sesuai filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($tickets->hasPages())
                    <div class="px-4 py-3 border-t border-gray-100">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($categoryChartData->isNotEmpty())
        <script>
            // Data dikirim dari PHP ke JavaScript lewat fungsi json Blade, lalu digambar setelah Chart.js siap.
            window.addEventListener('load', function () {
                var el = document.getElementById('categoryChart');
                if (!el || !window.Chart) { return; }
                new window.Chart(el, {
                    type: 'bar',
                    data: {
                        labels: @json($categoryChartData->pluck('nama')),
                        datasets: [{
                            label: 'Jumlah tiket',
                            data: @json($categoryChartData->pluck('total')),
                            backgroundColor: '#6366f1',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                    }
                });
            });
        </script>
    @endif
</x-app-layout>
