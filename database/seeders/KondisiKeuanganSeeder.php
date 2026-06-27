<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KondisiKeuangan;

class KondisiKeuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = database_path('seeders/bumd/kondisi_keuangans_202511141420.csv');
        if (!is_file($csvPath)) {
            $this->command?->warn("CSV file not found at: {$csvPath}");
            return;
        }

        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            $this->command?->warn("Unable to open CSV file: {$csvPath}");
            return;
        }

        // Read header
        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            $this->command?->warn("CSV header missing.");
            return;
        }

        // Normalize header: strip BOM from first column and trim spaces
        if (isset($header[0]) && is_string($header[0])) {
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
        }
        $header = array_map(static function ($h) {
            return is_string($h) ? trim($h) : $h;
        }, $header);

        // Helper to get column index by name
        $getIndex = function (string $name) use ($header): ?int {
            $idx = array_search($name, $header, true);
            return $idx === false ? null : $idx;
        };

        $indices = [
            'id' => $getIndex('id'),
            'bumd_id' => $getIndex('bumd_id'),
            'no_akun' => $getIndex('no_akun'),
            'nama_akun' => $getIndex('nama_akun'),
            'tahun_2020' => $getIndex('tahun_2020'),
            'tahun_2021' => $getIndex('tahun_2021'),
            'tahun_2022' => $getIndex('tahun_2022'),
            'tahun_2023' => $getIndex('tahun_2023'),
            'tahun_2024' => $getIndex('tahun_2024'),
        ];

        // Helper function to convert string to integer safely
        $toInteger = function ($value) {
            if ($value === '' || $value === null) {
                return 0;
            }
            return (int) $value;
        };

        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $id = $indices['id'] !== null ? ($row[$indices['id']] ?? null) : null;
            if (!$id) {
                continue;
            }

            $data = [
                'bumd_id' => $indices['bumd_id'] !== null ? ($row[$indices['bumd_id']] ?? null) : null,
                'no_akun' => $indices['no_akun'] !== null ? ($row[$indices['no_akun']] ?? null) : null,
                'nama_akun' => $indices['nama_akun'] !== null ? ($row[$indices['nama_akun']] ?? null) : null,
                'tahun_2020' => $indices['tahun_2020'] !== null ? $toInteger($row[$indices['tahun_2020']] ?? null) : 0,
                'tahun_2021' => $indices['tahun_2021'] !== null ? $toInteger($row[$indices['tahun_2021']] ?? null) : 0,
                'tahun_2022' => $indices['tahun_2022'] !== null ? $toInteger($row[$indices['tahun_2022']] ?? null) : 0,
                'tahun_2023' => $indices['tahun_2023'] !== null ? $toInteger($row[$indices['tahun_2023']] ?? null) : 0,
                'tahun_2024' => $indices['tahun_2024'] !== null ? $toInteger($row[$indices['tahun_2024']] ?? null) : 0,
            ];

            // Validate required fields
            $required = ['bumd_id', 'no_akun', 'nama_akun'];
            $skip = false;
            foreach ($required as $field) {
                $val = $data[$field] ?? null;
                if ($val === null || $val === '') {
                    $this->command?->warn("Skipping ID '{$id}': missing required field '{$field}'.");
                    $skip = true;
                    break;
                }
            }

            if ($skip) {
                continue;
            }

            // Create or update by ID
            $kondisi = KondisiKeuangan::firstOrNew(['id' => $id]);
            $kondisi->fill($data);

            // Persist
            $kondisi->save();
            $count++;
        }

        fclose($handle);
        $this->command?->info("KondisiKeuanganSeeder completed. Inserted/updated {$count} records.");
    }
}
