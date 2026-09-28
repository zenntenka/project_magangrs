<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['nama' => 'Komputer'],
            ['nama' => 'Jaringan'],
            ['nama' => 'Printer'],
            ['nama' => 'SIMRS'],
            ['nama' => 'Lainnya'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['nama' => $category['nama']], $category);
        }
    }
}
