<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    protected User $pelapor1;
    protected User $pelapor2;
    protected User $teknisi;
    protected User $admin;
    protected Unit $unitIGD;
    protected Category $categoryKomputer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->unitIGD = Unit::create(['nama' => 'Instalasi Gawat Darurat (IGD)']);
        $this->categoryKomputer = Category::create(['nama' => 'Komputer']);

        $this->pelapor1 = User::factory()->create([
            'name' => 'Pelapor Satu',
            'role' => 'pelapor',
            'unit_id' => $this->unitIGD->id,
        ]);

        $this->pelapor2 = User::factory()->create([
            'name' => 'Pelapor Dua',
            'role' => 'pelapor',
            'unit_id' => $this->unitIGD->id,
        ]);

        $this->teknisi = User::factory()->create([
            'name' => 'Teknisi IT',
            'role' => 'teknisi',
            'unit_id' => $this->unitIGD->id,
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin IT',
            'role' => 'admin',
            'unit_id' => $this->unitIGD->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_tickets(): void
    {
        $response = $this->get(route('tickets.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_pelapor_can_view_ticket_index_and_only_sees_own_tickets(): void
    {
        $ticket1 = Ticket::create([
            'kode' => 'TCK-20260928-0001',
            'user_id' => $this->pelapor1->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Komputer Pelapor 1 Rusak',
            'deskripsi' => 'Kendala pada monitor',
            'prioritas' => 'tinggi',
            'status' => 'baru',
        ]);

        $ticket2 = Ticket::create([
            'kode' => 'TCK-20260928-0002',
            'user_id' => $this->pelapor2->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Komputer Pelapor 2 Rusak',
            'deskripsi' => 'Kendala pada keyboard',
            'prioritas' => 'sedang',
            'status' => 'baru',
        ]);

        $response = $this->actingAs($this->pelapor1)->get(route('tickets.index'));

        $response->assertOk();
        $response->assertSee('Komputer Pelapor 1 Rusak');
        $response->assertDontSee('Komputer Pelapor 2 Rusak');
    }

    public function test_pelapor_can_view_create_ticket_page(): void
    {
        $response = $this->actingAs($this->pelapor1)->get(route('tickets.create'));

        $response->assertOk();
        $response->assertSee('Buat Tiket Pengaduan IT');
        $response->assertSee($this->unitIGD->nama);
        $response->assertSee($this->categoryKomputer->nama);
    }

    public function test_pelapor_can_create_ticket_with_auto_generated_code_and_attachment(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('error.jpg');

        $response = $this->actingAs($this->pelapor1)->post(route('tickets.store'), [
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'SIMRS tidak bisa dibuka',
            'deskripsi' => 'Muncul dialog error saat hendak klik menu pendaftaran pasien.',
            'prioritas' => 'tinggi',
            'lampiran' => $file,
        ]);

        $today = now()->format('Ymd');
        $expectedKode = "TCK-{$today}-0001";

        $ticket = Ticket::where('kode', $expectedKode)->first();
        $this->assertNotNull($ticket);
        $this->assertEquals('baru', $ticket->status);
        $this->assertEquals($this->pelapor1->id, $ticket->user_id);
        $this->assertNotNull($ticket->lampiran);

        Storage::disk('public')->assertExists($ticket->lampiran);

        $response->assertRedirect(route('tickets.show', $ticket));
    }

    public function test_sequential_ticket_codes_increment_properly(): void
    {
        $today = now()->format('Ymd');

        $t1 = $this->actingAs($this->pelapor1)->post(route('tickets.store'), [
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Masalah 1',
            'deskripsi' => 'Deskripsi 1',
            'prioritas' => 'sedang',
        ]);

        $t2 = $this->actingAs($this->pelapor1)->post(route('tickets.store'), [
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Masalah 2',
            'deskripsi' => 'Deskripsi 2',
            'prioritas' => 'rendah',
        ]);

        $this->assertDatabaseHas('tickets', ['kode' => "TCK-{$today}-0001"]);
        $this->assertDatabaseHas('tickets', ['kode' => "TCK-{$today}-0002"]);
    }

    public function test_pelapor_cannot_view_other_users_ticket_returns_403(): void
    {
        $ticket = Ticket::create([
            'kode' => 'TCK-20260928-0001',
            'user_id' => $this->pelapor1->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Kendala Rahasia',
            'deskripsi' => 'Deskripsi rahasia',
            'prioritas' => 'sedang',
            'status' => 'baru',
        ]);

        // Pelapor 2 mencoba membuka tiket milik Pelapor 1 -> Harus 403 Forbidden
        $response = $this->actingAs($this->pelapor2)->get(route('tickets.show', $ticket));
        $response->assertForbidden();
    }

    public function test_pelapor_can_view_own_ticket(): void
    {
        $ticket = Ticket::create([
            'kode' => 'TCK-20260928-0001',
            'user_id' => $this->pelapor1->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Kendala Saya Sendiri',
            'deskripsi' => 'Deskripsi saya',
            'prioritas' => 'sedang',
            'status' => 'baru',
        ]);

        $response = $this->actingAs($this->pelapor1)->get(route('tickets.show', $ticket));
        $response->assertOk();
        $response->assertSee('Kendala Saya Sendiri');
    }

    public function test_admin_and_teknisi_can_view_any_ticket(): void
    {
        $ticket = Ticket::create([
            'kode' => 'TCK-20260928-0001',
            'user_id' => $this->pelapor1->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Tiket Pelapor 1',
            'deskripsi' => 'Deskripsi',
            'prioritas' => 'tinggi',
            'status' => 'baru',
        ]);

        // Admin can view
        $responseAdmin = $this->actingAs($this->admin)->get(route('tickets.show', $ticket));
        $responseAdmin->assertOk();

        // Teknisi can view
        $responseTeknisi = $this->actingAs($this->teknisi)->get(route('tickets.show', $ticket));
        $responseTeknisi->assertOk();
    }
}
