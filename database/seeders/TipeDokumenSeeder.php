<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipeDokumen;

class TipeDokumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Abstrak', 'order' => 1],
            ['name' => 'Peraturan', 'order' => 2],
            ['name' => 'Monografi', 'order' => 3],
            ['name' => 'Artikel', 'order' => 4],
            ['name' => 'Putusan Pengadilan', 'order' => 5],
        ];

        foreach ($types as $type) {
            TipeDokumen::firstOrCreate(
                ['name' => $type['name']],
                [
                    'order' => $type['order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
