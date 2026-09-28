<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['nama' => 'Instalasi Gawat Darurat (IGD)'],
            ['nama' => 'Rawat Inap'],
            ['nama' => 'Rawat Jalan / Poliklinik'],
            ['nama' => 'Farmasi'],
            ['nama' => 'Laboratorium'],
            ['nama' => 'Radiologi'],
            ['nama' => 'Rekam Medis'],
            ['nama' => 'Keuangan & Kasir'],
            ['nama' => 'IT & SIMRS'],
            ['nama' => 'Tata Usaha & SDM'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['nama' => $unit['nama']], $unit);
        }
    }
}
