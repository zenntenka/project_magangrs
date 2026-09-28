<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitIT = Unit::where('nama', 'IT & SIMRS')->first();
        $unitIGD = Unit::where('nama', 'Instalasi Gawat Darurat (IGD)')->first();

        // 1. User Admin
        User::firstOrCreate(
            ['email' => 'admin@rs.local'],
            [
                'name' => 'Administrator IT',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'unit_id' => $unitIT?->id,
                'email_verified_at' => now(),
            ]
        );

        // 2. User Teknisi
        User::firstOrCreate(
            ['email' => 'teknisi@rs.local'],
            [
                'name' => 'Budi Santoso (Teknisi IT)',
                'password' => Hash::make('password'),
                'role' => 'teknisi',
                'unit_id' => $unitIT?->id,
                'email_verified_at' => now(),
            ]
        );

        // 3. User Pelapor
        User::firstOrCreate(
            ['email' => 'pelapor@rs.local'],
            [
                'name' => 'Siti Rahma (Perawat IGD)',
                'password' => Hash::make('password'),
                'role' => 'pelapor',
                'unit_id' => $unitIGD?->id,
                'email_verified_at' => now(),
            ]
        );
    }
}
