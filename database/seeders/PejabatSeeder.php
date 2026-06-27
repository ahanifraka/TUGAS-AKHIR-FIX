<?php

namespace Database\Seeders;

use App\Models\Pejabat;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PejabatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = database_path('seeders/pejabat/pejabats-20251027.csv');

        if (!file_exists($csvPath)) {
            throw new \RuntimeException("CSV file not found: {$csvPath}");
        }

        // Hapus data lama secara total agar tidak ada duplikasi (reset auto-increment)
        DB::table('pejabat')->truncate();

        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            throw new \RuntimeException("Unable to open CSV: {$csvPath}");
        }

        // Baca header
        $headers = fgetcsv($handle, 0, ',', '"');
        if ($headers === false) {
            fclose($handle);
            throw new \RuntimeException('CSV header row is missing or unreadable.');
        }
        $headers = array_map(function ($h) {
            return strtolower(trim($h));
        }, $headers);

        // Helper untuk normalisasi nilai string "NULL"/kosong menjadi null
        $normalize = function ($value) {
            if ($value === null) {
                return null;
            }
            if (is_string($value)) {
                $v = trim($value);
                if ($v === '' || strcasecmp($v, 'null') === 0) {
                    return null;
                }
            }
            return $value;
        };

        // Import baris-baris CSV
        while (($row = fgetcsv($handle, 0, ',', '"')) !== false) {
            // Lewati baris kosong
            if ($row === null || $row === [null]) {
                continue;
            }

            $assoc = [];
            foreach ($headers as $i => $header) {
                $assoc[$header] = $normalize($row[$i] ?? null);
            }

            // Tangani kolom image: jika JSON, ambil url original/varian lain
            $imageRaw = $assoc['image'] ?? null;
            $imagePath = null;
            if (!empty($imageRaw) && is_string($imageRaw)) {
                $decoded = json_decode($imageRaw, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $variantsOrder = ['original', 'large', 'medium', 'profile', 'small', 'xsmall', 'thumbnail'];
                    foreach ($variantsOrder as $variant) {
                        if (isset($decoded[$variant]['url']) && is_string($decoded[$variant]['url']) && $decoded[$variant]['url'] !== '') {
                            $imagePath = $decoded[$variant]['url'];
                            break;
                        }
                    }
                }
            }
            if ($imagePath === null && !empty($imageRaw)) {
                $imagePath = $imageRaw; // gunakan apa adanya jika bukan JSON
            }

            // Cast published ke boolean
            $publishedRaw = $assoc['published'] ?? null;
            $published = false;
            if (is_string($publishedRaw)) {
                $published = in_array(strtolower(trim($publishedRaw)), ['1', 'true', 'yes'], true);
            } else {
                $published = (bool) $publishedRaw;
            }

            // Cast order ke integer
            $orderVal = $assoc['order'] ?? null;
            $order = is_numeric($orderVal) ? (int) $orderVal : null;

            $model = new Pejabat();
            $model->fill([
                'nama' => $assoc['nama'] ?? null,
                'ttl' => $assoc['ttl'] ?? null,
                'pendidikan' => $assoc['pendidikan'] ?? null,
                'jabatan' => $assoc['jabatan'] ?? null,
                'description' => $assoc['description'] ?? null,
                'image' => $imagePath,
                'order' => $order,
                'published' => $published,
            ]);

            // Set timestamps bila tersedia di CSV
            if (!empty($assoc['created_at'])) {
                $model->created_at = \Carbon\Carbon::parse($assoc['created_at']);
            }
            if (!empty($assoc['updated_at'])) {
                $model->updated_at = \Carbon\Carbon::parse($assoc['updated_at']);
            }

            $model->save();
        }

        fclose($handle);
    }
}