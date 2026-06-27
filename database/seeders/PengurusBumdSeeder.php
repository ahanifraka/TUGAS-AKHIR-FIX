<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PengurusBumd;
use Carbon\Carbon;

class PengurusBumdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = database_path('seeders/bumd/pengurus_bumds_202511141419.csv');
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
            'nama' => $getIndex('nama'),
            'jabatan' => $getIndex('jabatan'),
            'grup' => $getIndex('grup'),
            'created_at' => $getIndex('created_at'),
            'updated_at' => $getIndex('updated_at'),
        ];

        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $id = $indices['id'] !== null ? ($row[$indices['id']] ?? null) : null;
            if (!$id) {
                continue;
            }

            $data = [
                'bumd_id' => $indices['bumd_id'] !== null ? ($row[$indices['bumd_id']] ?? null) : null,
                'nama' => $indices['nama'] !== null ? ($row[$indices['nama']] ?? null) : null,
                'jabatan' => $indices['jabatan'] !== null ? ($row[$indices['jabatan']] ?? null) : null,
                'grup' => $indices['grup'] !== null ? ($row[$indices['grup']] ?? null) : null,
            ];

            // Validate required fields
            $required = ['bumd_id', 'nama', 'jabatan', 'grup'];
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
            $pengurus = PengurusBumd::firstOrNew(['id' => $id]);
            $pengurus->fill(array_map(function ($v) {
                return $v === '' ? null : trim($v);
            }, $data));

            // Apply timestamps from CSV if available
            $createdAtCsv = $indices['created_at'] !== null ? ($row[$indices['created_at']] ?? null) : null;
            $updatedAtCsv = $indices['updated_at'] !== null ? ($row[$indices['updated_at']] ?? null) : null;

            try {
                if (!empty($createdAtCsv)) {
                    $pengurus->created_at = Carbon::parse($createdAtCsv);
                }
            } catch (\Throwable $e) {
                // ignore parse errors
            }

            try {
                if (!empty($updatedAtCsv)) {
                    $pengurus->updated_at = Carbon::parse($updatedAtCsv);
                }
            } catch (\Throwable $e) {
                // ignore parse errors
            }

            // Persist without auto-updating timestamps
            $pengurus->timestamps = false;
            $pengurus->save();
            $pengurus->timestamps = true;
            $count++;
        }

        fclose($handle);
        $this->command?->info("PengurusBumdSeeder completed. Inserted/updated {$count} records.");
    }
}
