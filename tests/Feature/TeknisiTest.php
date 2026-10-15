<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeknisiTest extends TestCase
{
    use RefreshDatabase;

    protected User $pelapor;
    protected User $teknisi;
    protected User $teknisiLain;
    protected Unit $unitIGD;
    protected Category $categoryKomputer;
    protected Category $categoryJaringan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->unitIGD = Unit::create(['nama' => 'Instalasi Gawat Darurat (IGD)']);
        $this->categoryKomputer = Category::create(['nama' => 'Komputer']);
        $this->categoryJaringan = Category::create(['nama' => 'Jaringan']);

        $this->pelapor = User::factory()->create([
            'name' => 'Perawat Siti',
            'role' => 'pelapor',
            'unit_id' => $this->unitIGD->id,
        ]);

        $this->teknisi = User::factory()->create([
            'name' => 'Budi Teknisi',
            'role' => 'teknisi',
            'unit_id' => $this->unitIGD->id,
        ]);

        $this->teknisiLain = User::factory()->create([
            'name' => 'Andi Teknisi',
            'role' => 'teknisi',
            'unit_id' => $this->unitIGD->id,
        ]);
    }

    public function test_guest_cannot_access_teknisi_routes(): void
    {
        $response = $this->get(route('teknisi.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_pelapor_cannot_access_teknisi_routes_returns_403(): void
    {
        $response = $this->actingAs($this->pelapor)->get(route('teknisi.dashboard'));
        $response->assertForbidden();

        $responseIndex = $this->actingAs($this->pelapor)->get(route('teknisi.tickets.index'));
        $responseIndex->assertForbidden();
    }

    public function test_teknisi_can_access_dashboard_and_see_stats(): void
    {
        // 1 tiket diproses oleh teknisi ini
        Ticket::create([
            'kode' => 'TCK-20261005-0001',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'PC Rusak',
            'deskripsi' => 'Mati total',
            'prioritas' => 'tinggi',
            'status' => 'diproses',
            'teknisi_id' => $this->teknisi->id,
        ]);

        // 1 tiket baru belum diambil
        Ticket::create([
            'kode' => 'TCK-20261005-0002',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryJaringan->id,
            'judul' => 'Kabel LAN Lepas',
            'deskripsi' => 'Tidak bisa konek internet',
            'prioritas' => 'sedang',
            'status' => 'baru',
            'teknisi_id' => null,
        ]);

        $response = $this->actingAs($this->teknisi)->get(route('teknisi.dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard Teknisi IT');
        $response->assertSee('PC Rusak');
        $response->assertSee('Kabel LAN Lepas');
        $response->assertDontSee('Buat Tiket');
    }

    public function test_teknisi_can_access_tickets_index_and_filter_status_and_category(): void
    {
        // Tiket baru
        $ticketBaru = Ticket::create([
            'kode' => 'TCK-20261005-0001',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Komputer Error',
            'deskripsi' => 'BSOD',
            'prioritas' => 'tinggi',
            'status' => 'baru',
        ]);

        // Tiket ditangani teknisi ini
        $ticketSaya = Ticket::create([
            'kode' => 'TCK-20261005-0002',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryJaringan->id,
            'judul' => 'WiFi Lemot',
            'deskripsi' => 'Sinyal hilang timbul',
            'prioritas' => 'sedang',
            'status' => 'diproses',
            'teknisi_id' => $this->teknisi->id,
        ]);

        // Tiket orang lain yang diproses teknisi lain (tidak boleh muncul di list teknisi ini)
        $ticketTeknisiLain = Ticket::create([
            'kode' => 'TCK-20261005-0003',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Printer Macet',
            'deskripsi' => 'Paper jam',
            'prioritas' => 'rendah',
            'status' => 'diproses',
            'teknisi_id' => $this->teknisiLain->id,
        ]);

        $response = $this->actingAs($this->teknisi)->get(route('teknisi.tickets.index'));
        $response->assertOk();
        $response->assertSee('Komputer Error');
        $response->assertSee('WiFi Lemot');
        $response->assertDontSee('Printer Macet');

        // Test filter status=baru (hanya antrean tiket baru, tiket diproses tidak boleh muncul)
        $responseBaru = $this->actingAs($this->teknisi)->get(route('teknisi.tickets.index', ['status' => 'baru']));
        $responseBaru->assertSee('Komputer Error');
        $responseBaru->assertDontSee('WiFi Lemot');

        // Test filter status=diproses (hanya tiket diproses oleh teknisi ini, tiket baru tidak boleh muncul)
        $responseDiproses = $this->actingAs($this->teknisi)->get(route('teknisi.tickets.index', ['status' => 'diproses']));
        $responseDiproses->assertSee('WiFi Lemot');
        $responseDiproses->assertDontSee('Komputer Error');

        // Test filter category_id (hanya tiket dengan kategori terkait)
        $responseCategory = $this->actingAs($this->teknisi)->get(route('teknisi.tickets.index', ['category_id' => $this->categoryJaringan->id]));
        $responseCategory->assertSee('WiFi Lemot');
        $responseCategory->assertDontSee('Komputer Error');

        // Test filter prioritas=tinggi (hanya prioritas tinggi, prioritas sedang tidak boleh muncul)
        $responsePrioritas = $this->actingAs($this->teknisi)->get(route('teknisi.tickets.index', ['prioritas' => 'tinggi']));
        $responsePrioritas->assertSee('Komputer Error');
        $responsePrioritas->assertDontSee('WiFi Lemot');
    }

    public function test_teknisi_tickets_pagination_preserves_query_string(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            Ticket::create([
                'kode' => sprintf('TCK-20261008-%04d', $i),
                'user_id' => $this->pelapor->id,
                'unit_id' => $this->unitIGD->id,
                'category_id' => $this->categoryKomputer->id,
                'judul' => "Tiket Batch $i",
                'deskripsi' => "Deskripsi kendala batch $i",
                'prioritas' => 'sedang',
                'status' => 'diproses',
                'teknisi_id' => $this->teknisi->id,
            ]);
        }

        $response = $this->actingAs($this->teknisi)->get(route('teknisi.tickets.index', ['status' => 'diproses']));
        $response->assertOk();
        // Memastikan link paginasi menyertakan parameter query string filter
        $response->assertSee('status=diproses');
        $response->assertSee('page=2');
    }

    public function test_teknisi_can_take_new_ticket(): void
    {
        $ticket = Ticket::create([
            'kode' => 'TCK-20261005-0001',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Tiket Antrean Baru',
            'deskripsi' => 'Menunggu teknisi',
            'prioritas' => 'tinggi',
            'status' => 'baru',
            'teknisi_id' => null,
        ]);

        $response = $this->actingAs($this->teknisi)->post(route('teknisi.tickets.take', $ticket));

        $response->assertRedirect(route('teknisi.tickets.show', $ticket));

        $ticket->refresh();
        $this->assertEquals('diproses', $ticket->status);
        $this->assertEquals($this->teknisi->id, $ticket->teknisi_id);

        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $this->teknisi->id,
        ]);
    }

    public function test_teknisi_can_update_status_to_selesai_and_sets_waktu_selesai(): void
    {
        $ticket = Ticket::create([
            'kode' => 'TCK-20261005-0001',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'PC Perlu Instal Ulang',
            'deskripsi' => 'Virus',
            'prioritas' => 'sedang',
            'status' => 'diproses',
            'teknisi_id' => $this->teknisi->id,
            'waktu_selesai' => null,
        ]);

        $response = $this->actingAs($this->teknisi)->patch(route('teknisi.tickets.status', $ticket), [
            'status' => 'selesai',
            'catatan' => 'Sistem operasi sudah diinstal ulang dan antivirus aktif.',
        ]);

        $response->assertRedirect(route('teknisi.tickets.show', $ticket));

        $ticket->refresh();
        $this->assertEquals('selesai', $ticket->status);
        $this->assertNotNull($ticket->waktu_selesai);

        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $this->teknisi->id,
        ]);
    }

    public function test_teknisi_can_update_status_to_ditutup(): void
    {
        $ticket = Ticket::create([
            'kode' => 'TCK-20261005-0001',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Tutup Tiket',
            'deskripsi' => 'Sudah dicek oke',
            'prioritas' => 'rendah',
            'status' => 'selesai',
            'teknisi_id' => $this->teknisi->id,
        ]);

        $response = $this->actingAs($this->teknisi)->patch(route('teknisi.tickets.status', $ticket), [
            'status' => 'ditutup',
            'catatan' => 'Pelapor mengonfirmasi kendala telah tuntas.',
        ]);

        $response->assertRedirect(route('teknisi.tickets.show', $ticket));

        $ticket->refresh();
        $this->assertEquals('ditutup', $ticket->status);
    }

    public function test_teknisi_can_add_comment(): void
    {
        $ticket = Ticket::create([
            'kode' => 'TCK-20261005-0001',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Uji Komentar',
            'deskripsi' => 'Deskripsi',
            'prioritas' => 'sedang',
            'status' => 'diproses',
            'teknisi_id' => $this->teknisi->id,
        ]);

        $response = $this->actingAs($this->teknisi)->post(route('teknisi.tickets.comments.store', $ticket), [
            'isi' => 'Kami sudah memesan sparepart RAM baru ke bagian pengadaan.',
        ]);

        $response->assertRedirect(route('teknisi.tickets.show', $ticket));

        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $this->teknisi->id,
            'isi' => 'Kami sudah memesan sparepart RAM baru ke bagian pengadaan.',
        ]);
    }

    public function test_teknisi_can_view_ticket_detail(): void
    {
        $ticket = Ticket::create([
            'kode' => 'TCK-20261005-0001',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Detail Tiket Komputer Rusak',
            'deskripsi' => 'Deskripsi kendala teknis',
            'prioritas' => 'tinggi',
            'status' => 'diproses',
            'teknisi_id' => $this->teknisi->id,
        ]);

        $response = $this->actingAs($this->teknisi)->get(route('teknisi.tickets.show', $ticket));

        $response->assertOk();
        $response->assertSee('Detail Tiket Komputer Rusak');
        $response->assertSee('TCK-20261005-0001');
        $response->assertSee('Instalasi Gawat Darurat (IGD)');
        $response->assertSee('Komputer');
    }
}

