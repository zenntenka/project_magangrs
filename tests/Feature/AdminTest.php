<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teknisi;
    protected User $pelapor;
    protected Unit $unitIGD;
    protected Category $categoryKomputer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->unitIGD = Unit::create(['nama' => 'Instalasi Gawat Darurat (IGD)']);
        $this->categoryKomputer = Category::create(['nama' => 'Komputer']);

        $this->admin = User::factory()->create([
            'name' => 'Admin Utama',
            'email' => 'admin.test@rs.local',
            'role' => 'admin',
        ]);

        $this->teknisi = User::factory()->create([
            'name' => 'Teknisi IT',
            'email' => 'teknisi.test@rs.local',
            'role' => 'teknisi',
        ]);

        $this->pelapor = User::factory()->create([
            'name' => 'Perawat Siti',
            'email' => 'pelapor.test@rs.local',
            'role' => 'pelapor',
            'unit_id' => $this->unitIGD->id,
        ]);
    }

    public function test_guest_cannot_access_admin_routes(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));

        $responseUnits = $this->get(route('admin.units.index'));
        $responseUnits->assertRedirect(route('login'));
    }

    public function test_pelapor_and_teknisi_cannot_access_admin_routes_returns_403(): void
    {
        // Pelapor mencoba akses admin
        $responsePelapor = $this->actingAs($this->pelapor)->get(route('admin.dashboard'));
        $responsePelapor->assertForbidden();

        // Teknisi mencoba akses admin
        $responseTeknisi = $this->actingAs($this->teknisi)->get(route('admin.dashboard'));
        $responseTeknisi->assertForbidden();

        $responseUnits = $this->actingAs($this->teknisi)->get(route('admin.units.index'));
        $responseUnits->assertForbidden();
    }

    public function test_admin_can_view_dashboard_with_stats_and_charts(): void
    {
        // Buat 1 tiket selesai dan 1 tiket baru
        Ticket::create([
            'kode' => 'TCK-20261013-0001',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'PC Kasir IGD Mati',
            'deskripsi' => 'Mati mendadak',
            'prioritas' => 'tinggi',
            'status' => 'selesai',
            'teknisi_id' => $this->teknisi->id,
            'created_at' => now()->subHours(2),
            'waktu_selesai' => now(),
        ]);

        Ticket::create([
            'kode' => 'TCK-20261013-0002',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Printer Kasir Rusak',
            'deskripsi' => 'Paper jam',
            'prioritas' => 'sedang',
            'status' => 'baru',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard', ['periode' => 'bulan_ini']));

        $response->assertOk();
        $response->assertSee('Dashboard Administrator');
        $response->assertSee('Total Tiket');
        $response->assertSee('Tiket Baru');
        $response->assertSee('Selesai');
        $response->assertSee('PC Kasir IGD Mati');
        $response->assertSee('Printer Kasir Rusak');
    }

    public function test_admin_can_manage_units_crud_and_cannot_delete_used_unit(): void
    {
        // 1. Index
        $response = $this->actingAs($this->admin)->get(route('admin.units.index'));
        $response->assertOk();
        $response->assertSee('Instalasi Gawat Darurat (IGD)');

        // 2. Create Unit
        $responseCreate = $this->actingAs($this->admin)->post(route('admin.units.store'), [
            'nama' => 'Laboratorium Patologi',
        ]);
        $responseCreate->assertRedirect(route('admin.units.index'));
        $this->assertDatabaseHas('units', ['nama' => 'Laboratorium Patologi']);

        $lab = Unit::where('nama', 'Laboratorium Patologi')->first();

        // 3. Update Unit
        $responseUpdate = $this->actingAs($this->admin)->put(route('admin.units.update', $lab), [
            'nama' => 'Laboratorium Klinik Sentral',
        ]);
        $responseUpdate->assertRedirect(route('admin.units.index'));
        $this->assertDatabaseHas('units', ['nama' => 'Laboratorium Klinik Sentral']);

        // 4. Coba hapus unit yang dipakai tiket
        Ticket::create([
            'kode' => 'TCK-20261013-0001',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Kendala IGD',
            'deskripsi' => 'Deskripsi',
            'prioritas' => 'tinggi',
            'status' => 'baru',
        ]);

        $responseDeleteUsed = $this->actingAs($this->admin)->delete(route('admin.units.destroy', $this->unitIGD));
        $responseDeleteUsed->assertSessionHas('error');
        $this->assertDatabaseHas('units', ['id' => $this->unitIGD->id]);

        // 5. Hapus unit yang tidak terpakai
        $responseDeleteUnused = $this->actingAs($this->admin)->delete(route('admin.units.destroy', $lab));
        $responseDeleteUnused->assertRedirect(route('admin.units.index'));
        $this->assertDatabaseMissing('units', ['id' => $lab->id]);
    }

    public function test_admin_can_manage_categories_crud_and_cannot_delete_used_category(): void
    {
        // 1. Create Category
        $responseCreate = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'nama' => 'SIMRS & Rekam Medis Elektronik',
        ]);
        $responseCreate->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['nama' => 'SIMRS & Rekam Medis Elektronik']);

        $simrs = Category::where('nama', 'SIMRS & Rekam Medis Elektronik')->first();

        // 2. Update Category
        $responseUpdate = $this->actingAs($this->admin)->put(route('admin.categories.update', $simrs), [
            'nama' => 'SIMRS Bridging BPJS',
        ]);
        $responseUpdate->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['nama' => 'SIMRS Bridging BPJS']);

        // 3. Coba hapus kategori yang dipakai tiket
        Ticket::create([
            'kode' => 'TCK-20261013-0001',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Komputer Error',
            'deskripsi' => 'Deskripsi',
            'prioritas' => 'tinggi',
            'status' => 'baru',
        ]);

        $responseDeleteUsed = $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $this->categoryKomputer));
        $responseDeleteUsed->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $this->categoryKomputer->id]);

        // 4. Hapus kategori yang tidak terpakai
        $responseDeleteUnused = $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $simrs));
        $responseDeleteUnused->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $simrs->id]);
    }

    public function test_admin_can_manage_users_crud_and_cannot_delete_self_or_user_with_tickets(): void
    {
        // 1. Index
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));
        $response->assertOk();
        $response->assertSee($this->pelapor->name);

        // 2. Create User dengan default password
        $responseCreate = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Bambang Teknisi Baru',
            'email' => 'bambang@rs.local',
            'role' => 'teknisi',
            'unit_id' => $this->unitIGD->id,
            'password' => '', // kosong, otomatis default password123
        ]);
        $responseCreate->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['email' => 'bambang@rs.local', 'role' => 'teknisi']);

        $bambang = User::where('email', 'bambang@rs.local')->first();
        $this->assertTrue(Hash::check('password123', $bambang->password));

        // 3. Update User
        $responseUpdate = $this->actingAs($this->admin)->put(route('admin.users.update', $bambang), [
            'name' => 'Bambang Sudiro',
            'email' => 'bambang@rs.local',
            'role' => 'teknisi',
            'unit_id' => $this->unitIGD->id,
            'password' => 'secret12345',
        ]);
        $responseUpdate->assertRedirect(route('admin.users.index'));
        $bambang->refresh();
        $this->assertEquals('Bambang Sudiro', $bambang->name);
        $this->assertTrue(Hash::check('secret12345', $bambang->password));

        // 4. Admin tidak boleh menghapus akunnya sendiri
        $responseDeleteSelf = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin));
        $responseDeleteSelf->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);

        // 5. Coba hapus pelapor yang punya tiket
        Ticket::create([
            'kode' => 'TCK-20261013-0001',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Tiket Pelapor',
            'deskripsi' => 'Deskripsi',
            'prioritas' => 'tinggi',
            'status' => 'baru',
        ]);

        $responseDeleteReporter = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->pelapor));
        $responseDeleteReporter->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->pelapor->id]);

        // 6. Hapus pengguna yang belum memiliki tiket
        $responseDeleteUnused = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $bambang));
        $responseDeleteUnused->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $bambang->id]);
    }

    public function test_admin_can_view_all_tickets_and_assign_to_technician(): void
    {
        $ticket = Ticket::create([
            'kode' => 'TCK-20261013-0001',
            'user_id' => $this->pelapor->id,
            'unit_id' => $this->unitIGD->id,
            'category_id' => $this->categoryKomputer->id,
            'judul' => 'Gangguan Monitor IGD',
            'deskripsi' => 'Layar bergaris',
            'prioritas' => 'tinggi',
            'status' => 'baru',
            'teknisi_id' => null,
        ]);

        // 1. Index tickets
        $response = $this->actingAs($this->admin)->get(route('admin.tickets.index'));
        $response->assertOk();
        $response->assertSee('Gangguan Monitor IGD');
        $response->assertSee('TCK-20261013-0001');

        // 2. Assign ticket ke teknisi
        $responseAssign = $this->actingAs($this->admin)->post(route('admin.tickets.assign', $ticket), [
            'teknisi_id' => $this->teknisi->id,
        ]);
        $responseAssign->assertRedirect();

        $ticket->refresh();
        $this->assertEquals($this->teknisi->id, $ticket->teknisi_id);
        $this->assertEquals('diproses', $ticket->status); // Status otomatis berubah dari baru ke diproses

        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $this->admin->id,
        ]);

        // 3. Admin update status ke selesai
        $responseStatus = $this->actingAs($this->admin)->patch(route('admin.tickets.status', $ticket), [
            'status' => 'selesai',
            'catatan' => 'Monitor sudah diganti dengan unit cadangan.',
        ]);
        $responseStatus->assertRedirect();

        $ticket->refresh();
        $this->assertEquals('selesai', $ticket->status);
        $this->assertNotNull($ticket->waktu_selesai);

        // 4. Admin show ticket detail
        $responseShow = $this->actingAs($this->admin)->get(route('admin.tickets.show', $ticket));
        $responseShow->assertOk();
        $responseShow->assertSee('Gangguan Monitor IGD');
        $responseShow->assertSee($this->teknisi->name);
    }

    public function test_admin_navigation_is_visible_only_for_admin(): void
    {
        // Admin melihat menu admin
        $responseAdmin = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $responseAdmin->assertSee('Dashboard Admin');
        $responseAdmin->assertSee('Semua Tiket');
        $responseAdmin->assertSee('Unit RS');
        $responseAdmin->assertSee('Kategori');
        $responseAdmin->assertSee('Pengguna');

        // Teknisi tidak melihat menu admin
        $responseTeknisi = $this->actingAs($this->teknisi)->get(route('teknisi.dashboard'));
        $responseTeknisi->assertDontSee('Dashboard Admin');
        $responseTeknisi->assertDontSee('Semua Tiket');

        // Pelapor tidak melihat menu admin
        $responsePelapor = $this->actingAs($this->pelapor)->get(route('dashboard'));
        $responsePelapor->assertDontSee('Dashboard Admin');
        $responsePelapor->assertDontSee('Semua Tiket');
    }
}

