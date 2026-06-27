<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles first
        $this->call([
            RoleSeeder::class,
        ]);

        // Create or retrieve admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => bcrypt('password')]
        );
        $admin->assignRole('admin');

        // Seed BUMD, Berita categories and Berita
        $this->call([
            BUMDSeeder::class,
            BeritaCategorySeeder::class,
            BeritaSeeder::class,
            AlbumsSeeder::class,
            ContentSliderSeeder::class,
            PejabatSeeder::class,
            RegulasiSeeder::class,
        ]);
    }
}
