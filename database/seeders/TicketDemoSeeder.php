<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TicketDemoSeeder extends Seeder
{
    /**
     * Jalankan seeder data demo tiket helpdesk IT.
     * Dapat dipanggil secara terpisah: php artisan db:seed --class=TicketDemoSeeder
     */
    public function run(): void
    {
        // 1. Pastikan data Master Unit dan Kategori tersedia
        $this->ensureMasterData();

        // 2. Buat atau ambil Teknisi (minimal 3 teknisi: 1 lama + 2 baru)
        $technicians = $this->createTechnicians();

        // 3. Buat atau ambil Pelapor dari unit yang berbeda-beda (5-8 pelapor)
        $reporters = $this->createReporters();

        // 4. Siapkan 60 data template tiket dengan distribusi realistis
        $ticketTemplates = $this->getTicketTemplates();

        // 5. Urutkan template berdasarkan hari dibuat (dari 59 hari lalu s/d hari ini)
        // Distribusi: 9 Baru (~15%), 12 Diproses (~20%), 21 Selesai (~35%), 18 Ditutup (~30%)
        $this->seedTickets($ticketTemplates, $reporters, $technicians);

        $this->command?->info('TicketDemoSeeder berhasil menambahkan 60 tiket demo realistis beserta komentar teknisi.');
    }

    /**
     * Memastikan Master Unit dan Kategori terisi jika database masih kosong.
     */
    protected function ensureMasterData(): void
    {
        $units = [
            'Instalasi Gawat Darurat (IGD)',
            'Rawat Inap',
            'Rawat Jalan / Poliklinik',
            'Farmasi',
            'Laboratorium',
            'Radiologi',
            'Rekam Medis',
            'Keuangan & Kasir',
            'IT & SIMRS',
            'Tata Usaha & SDM',
        ];

        foreach ($units as $nama) {
            Unit::firstOrCreate(['nama' => $nama]);
        }

        $categories = [
            'SIMRS',
            'Komputer',
            'Jaringan',
            'Printer',
            'Lainnya',
        ];

        foreach ($categories as $nama) {
            Category::firstOrCreate(['nama' => $nama]);
        }
    }

    /**
     * Menyiapkan daftar teknisi (1 teknisi awal + 2 teknisi tambahan).
     */
    protected function createTechnicians(): array
    {
        $unitIT = Unit::where('nama', 'IT & SIMRS')->first();

        // Teknisi 1 (Default)
        $t1 = User::firstOrCreate(
            ['email' => 'teknisi@rs.local'],
            [
                'name' => 'Budi Santoso (Teknisi IT)',
                'password' => Hash::make('password'),
                'role' => 'teknisi',
                'unit_id' => $unitIT?->id,
                'email_verified_at' => now(),
            ]
        );

        // Teknisi 2 Tambahan
        $t2 = User::firstOrCreate(
            ['email' => 'teknisi.ahmad@rs.local'],
            [
                'name' => 'Ahmad Fauzi (Teknisi Hardware & Jaringan)',
                'password' => Hash::make('password'),
                'role' => 'teknisi',
                'unit_id' => $unitIT?->id,
                'email_verified_at' => now(),
            ]
        );

        // Teknisi 3 Tambahan
        $t3 = User::firstOrCreate(
            ['email' => 'teknisi.rizky@rs.local'],
            [
                'name' => 'Rizky Pratama (Teknisi SIMRS & Aplikasi)',
                'password' => Hash::make('password'),
                'role' => 'teknisi',
                'unit_id' => $unitIT?->id,
                'email_verified_at' => now(),
            ]
        );

        return [$t1, $t2, $t3];
    }

    /**
     * Menyiapkan 8 user pelapor dari unit rumah sakit yang berbeda-beda.
     */
    protected function createReporters(): array
    {
        $unitIGD = Unit::where('nama', 'Instalasi Gawat Darurat (IGD)')->first();
        $unitRanap = Unit::where('nama', 'Rawat Inap')->first();
        $unitRajal = Unit::where('nama', 'Rawat Jalan / Poliklinik')->first();
        $unitFarmasi = Unit::where('nama', 'Farmasi')->first();
        $unitLab = Unit::where('nama', 'Laboratorium')->first();
        $unitRadiologi = Unit::where('nama', 'Radiologi')->first();
        $unitRekam = Unit::where('nama', 'Rekam Medis')->first();
        $unitKasir = Unit::where('nama', 'Keuangan & Kasir')->first();

        $reportersData = [
            [
                'email' => 'pelapor@rs.local',
                'name' => 'Siti Rahma (Perawat IGD)',
                'unit_id' => $unitIGD?->id,
                'unit_name' => 'Instalasi Gawat Darurat (IGD)',
            ],
            [
                'email' => 'pelapor.ranap@rs.local',
                'name' => 'Dewi Lestari (Perawat Rawat Inap)',
                'unit_id' => $unitRanap?->id,
                'unit_name' => 'Rawat Inap',
            ],
            [
                'email' => 'pelapor.rajal@rs.local',
                'name' => 'dr. Hendra Wijaya (Dokter Poliklinik)',
                'unit_id' => $unitRajal?->id,
                'unit_name' => 'Rawat Jalan / Poliklinik',
            ],
            [
                'email' => 'pelapor.farmasi@rs.local',
                'name' => 'Apt. Nurul Hidayah (Apoteker Farmasi)',
                'unit_id' => $unitFarmasi?->id,
                'unit_name' => 'Farmasi',
            ],
            [
                'email' => 'pelapor.lab@rs.local',
                'name' => 'Bambang Kusuma (Analis Laboratorium)',
                'unit_id' => $unitLab?->id,
                'unit_name' => 'Laboratorium',
            ],
            [
                'email' => 'pelapor.radiologi@rs.local',
                'name' => 'Eko Prasetyo (Radiografer Radiologi)',
                'unit_id' => $unitRadiologi?->id,
                'unit_name' => 'Radiologi',
            ],
            [
                'email' => 'pelapor.rekam@rs.local',
                'name' => 'Tri Wahyuni (Staf Rekam Medis)',
                'unit_id' => $unitRekam?->id,
                'unit_name' => 'Rekam Medis',
            ],
            [
                'email' => 'pelapor.kasir@rs.local',
                'name' => 'Fitri Handayani (Kasir & Billing)',
                'unit_id' => $unitKasir?->id,
                'unit_name' => 'Keuangan & Kasir',
            ],
        ];

        $reporters = [];
        foreach ($reportersData as $item) {
            $user = User::firstOrCreate(
                ['email' => $item['email']],
                [
                    'name' => $item['name'],
                    'password' => Hash::make('password'),
                    'role' => 'pelapor',
                    'unit_id' => $item['unit_id'],
                    'email_verified_at' => now(),
                ]
            );
            $reporters[$item['unit_name']] = $user;
        }

        return $reporters;
    }

    /**
     * Membuat dan menyimpan 60 tiket ke database beserta relasinya.
     */
    protected function seedTickets(array $templates, array $reporters, array $technicians): void
    {
        $categories = Category::all()->keyBy('nama');
        $units = Unit::all()->keyBy('nama');

        // Cache nomor urut per tanggal agar kode tiket TCK-YYYYMMDD-XXXX tetap unik dan berurutan
        $dailyCounters = [];

        foreach ($templates as $t) {
            $unitName = $t['unit_name'];
            $unit = $units->get($unitName);
            $category = $categories->get($t['category_name']);
            $reporter = $reporters[$unitName] ?? array_values($reporters)[0];

            // Tentukan tanggal dibuat (created_at) dalam rentang 60 hari terakhir
            $daysAgo = $t['days_ago'];
            $createdAt = Carbon::now()->subDays($daysAgo)->subHours(rand(1, 10))->subMinutes(rand(5, 55));
            $dateStr = $createdAt->format('Ymd');

            // Hitung nomor urut kode tiket unik untuk hari tersebut
            if (!isset($dailyCounters[$dateStr])) {
                $lastTicket = Ticket::where('kode', 'like', "TCK-{$dateStr}-%")
                    ->orderByDesc('kode')
                    ->first();
                $lastNumber = $lastTicket ? (int) substr($lastTicket->kode, -4) : 0;
                $dailyCounters[$dateStr] = $lastNumber;
            }
            $dailyCounters[$dateStr]++;
            $kode = sprintf('TCK-%s-%04d', $dateStr, $dailyCounters[$dateStr]);

            // Tentukan status, teknisi, dan waktu_selesai
            $status = $t['status'];
            $teknisi = null;
            $waktuSelesai = null;

            if ($status === 'baru') {
                $teknisi = null;
                $waktuSelesai = null;
            } elseif ($status === 'diproses') {
                $teknisi = $technicians[array_rand($technicians)];
                $waktuSelesai = null;
            } elseif (in_array($status, ['selesai', 'ditutup'])) {
                $teknisi = $technicians[array_rand($technicians)];

                // waktu_selesai LEBIH LAMA dari created_at (selisih 30 menit s/d 3 hari = 4320 menit)
                // Dipastikan tidak melebihi Carbon::now() saat ini
                $durationMinutes = rand(30, min(4320, max(35, (int) $createdAt->diffInMinutes(now()))));
                $waktuSelesaiCandidate = (clone $createdAt)->addMinutes($durationMinutes);

                if ($waktuSelesaiCandidate->greaterThan(now())) {
                    $waktuSelesai = (clone $createdAt)->addMinutes(max(20, (int) round($createdAt->diffInMinutes(now()) * 0.8)));
                } else {
                    $waktuSelesai = $waktuSelesaiCandidate;
                }
            }

            // Simpan tiket
            $ticket = new Ticket();
            $ticket->kode = $kode;
            $ticket->user_id = $reporter->id;
            $ticket->unit_id = $unit?->id ?? $reporter->unit_id;
            $ticket->category_id = $category?->id ?? 1;
            $ticket->judul = $t['judul'];
            $ticket->deskripsi = $t['deskripsi'];
            $ticket->prioritas = $t['prioritas'];
            $ticket->status = $status;
            $ticket->teknisi_id = $teknisi?->id;
            $ticket->waktu_selesai = $waktuSelesai;
            $ticket->created_at = $createdAt;
            $ticket->updated_at = $waktuSelesai ?? (clone $createdAt)->addMinutes(15);
            $ticket->save();

            // Berikan 1-3 komentar teknisi untuk tiket yang diproses, selesai, atau ditutup
            if (in_array($status, ['diproses', 'selesai', 'ditutup']) && $teknisi) {
                $this->seedComments($ticket, $teknisi, $createdAt, $waktuSelesai, $t['category_name']);
            }
        }
    }

    /**
     * Membuat 1-3 komentar teknisi dengan waktu yang logis di antara created_at dan waktu_selesai.
     */
    protected function seedComments(Ticket $ticket, User $teknisi, Carbon $createdAt, ?Carbon $waktuSelesai, string $categoryName): void
    {
        $commentTemplates = [
            'SIMRS' => [
                'Tiket telah diterima. Segera dikoordinasikan dengan tim programmer SIMRS untuk verifikasi data.',
                'Service aplikasi dan database SIMRS telah direstart. Bridging koneksi sudah dites kembali.',
                'Selesai ditangani. Pengguna telah mencoba input transaksi kembali dan berhasil normal.',
            ],
            'Komputer' => [
                'Laporan keluhan komputer diterima. Teknisi sedang meluncur ke unit kerja membawa alat diagnosa.',
                'Ditemukan kendala pada suplai daya/RAM. Telah dilakukan pembersihan slot dan penggantian thermal paste.',
                'Sistem operasi telah booting normal, pengujian fungsi port dan periferal berjalan lancar.',
            ],
            'Jaringan' => [
                'Tiket telah diambil. Sedang memeriksa koneksi ping gateway switch dan jalur kabel LAN ruangan.',
                'Kabel LAN konektor RJ45 ditemukan kendor/patah, telah dilakukan terminasi ulang kabel cat6.',
                'Koneksi internet dan LAN ruangan sudah stabil kembali dengan throughput 1 Gbps.',
            ],
            'Printer' => [
                'Tiket diterima. Membawa perlengkapan pembersih roller dan cartridge printer pengganti.',
                'Kertas macet berhasil dikeluarkan dari roller, head printer telah dibersihkan secara menyeluruh.',
                'Uji cetak dokumen dan cetak barcode berhasil jelas dan terbaca oleh barcode scanner.',
            ],
            'Lainnya' => [
                'Laporan telah diterima oleh tim helpdesk IT. Teknisi segera memeriksa ke lokasi.',
                'Pemeriksaan perangkat periferal telah selesai dilakukan dan driver perangkat diperbarui.',
                'Perangkat sudah berfungsi normal dan telah dikonfirmasi langsung oleh penanggung jawab ruangan.',
            ],
        ];

        $pool = $commentTemplates[$categoryName] ?? $commentTemplates['Lainnya'];
        $numComments = rand(1, 3);
        $endTime = $waktuSelesai ?? now();
        $totalMinutes = max(10, $createdAt->diffInMinutes($endTime));

        for ($i = 0; $i < $numComments; $i++) {
            $fraction = ($i + 1) / ($numComments + 1);
            $commentTime = (clone $createdAt)->addMinutes((int) round($totalMinutes * $fraction));

            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $teknisi->id,
                'isi' => $pool[$i % count($pool)],
                'created_at' => $commentTime,
                'updated_at' => $commentTime,
            ]);
        }
    }

    /**
     * Koleksi 60 data template masalah IT rumah sakit yang realistis tanpa data pasien.
     * Komposisi:
     * - Baru    : 9 tiket  (~15%)
     * - Diproses: 12 tiket (~20%)
     * - Selesai : 21 tiket (~35%)
     * - Ditutup : 18 tiket (~30%)
     * Total: 60 tiket tersebar dalam 60 hari terakhir.
     */
    protected function getTicketTemplates(): array
    {
        return [
            // ==========================================
            // STATUS: BARU (9 Tiket - 15%) - 0 s/d 4 hari lalu
            // ==========================================
            [
                'status' => 'baru',
                'days_ago' => 0,
                'prioritas' => 'tinggi',
                'category_name' => 'SIMRS',
                'unit_name' => 'Instalasi Gawat Darurat (IGD)',
                'judul' => 'SIMRS modul triase IGD tidak bisa simpan data',
                'deskripsi' => 'Tombol simpan form triase gawat darurat loading berputar terus dan muncul notifikasi timeout database.',
            ],
            [
                'status' => 'baru',
                'days_ago' => 0,
                'prioritas' => 'tinggi',
                'category_name' => 'Jaringan',
                'unit_name' => 'Keuangan & Kasir',
                'judul' => 'Koneksi LAN kasir 1 terputus saat transaksi',
                'deskripsi' => 'Lampu indikator kabel LAN di CPU kasir mati, pembayaran pasien rawat jalan sementara tertahan.',
            ],
            [
                'status' => 'baru',
                'days_ago' => 1,
                'prioritas' => 'sedang',
                'category_name' => 'Printer',
                'unit_name' => 'Farmasi',
                'judul' => 'Printer etiket obat farmasi cetakan terpotong',
                'deskripsi' => 'Kertas label resep obat tidak pas pada batas potong otomatis, sebagian aturan pakai terpotong.',
            ],
            [
                'status' => 'baru',
                'days_ago' => 1,
                'prioritas' => 'tinggi',
                'category_name' => 'Komputer',
                'unit_name' => 'Rawat Jalan / Poliklinik',
                'judul' => 'PC Poli Jantung mati total tidak menyala',
                'deskripsi' => 'Layar monitor dan CPU tidak merespons tombol power sama sekali saat dokter akan mulai praktek.',
            ],
            [
                'status' => 'baru',
                'days_ago' => 2,
                'prioritas' => 'sedang',
                'category_name' => 'SIMRS',
                'unit_name' => 'Laboratorium',
                'judul' => 'Hasil verifikasi LIS tidak otomatis masuk SIMRS',
                'deskripsi' => 'Hasil pemeriksaan laboratorium darah lengkap sudah divalidasi dokter spesialis patologi tapi belum tersinkron ke SIMRS.',
            ],
            [
                'status' => 'baru',
                'days_ago' => 2,
                'prioritas' => 'rendah',
                'category_name' => 'Lainnya',
                'unit_name' => 'Rawat Inap',
                'judul' => 'Barcode scanner di nurse station lantai 3 mati',
                'deskripsi' => 'Scanner USB tidak mengeluarkan sinar laser merah saat ditekan untuk verifikasi barcode obat pasien.',
            ],
            [
                'status' => 'baru',
                'days_ago' => 3,
                'prioritas' => 'sedang',
                'category_name' => 'Printer',
                'unit_name' => 'Rekam Medis',
                'judul' => 'Printer cetak tracer berkas rekam medis paper jam',
                'deskripsi' => 'Kertas formulir peminjaman berkas rekam medis macet di bagian penarik belakang roller printer.',
            ],
            [
                'status' => 'baru',
                'days_ago' => 3,
                'prioritas' => 'tinggi',
                'category_name' => 'SIMRS',
                'unit_name' => 'Radiologi',
                'judul' => 'Error bridging integrasi PACS dengan SIMRS',
                'deskripsi' => 'File foto rontgen thoraks tidak dapat dibuka melalui viewer SIMRS dokter ruang rawat jalan.',
            ],
            [
                'status' => 'baru',
                'days_ago' => 4,
                'prioritas' => 'rendah',
                'category_name' => 'Komputer',
                'unit_name' => 'Tata Usaha & SDM',
                'judul' => 'Mouse optik PC kepegawaian kursor meloncat-loncat',
                'deskripsi' => 'Kursor mouse tidak presisi dan sering freeze di desktop ruang tata usaha bagian kepegawaian.',
            ],

            // ==========================================
            // STATUS: DIPROSES (12 Tiket - 20%) - 1 s/d 14 hari lalu
            // ==========================================
            [
                'status' => 'diproses',
                'days_ago' => 1,
                'prioritas' => 'tinggi',
                'category_name' => 'SIMRS',
                'unit_name' => 'Instalasi Gawat Darurat (IGD)',
                'judul' => 'Gagal bridging V-Claim BPJS pembuatan SEP IGD',
                'deskripsi' => 'Muncul kode kesalahan 500 koneksi service BPJS saat verifikasi nomor rujukan pasien kecelakaan.',
            ],
            [
                'status' => 'diproses',
                'days_ago' => 2,
                'prioritas' => 'sedang',
                'category_name' => 'Printer',
                'unit_name' => 'Rawat Inap',
                'judul' => 'Printer thermal cetak gelang pasien hasil buram',
                'deskripsi' => 'Barcode pada gelang identitas pasien rawat inap gedung Anggrek tidak bisa dibaca scanner mobile.',
            ],
            [
                'status' => 'diproses',
                'days_ago' => 3,
                'prioritas' => 'tinggi',
                'category_name' => 'Jaringan',
                'unit_name' => 'Laboratorium',
                'judul' => 'Access Point Wifi ruang analisa darah sering putus',
                'deskripsi' => 'Tablet analis laboratorium sering kehilangan koneksi saat input keliling hasil pemeriksaan.',
            ],
            [
                'status' => 'diproses',
                'days_ago' => 4,
                'prioritas' => 'sedang',
                'category_name' => 'Komputer',
                'unit_name' => 'Farmasi',
                'judul' => 'Komputer racikan obat lambat dan sering not responding',
                'deskripsi' => 'Aplikasi resep elektronik membutuhkan waktu hingga 2 menit setiap membuka form telaah obat.',
            ],
            [
                'status' => 'diproses',
                'days_ago' => 5,
                'prioritas' => 'sedang',
                'category_name' => 'SIMRS',
                'unit_name' => 'Rawat Jalan / Poliklinik',
                'judul' => 'Layar display antrean poli anak tidak bergerak',
                'deskripsi' => 'Nomor antrean di TV ruang tunggu poli anak tidak sinkron saat dipanggil dari aplikasi dokter.',
            ],
            [
                'status' => 'diproses',
                'days_ago' => 6,
                'prioritas' => 'rendah',
                'category_name' => 'Lainnya',
                'unit_name' => 'Rekam Medis',
                'judul' => 'Scanner arsip dokumen rekam medis hasil miring',
                'deskripsi' => 'Roller pemindaian dokumen riwayat medis lembar asesmen awal menarik kertas secara miring.',
            ],
            [
                'status' => 'diproses',
                'days_ago' => 7,
                'prioritas' => 'tinggi',
                'category_name' => 'Komputer',
                'unit_name' => 'Radiologi',
                'judul' => 'PC workstation CT-Scan bluescreen berulang (BSOD)',
                'deskripsi' => 'Muncul layar biru kode IRQL_NOT_LESS_OR_EQUAL saat melakukan rendering rekonstruksi gambar 3D.',
            ],
            [
                'status' => 'diproses',
                'days_ago' => 8,
                'prioritas' => 'sedang',
                'category_name' => 'Jaringan',
                'unit_name' => 'Keuangan & Kasir',
                'judul' => 'Kabel LAN kasir rawat inap longgar sering disconnected',
                'deskripsi' => 'Pengunci konektor RJ45 patah sehingga kabel sering terlepas saat CPU tersenggol.',
            ],
            [
                'status' => 'diproses',
                'days_ago' => 10,
                'prioritas' => 'sedang',
                'category_name' => 'Printer',
                'unit_name' => 'Instalasi Gawat Darurat (IGD)',
                'judul' => 'Printer cetak lembar rujukan IGD bergaris hitam pekat',
                'deskripsi' => 'Hasil cetak dokumen resume medis rujukan terdapat garis tebal vertikal di sisi kanan halaman.',
            ],
            [
                'status' => 'diproses',
                'days_ago' => 11,
                'prioritas' => 'rendah',
                'category_name' => 'SIMRS',
                'unit_name' => 'Rawat Inap',
                'judul' => 'Akun perawat baru belum bisa akses modul sensus harian',
                'deskripsi' => 'Hak akses peran perawat pengganti di ruang Melati belum memiliki wewenang input mutasi ranap.',
            ],
            [
                'status' => 'diproses',
                'days_ago' => 12,
                'prioritas' => 'tinggi',
                'category_name' => 'Komputer',
                'unit_name' => 'Rawat Jalan / Poliklinik',
                'judul' => 'Monitor PC Poli Gigi kedip-kedip lalu blank hitam',
                'deskripsi' => 'Layar monitor mati tiba-tiba saat dokter sedang menjelaskan rencana tindakan perawatan.',
            ],
            [
                'status' => 'diproses',
                'days_ago' => 14,
                'prioritas' => 'sedang',
                'category_name' => 'Lainnya',
                'unit_name' => 'Tata Usaha & SDM',
                'judul' => 'Mesin fingerprint absensi pintu timur tidak merekam sidik jari',
                'deskripsi' => 'Karyawan shift pagi gagal melakukan absensi mandiri, layar mesin tidak merespons sentuhan.',
            ],

            // ==========================================
            // STATUS: SELESAI (21 Tiket - 35%) - 3 s/d 42 hari lalu
            // ==========================================
            [
                'status' => 'selesai',
                'days_ago' => 3,
                'prioritas' => 'tinggi',
                'category_name' => 'SIMRS',
                'unit_name' => 'Farmasi',
                'judul' => 'Modul e-Resep depo farmasi rawat jalan error perhitungan dosis',
                'deskripsi' => 'Perhitungan stok obat sirup tidak berkurang otomatis pada transaksi resep dokter spesialis anak.',
            ],
            [
                'status' => 'selesai',
                'days_ago' => 5,
                'prioritas' => 'sedang',
                'category_name' => 'Printer',
                'unit_name' => 'Keuangan & Kasir',
                'judul' => 'Printer cetak kuitansi rincian biaya rawat inap habis ribbon',
                'deskripsi' => 'Pita ribbon printer dot matrix kasir tipis dan cetakan angka nominal pembayaran hampir tidak terbaca.',
            ],
            [
                'status' => 'selesai',
                'days_ago' => 6,
                'prioritas' => 'tinggi',
                'category_name' => 'Jaringan',
                'unit_name' => 'Rawat Jalan / Poliklinik',
                'judul' => 'Switch hub lantai 2 poliklinik hang semua PC offline',
                'deskripsi' => 'Semua poli spesialis di lantai 2 kehilangan koneksi ke server pusat SIMRS serentak.',
            ],
            [
                'status' => 'selesai',
                'days_ago' => 8,
                'prioritas' => 'sedang',
                'category_name' => 'Komputer',
                'unit_name' => 'Instalasi Gawat Darurat (IGD)',
                'judul' => 'Keyboard PC triage IGD tombol Enter dan Spacebar macet',
                'deskripsi' => 'Cairan alkohol pembersih menetes ke sela-sela tombol keyboard perawat saat desinfeksi.',
            ],
            [
                'status' => 'selesai',
                'days_ago' => 9,
                'prioritas' => 'tinggi',
                'category_name' => 'SIMRS',
                'unit_name' => 'Rekam Medis',
                'judul' => 'Menu pengembalian berkas rekam medis klaim BPJS terkunci',
                'deskripsi' => 'Staf koding BPJS tidak bisa mengubah status penyerahan dokumen klaim bulan berjalan.',
            ],
            [
                'status' => 'selesai',
                'days_ago' => 11,
                'prioritas' => 'rendah',
                'category_name' => 'Lainnya',
                'unit_name' => 'Laboratorium',
                'judul' => 'UPS cadangan PC alat hematology analyzer berbunyi beeping',
                'deskripsi' => 'Baterai backup UPS mengeluarkan alarm indikator kelebihan beban saat alat instrumen running.',
            ],
            [
                'status' => 'selesai',
                'days_ago' => 13,
                'prioritas' => 'tinggi',
                'category_name' => 'Komputer',
                'unit_name' => 'Radiologi',
                'judul' => 'Kipas CPU ruang USG bunyi bising dan suhu prosesor panas',
                'deskripsi' => 'PC mengalami thermal throttling sehingga aplikasi olah citra radiologi sangat patah-patah.',
            ],
            [
                'status' => 'selesai',
                'days_ago' => 15,
                'prioritas' => 'sedang',
                'category_name' => 'Printer',
                'unit_name' => 'Rawat Inap',
                'judul' => 'Printer LaserJet nurse station lantai 4 menolak cetak PDF',
                'deskripsi' => 'Status antrean cetak dokumen resume asuhan keperawatan terhenti pada status spooling.',
            ],
            [
                'status' => 'selesai',
                'days_ago' => 17,
                'prioritas' => 'tinggi',
                'category_name' => 'Jaringan',
                'unit_name' => 'Farmasi',
                'judul' => 'Koneksi jaringan gudang farmasi basement drop total',
                'deskripsi' => 'Petugas gudang tidak bisa menginput penerimaan pesanan obat dari distributor PBF.',
            ],
            [
                'status' => 'selesai',
                'days_ago' => 19,
                'prioritas' => 'sedang',
                'category_name' => 'SIMRS',
                'unit_name' => 'Instalasi Gawat Darurat (IGD)',
                'judul' => 'Gagal input data penjamin Jasa Raharja pasien IGD',
                'deskripsi' => 'Kolom nomor laporan polisi dan tanggal kejadian tidak bisa disimpan di master pasien kecelakaan.',
            ],
            [
                'status' => 'selesai',
                'days_ago' => 21,
                'prioritas' => 'rendah',
                'category_name' => 'Komputer',
                'unit_name' => 'Tata Usaha & SDM',
                'judul' => 'Setting resolusi layar monitor ruang keuangan berubah gepeng',
                'deskripsi' => 'Tampilan spreadsheet Excel terdistorsi setelah pembaruan otomatis driver grafis Windows.',
            ],
            [
                'status' => 'selesai',
                'days_ago' => 23,
                'prioritas' => 'tinggi',
                'category_name' => 'SIMRS',
                'unit_name' => 'Rawat Jalan / Poliklinik',
                'judul' => 'Fitur antrean online Mobile JKN tidak memanggil pasien',
                'deskripsi' => 'Status check-in peserta BPJS di kiosk pendaftaran mandiri tidak meneruskan data ke dokter poli.',
            ],
            [
                'status' => 'selesai',
                'days_ago' => 25,
                'prioritas' => 'sedang',
                'category_name' => 'Printer',
                'unit_name' => 'Laboratorium',
                'judul' => 'Printer barcode tabung darah sering double feed label',
                'deskripsi' => 'Dua label stiker tabung spesimen keluar bersamaan menyebabkan pemborosan stiker reagen.',
            ],
            [
                'status' => 'selesai',
                'days_ago' => 27,
                'prioritas' => 'tinggi',
                'category_name' => 'Jaringan',
                'unit_name' => 'Radiologi',
                'judul' => 'Koneksi transfer file DICOM dari MRI ke server PACS gagal',
                'deskripsi' => 'Proses pengiriman hasil scan otak berukuran 800 MB gagal di tengah jalan karena socket timeout.',
            ],
            [
                'status' => 'selesai',
                'days_ago' => 29,
                'prioritas' => 'sedang',
                'category_name' => 'Komputer',
                'unit_name' => 'Rawat Inap',
                'judul' => 'Komputer dokter bangsal Flamboyan lemot saat pergantian shift',
                'deskripsi' => 'Banyak file cache temporary yang menumpuk di drive C sistem menyebabkan kapasitas penuh.',
            ],
            [
                'status' => 'selesai',
                'days_ago' => 31,
                'prioritas' => 'rendah',
                'category_name' => 'Lainnya',
                'unit_name' => 'Keuangan & Kasir',
                'judul' => 'Cash drawer laci uang kasir 2 macet tidak mau terbuka',
                'deskripsi' => 'Kabel pemicu RJ11 dari printer kasir ke laci uang kasir lepas di bagian soket bawah meja.',
            ],
            [
                'status' => 'selesai',
                'days_ago' => 33,
                'prioritas' => 'tinggi',
                'category_name' => 'SIMRS',
                'unit_name' => 'Instalasi Gawat Darurat (IGD)',
                'judul' => 'Sistem alert alergi obat pasien tidak memunculkan popup merah',
                'deskripsi' => 'Peringatan riwayat alergi antibiotik penisilin tidak muncul saat perawat IGD input resep.',
            ],
            [
                'status' => 'selesai',
                'days_ago' => 36,
                'prioritas' => 'sedang',
                'category_name' => 'Printer',
                'unit_name' => 'Rekam Medis',
                'judul' => 'Toner printer cetak dokumen rekam elektronik habis',
                'deskripsi' => 'Lampu indikator toner cartridge berwarna oranye berkedip dan cetakan formulir menjadi pucat.',
            ],
            [
                'status' => 'selesai',
                'days_ago' => 38,
                'prioritas' => 'tinggi',
                'category_name' => 'Jaringan',
                'unit_name' => 'Keuangan & Kasir',
                'judul' => 'IP conflict di jaringan kasir rawat jalan dengan mesin EDC',
                'deskripsi' => 'Komputer kasir utama terputus koneksi karena alamat IP statis terduplikasi dengan terminal EDC bank.',
            ],
            [
                'status' => 'selesai',
                'days_ago' => 40,
                'prioritas' => 'sedang',
                'category_name' => 'Komputer',
                'unit_name' => 'Farmasi',
                'judul' => 'Aplikasi browser SIMRS sering reload mendadak di PC depo',
                'deskripsi' => 'Ekstensi browser lama menyebabkan memory leak dan crash saat membuka banyak tab obat.',
            ],
            [
                'status' => 'selesai',
                'days_ago' => 42,
                'prioritas' => 'rendah',
                'category_name' => 'Lainnya',
                'unit_name' => 'Tata Usaha & SDM',
                'judul' => 'Kabel HDMI proyektor ruang rapat komite medis rusak',
                'deskripsi' => 'Layar proyektor menampilkan warna hijau dan bergaris-garis saat presentasi audit medis.',
            ],

            // ==========================================
            // STATUS: DITUTUP (18 Tiket - 30%) - 10 s/d 59 hari lalu
            // ==========================================
            [
                'status' => 'ditutup',
                'days_ago' => 10,
                'prioritas' => 'tinggi',
                'category_name' => 'SIMRS',
                'unit_name' => 'Laboratorium',
                'judul' => 'Gagal validasi nilai kritis elektrolit pasien IGD',
                'deskripsi' => 'Nilai kritis kalium darah pasien tidak mengirim SMS/notifikasi telegram darurat ke dokter DPJP.',
            ],
            [
                'status' => 'ditutup',
                'days_ago' => 12,
                'prioritas' => 'sedang',
                'category_name' => 'Komputer',
                'unit_name' => 'Rawat Jalan / Poliklinik',
                'judul' => 'Power supply PC Poli Mata korsleting dan tercium bau hangus',
                'deskripsi' => 'Komputer mati mendadak disertai percikan kecil di bagian belakang casing CPU poli spesialis mata.',
            ],
            [
                'status' => 'ditutup',
                'days_ago' => 15,
                'prioritas' => 'tinggi',
                'category_name' => 'Jaringan',
                'unit_name' => 'Instalasi Gawat Darurat (IGD)',
                'judul' => 'Koneksi internet backup GSM failover tidak aktif saat FO putus',
                'deskripsi' => 'Router mikrotik tidak otomatis mengalihkan koneksi ke modem GSM backup saat kabel optik utama terganggu.',
            ],
            [
                'status' => 'ditutup',
                'days_ago' => 18,
                'prioritas' => 'sedang',
                'category_name' => 'Printer',
                'unit_name' => 'Farmasi',
                'judul' => 'Sensor kertas printer thermal etiket sirup kotor',
                'deskripsi' => 'Printer memunculkan status error out of paper padahal roll stiker etiket masih penuh.',
            ],
            [
                'status' => 'ditutup',
                'days_ago' => 22,
                'prioritas' => 'rendah',
                'category_name' => 'Lainnya',
                'unit_name' => 'Rawat Inap',
                'judul' => 'Speaker panggilan bel perawat ruang VIP 1 volume kecil',
                'deskripsi' => 'Suara interkom dari kamar pasien VIP 1 ke nurse station terdengar sangat pelan.',
            ],
            [
                'status' => 'ditutup',
                'days_ago' => 26,
                'prioritas' => 'tinggi',
                'category_name' => 'SIMRS',
                'unit_name' => 'Keuangan & Kasir',
                'judul' => 'Selisih perhitungan diskon tarif kamar rawat inap BPJS',
                'deskripsi' => 'Sistem tidak menerapkan aturan plafon kelas 1 saat penghitungan selisih bayar naik kelas rawat.',
            ],
            [
                'status' => 'ditutup',
                'days_ago' => 30,
                'prioritas' => 'sedang',
                'category_name' => 'Komputer',
                'unit_name' => 'Rekam Medis',
                'judul' => 'Hardisk PC koding rekam medis mengeluarkan bunyi clicking',
                'deskripsi' => 'Hard drive mekanis berbunyi cetuk-cetuk dan Windows beberapa kali memunculkan peringatan bad sector.',
            ],
            [
                'status' => 'ditutup',
                'days_ago' => 34,
                'prioritas' => 'tinggi',
                'category_name' => 'Jaringan',
                'unit_name' => 'Radiologi',
                'judul' => 'Port LAN pada workstation radiologi 2 tidak dapat IP DHCP',
                'deskripsi' => 'Komputer mendapat IP 169.254.x.x (APIPA) sehingga tidak dapat menjangkau gateway RS.',
            ],
            [
                'status' => 'ditutup',
                'days_ago' => 37,
                'prioritas' => 'sedang',
                'category_name' => 'Printer',
                'unit_name' => 'Instalasi Gawat Darurat (IGD)',
                'judul' => 'Roller karet printer resep IGD aus dan licin',
                'deskripsi' => 'Kertas resep sering miring saat ditarik dan tinta tercoreng di sudut kiri bawah formulir.',
            ],
            [
                'status' => 'ditutup',
                'days_ago' => 41,
                'prioritas' => 'rendah',
                'category_name' => 'Lainnya',
                'unit_name' => 'Tata Usaha & SDM',
                'judul' => 'Webcam pendaftaran pegawai baru buram dan gelap',
                'deskripsi' => 'Lensa kamera eksternal USB berdebu dan pengaturan exposure otomatis macet pada setting tergelap.',
            ],
            [
                'status' => 'ditutup',
                'days_ago' => 44,
                'prioritas' => 'tinggi',
                'category_name' => 'SIMRS',
                'unit_name' => 'Rawat Jalan / Poliklinik',
                'judul' => 'Jadwal dokter praktek poli paru tidak muncul di antrean online',
                'deskripsi' => 'Hari dan jam praktek dokter spesialis paru hilang dari daftar booking aplikasi Mobile JKN.',
            ],
            [
                'status' => 'ditutup',
                'days_ago' => 47,
                'prioritas' => 'sedang',
                'category_name' => 'Komputer',
                'unit_name' => 'Laboratorium',
                'judul' => 'Layar monitor PC analis kimia klinik bergaris vertikal ungu',
                'deskripsi' => 'Kabel VGA longgar dan jarum pin konektor ada yang bengkok setelah pembersihan meja lab.',
            ],
            [
                'status' => 'ditutup',
                'days_ago' => 50,
                'prioritas' => 'tinggi',
                'category_name' => 'Jaringan',
                'unit_name' => 'Rawat Inap',
                'judul' => 'Sinyal Wifi di nurse station lantai 5 gedung barat hilang',
                'deskripsi' => 'Perawat tidak bisa mengoperasikan troli EMR mobile karena access point mati mendadak.',
            ],
            [
                'status' => 'ditutup',
                'days_ago' => 52,
                'prioritas' => 'sedang',
                'category_name' => 'Printer',
                'unit_name' => 'Keuangan & Kasir',
                'judul' => 'Printer thermal bukti pembayaran kasir tidak memotong kertas',
                'deskripsi' => 'Pisau auto cutter printer macet akibat sisa sobekan struk terselip di mekanisme pisau.',
            ],
            [
                'status' => 'ditutup',
                'days_ago' => 54,
                'prioritas' => 'rendah',
                'category_name' => 'SIMRS',
                'unit_name' => 'Instalasi Gawat Darurat (IGD)',
                'judul' => 'Penambahan item master tindakan jahit luka 5 simpul baru',
                'deskripsi' => 'Permintaan penambahan kode tarif tindakan IGD untuk jahit luka tindakan gawat darurat bedah minor.',
            ],
            [
                'status' => 'ditutup',
                'days_ago' => 56,
                'prioritas' => 'tinggi',
                'category_name' => 'Komputer',
                'unit_name' => 'Farmasi',
                'judul' => 'Komputer gudang obat infeksius mati kipas processor macet',
                'deskripsi' => 'Debu tebal menghalangi putaran pendingin CPU menyebabkan sistem mati otomatis (overheat shutdown).',
            ],
            [
                'status' => 'ditutup',
                'days_ago' => 57,
                'prioritas' => 'sedang',
                'category_name' => 'Jaringan',
                'unit_name' => 'Rekam Medis',
                'judul' => 'Kabel LAN di rak filing rekam medis terkelupas tersangkut troli',
                'deskripsi' => 'Pelindung kabel LAN terkelupas sehingga koneksi PC scanner arsip sering putus nyambung.',
            ],
            [
                'status' => 'ditutup',
                'days_ago' => 59,
                'prioritas' => 'tinggi',
                'category_name' => 'SIMRS',
                'unit_name' => 'Radiologi',
                'judul' => 'Bridging pemeriksaan radiologi gagal lempar data ke billing kasir',
                'deskripsi' => 'Tindakan foto rontgen thoraks tidak tercatat di rekap tagihan pasien saat hendak checkout pembayaran.',
            ],
        ];
    }
}

