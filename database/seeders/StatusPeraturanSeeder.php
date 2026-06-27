<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StatusPeraturan;

class StatusPeraturanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Berlaku', 'order' => 1],
            ['name' => 'Tidak Berlaku', 'order' => 2],
            ['name' => 'Dicabut', 'order' => 3],
            ['name' => 'Diubah', 'order' => 4],
        ];

        foreach ($statuses as $status) {
            StatusPeraturan::firstOrCreate(
                ['name' => $status['name']],
                [
                    'order' => $status['order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
