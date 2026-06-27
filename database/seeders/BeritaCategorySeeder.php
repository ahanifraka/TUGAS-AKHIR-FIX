<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BeritaCategory;

class BeritaCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['category_name' => 'Berita', 'is_active' => true],
            ['category_name' => 'Pengumuman', 'is_active' => true],
            ['category_name' => 'Festival', 'is_active' => true],
            ['category_name' => 'Layanan', 'is_active' => true],
            ['category_name' => 'Pembangunan', 'is_active' => true],
        ];

        foreach ($categories as $c) {
            BeritaCategory::firstOrCreate(
                ['category_name' => $c['category_name']],
                ['is_active' => $c['is_active']]
            );
        }
    }
}
