<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teknisi;
    protected User $pelapor;
    protected User $otherPelapor;
    protected Unit $unitIGD;
    protected Unit $unitFarmasi;
    protected Category $categoryKomputer;
    protected Category $categorySIMRS;

    protected function setUp(): void
    {
        parent::setUp();

        $this->unitIGD = Unit::create(['nama' => 'Instalasi Gawat Darurat (IGD)']);
        $this->unitFarmasi = Unit::create(['nama' => 'Instalasi Farmasi']);

        $this->categoryKomputer = Category::create(['nama' => 'Komputer']);
        $this->categorySIMRS = Category::create(['nama' => 'SIMRS']);

        $this->admin = User::factory()->create([
            'name' => 'Admin Utama',
            'role' => 'admin',
        ]);

        $this->teknisi = User::factory()->create([
            'name' => 'Budi Teknisi',
            'role' => 'teknisi',
        ]);

        $this->pelapor = User::factory()->create([
            'name' => 'Siti Perawat IGD',
            'role' => 'pelapor',
            'unit_id' => $this->unitIGD->id,
        ]);

        $this->otherPelapor = User::factory()->create([
            'name' => 'Ahmad Farmasi',
            'role' => 'pelapor',
            'unit_id' => $this->unitFarmasi->id,
        ]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect(route('login'));
    }

    public function test_admin_is_redirected_to_admin_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_teknisi_is_redirected_to_teknisi_dashboard(): void
    {
        $response = $this->actingAs($this->teknisi)->get('/dashboard');
        $response->assertRedirect(route('teknisi.dashboard'));
    }

    public function test_pelapor_can_view_dashboard_with_greeting_unit_and_guidance(): void
    {
        $response = $this->actingAs($this->pelapor)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Halo, Siti Perawat IGD');
        $response->assertSee('Instalasi Gawat Darurat (IGD)');
        $response->assertSee('Buat Tiket Baru');
        $response->assertSee(route('tickets.create'));
        $response->assertSee('Total Tiket');
        $response->assertSee('Menunggu');
        $response->assertSee('Diproses');
        $response->assertSee('Selesai');
        $response->assertSee('Panduan Cara Melapor Kendala IT');
        $response->assertSee('Kontak Helpdesk IT');
        $response->assertSee('ext. [isi nomor]');
        $response->assertSee('jam layanan [isi jam]');
    }

    public function test_pelapor_dashboard_displays_correct_stats_and_only_own_tickets(): void
    {
        // 1 tiket baru milik pelapor
        Ticket::create([
            'kode' => 'TCK-20261029-0001',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Komputer IGD Mati Total',
            'deskripsi' => 'CPU tidak menyala',
            'prioritas' => 'tinggi',
            'status' => 'baru',
        ]);

        // 1 tiket diproses milik pelapor
        Ticket::create([
            'kode' => 'TCK-20261029-0002',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categorySIMRS->id,
            'judul' => 'SIMRS Triase Loading Lama',
            'deskripsi' => 'Modul triase berputar terus',
            'prioritas' => 'sedang',
            'status' => 'diproses',
            'teknisi_id' => $this->teknisi->id,
        ]);

        // 1 tiket selesai milik pelapor
        Ticket::create([
            'kode' => 'TCK-20261029-0003',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Monitor IGD Bergaris',
            'deskripsi' => 'Kabel VGA longgar',
            'prioritas' => 'rendah',
            'status' => 'selesai',
            'teknisi_id' => $this->teknisi->id,
            'waktu_selesai' => now(),
        ]);

        // 1 tiket ditutup milik pelapor (dihitung sebagai selesai)
        Ticket::create([
            'kode' => 'TCK-20261029-0004',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categorySIMRS->id,
            'judul' => 'Akun SIMRS Terkunci',
            'deskripsi' => 'Lupa password akun IGD',
            'prioritas' => 'sedang',
            'status' => 'ditutup',
            'teknisi_id' => $this->teknisi->id,
            'waktu_selesai' => now(),
        ]);

        // 1 tiket milik orang lain (tidak boleh masuk hitungan / tampilan)
        Ticket::create([
            'kode' => 'TCK-20261029-0005',
            'user_id' => $this->otherPelapor->id,
            'unit_id' => $this->unitFarmasi->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Komputer Farmasi Rusak Rahasia',
            'deskripsi' => 'Milik user lain',
            'prioritas' => 'tinggi',
            'status' => 'baru',
        ]);

        $response = $this->actingAs($this->pelapor)->get('/dashboard');

        $response->assertOk();

        // Cek data yang dikirim ke view
        $response->assertViewHas('stats', [
            'total' => 4,    // 1 baru + 1 diproses + 1 selesai + 1 ditutup
            'baru' => 1,
            'diproses' => 1,
            'selesai' => 2,  // 1 selesai + 1 ditutup
        ]);

        // Memastikan tiket milik pelapor tampil
        $response->assertSee('Komputer IGD Mati Total');
        $response->assertSee('SIMRS Triase Loading Lama');
        $response->assertSee('Monitor IGD Bergaris');
        $response->assertSee('Akun SIMRS Terkunci');
        $response->assertSee('Budi Teknisi');

        // Memastikan tiket milik orang lain TIDAK tampil
        $response->assertDontSee('Komputer Farmasi Rusak Rahasia');
    }

    public function test_pelapor_dashboard_shows_empty_state_when_no_tickets(): void
    {
        $response = $this->actingAs($this->pelapor)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Belum ada tiket pengaduan');
        $response->assertSee('Buat Tiket Pertama');
    }
}

