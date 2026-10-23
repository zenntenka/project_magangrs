<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teknisi;
    protected User $pelapor;
    protected Unit $unitIGD;
    protected Unit $unitFarmasi;
    protected Category $categoryKomputer;
    protected Category $categoryJaringan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->unitIGD = Unit::create(['nama' => 'Instalasi Gawat Darurat (IGD)']);
        $this->unitFarmasi = Unit::create(['nama' => 'Instalasi Farmasi']);

        $this->categoryKomputer = Category::create(['nama' => 'Komputer']);
        $this->categoryJaringan = Category::create(['nama' => 'Jaringan']);

        $this->admin = User::factory()->create([
            'name' => 'Admin Utama',
            'email' => 'admin.report@rs.local',
            'role' => 'admin',
        ]);

        $this->teknisi = User::factory()->create([
            'name' => 'Budi Teknisi',
            'email' => 'teknisi.report@rs.local',
            'role' => 'teknisi',
        ]);

        $this->pelapor = User::factory()->create([
            'name' => 'Perawat Siti',
            'email' => 'pelapor.report@rs.local',
            'role' => 'pelapor',
            'unit_id' => $this->unitIGD->id,
        ]);
    }

    public function test_guest_cannot_access_report_routes(): void
    {
        $response = $this->get(route('admin.laporan.index'));
        $response->assertRedirect(route('login'));

        $responsePdf = $this->get(route('admin.laporan.pdf'));
        $responsePdf->assertRedirect(route('login'));

        $responseExcel = $this->get(route('admin.laporan.excel'));
        $responseExcel->assertRedirect(route('login'));
    }

    public function test_pelapor_and_teknisi_cannot_access_report_routes_returns_403(): void
    {
        $responsePelapor = $this->actingAs($this->pelapor)->get(route('admin.laporan.index'));
        $responsePelapor->assertForbidden();

        $responseTeknisi = $this->actingAs($this->teknisi)->get(route('admin.laporan.index'));
        $responseTeknisi->assertForbidden();
    }

    public function test_admin_can_view_report_page_with_summary_and_filters(): void
    {
        // Buat tiket 1 (Selesai di IGD)
        Ticket::create([
            'kode' => 'TCK-20261020-0001',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Komputer IGD Rusak',
            'deskripsi' => 'Mati total',
            'prioritas' => 'tinggi',
            'status' => 'selesai',
            'teknisi_id' => $this->teknisi->id,
            'created_at' => now()->subHours(3),
            'waktu_selesai' => now(),
        ]);

        // Buat tiket 2 (Baru di Farmasi)
        Ticket::create([
            'kode' => 'TCK-20261020-0002',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitFarmasi->id,
            'category_id' => $this->categoryJaringan->id,
            'judul' => 'Jaringan Farmasi Putus',
            'deskripsi' => 'Tidak bisa bridging BPJS',
            'prioritas' => 'sedang',
            'status' => 'baru',
            'teknisi_id' => null,
            'created_at' => now()->subDay(),
        ]);

        // Akses halaman tanpa filter
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.index'));
        $response->assertOk();
        $response->assertSee('Laporan & Rekapitulasi Tiket IT');
        $response->assertSee('Komputer IGD Rusak');
        $response->assertSee('Jaringan Farmasi Putus');
        $response->assertSee('Ekspor PDF');
        $response->assertSee('Ekspor Excel');

        // Filter berdasarkan unit IGD
        $responseFilterUnit = $this->actingAs($this->admin)->get(route('admin.laporan.index', [
            'unit_id' => $this->unitIGD->id,
        ]));
        $responseFilterUnit->assertSee('Komputer IGD Rusak');
        $responseFilterUnit->assertDontSee('Jaringan Farmasi Putus');

        // Filter berdasarkan status baru
        $responseFilterStatus = $this->actingAs($this->admin)->get(route('admin.laporan.index', [
            'status' => 'baru',
        ]));
        $responseFilterStatus->assertSee('Jaringan Farmasi Putus');
        $responseFilterStatus->assertDontSee('Komputer IGD Rusak');
    }

    public function test_admin_can_export_report_to_pdf(): void
    {
        Ticket::create([
            'kode' => 'TCK-20261020-0001',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Ekspor PDF Tiket',
            'deskripsi' => 'Uji cetak pdf',
            'prioritas' => 'tinggi',
            'status' => 'selesai',
            'teknisi_id' => $this->teknisi->id,
            'created_at' => now()->subHours(1),
            'waktu_selesai' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.laporan.pdf', [
            'status' => 'selesai',
            'unit_id' => $this->unitIGD->id,
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertNotEmpty($response->getContent());
    }

    public function test_admin_can_export_report_to_excel(): void
    {
        Ticket::create([
            'kode' => 'TCK-20261020-0001',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Ekspor Excel Tiket',
            'deskripsi' => 'Uji ekspor excel',
            'prioritas' => 'sedang',
            'status' => 'baru',
            'teknisi_id' => null,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.laporan.excel', [
            'status' => 'baru',
        ]));

        $response->assertOk();
        // Memastikan respons download file spreadsheet/excel atau csv
        $this->assertTrue(
            str_contains($response->headers->get('content-type'), 'spreadsheet') ||
            str_contains($response->headers->get('content-type'), 'excel') ||
            str_contains($response->headers->get('content-type'), 'octet-stream') ||
            str_contains($response->headers->get('content-type'), 'text/csv')
        );
    }

    public function test_report_menu_is_visible_in_admin_navigation(): void
    {
        $responseAdmin = $this->actingAs($this->admin)->get(route('admin.laporan.index'));
        $responseAdmin->assertSee('Laporan');

        $responseTeknisi = $this->actingAs($this->teknisi)->get(route('teknisi.dashboard'));
        $responseTeknisi->assertDontSee(route('admin.laporan.index'));

        $responsePelapor = $this->actingAs($this->pelapor)->get(route('dashboard'));
        $responsePelapor->assertDontSee(route('admin.laporan.index'));
    }

    public function test_ticket_duration_is_formatted_in_indonesian(): void
    {
        $ticket = Ticket::create([
            'kode' => 'TCK-20261020-0099',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Uji Durasi Bahasa Indonesia',
            'deskripsi' => 'Pengujian durasi',
            'prioritas' => 'tinggi',
            'status' => 'selesai',
            'teknisi_id' => $this->teknisi->id,
            'waktu_selesai' => now(),
        ]);
        $ticket->created_at = now()->subDays(2);
        $ticket->save();

        $this->assertEquals('2 hari', $ticket->durasi_penyelesaian);

        $response = $this->actingAs($this->admin)->get(route('admin.laporan.index'));
        $response->assertSee('2 hari');
        $response->assertDontSee('2 days');
    }
}

