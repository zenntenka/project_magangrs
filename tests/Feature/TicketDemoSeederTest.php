<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\TicketDemoSeeder;
use Database\Seeders\UnitSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_demo_seeder_creates_expected_data(): void
    {
        // 1. Seed master data terlebih dahulu
        $this->seed(UnitSeeder::class);
        $this->seed(CategorySeeder::class);
        $this->seed(UserSeeder::class);

        // 2. Jalankan TicketDemoSeeder terpisah
        $this->seed(TicketDemoSeeder::class);

        // 3. Verifikasi jumlah tiket (minimal 60 tiket)
        $this->assertGreaterThanOrEqual(60, Ticket::count());

        // 4. Verifikasi distribusi status
        $totalTickets = Ticket::count();
        $baruCount = Ticket::where('status', 'baru')->count();
        $diprosesCount = Ticket::where('status', 'diproses')->count();
        $selesaiCount = Ticket::where('status', 'selesai')->count();
        $ditutupCount = Ticket::where('status', 'ditutup')->count();

        $this->assertEquals(9, $baruCount, 'Jumlah tiket baru harus 9 (~15%)');
        $this->assertEquals(12, $diprosesCount, 'Jumlah tiket diproses harus 12 (~20%)');
        $this->assertEquals(21, $selesaiCount, 'Jumlah tiket selesai harus 21 (~35%)');
        $this->assertEquals(18, $ditutupCount, 'Jumlah tiket ditutup harus 18 (~30%)');

        // 5. Verifikasi aturan waktu_selesai dan teknisi_id
        // Tiket baru tidak boleh punya teknisi atau waktu_selesai
        $this->assertEquals(0, Ticket::where('status', 'baru')->whereNotNull('teknisi_id')->count());
        $this->assertEquals(0, Ticket::where('status', 'baru')->whereNotNull('waktu_selesai')->count());

        // Tiket diproses harus punya teknisi, tapi waktu_selesai belum ada
        $this->assertEquals(0, Ticket::where('status', 'diproses')->whereNull('teknisi_id')->count());
        $this->assertEquals(0, Ticket::where('status', 'diproses')->whereNotNull('waktu_selesai')->count());

        // Tiket selesai dan ditutup wajib punya teknisi dan waktu_selesai LEBIH LAMA dari created_at
        $resolvedTickets = Ticket::whereIn('status', ['selesai', 'ditutup'])->get();
        $this->assertCount(39, $resolvedTickets);

        foreach ($resolvedTickets as $ticket) {
            $this->assertNotNull($ticket->teknisi_id, "Tiket {$ticket->kode} wajib punya teknisi_id");
            $this->assertNotNull($ticket->waktu_selesai, "Tiket {$ticket->kode} wajib punya waktu_selesai");
            $this->assertTrue(
                $ticket->waktu_selesai->greaterThan($ticket->created_at),
                "waktu_selesai ({$ticket->waktu_selesai}) harus lebih lama dari created_at ({$ticket->created_at}) pada tiket {$ticket->kode}"
            );
            $this->assertTrue(
                $ticket->waktu_selesai->lessThanOrEqualTo(now()->addMinute()),
                "waktu_selesai ({$ticket->waktu_selesai}) tidak boleh melebihi waktu sekarang pada tiket {$ticket->kode}"
            );
        }

        // 6. Verifikasi komentar teknisi
        $ticketsWithComments = Ticket::whereIn('status', ['diproses', 'selesai', 'ditutup'])->with('comments')->get();
        foreach ($ticketsWithComments as $ticket) {
            $this->assertGreaterThanOrEqual(1, $ticket->comments->count(), "Tiket {$ticket->kode} harus punya minimal 1 komentar");
            $this->assertLessThanOrEqual(3, $ticket->comments->count(), "Tiket {$ticket->kode} maksimal 3 komentar");
        }

        // 7. Verifikasi keunikan kode tiket TCK-YYYYMMDD-XXXX
        $allKodes = Ticket::pluck('kode')->toArray();
        $this->assertEquals(count($allKodes), count(array_unique($allKodes)), 'Semua kode tiket harus unik tanpa duplikasi');

        foreach ($allKodes as $kode) {
            $this->assertMatchesRegularExpression('/^TCK-\d{8}-\d{4}$/', $kode);
        }

        // 8. Verifikasi teknisi tambahan dan pelapor dari unit berbeda
        $techniciansCount = User::where('role', 'teknisi')->count();
        $this->assertGreaterThanOrEqual(3, $techniciansCount, 'Harus ada minimal 3 teknisi');

        $reportersCount = User::where('role', 'pelapor')->count();
        $this->assertGreaterThanOrEqual(8, $reportersCount, 'Harus ada minimal 8 pelapor');
    }
}

