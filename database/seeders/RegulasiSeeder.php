<?php

namespace Database\Seeders;

use App\Models\Regulasi;
use App\Models\File;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class RegulasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = database_path('seeders/regulasi/regulasi.csv');

        if (!file_exists($csvPath)) {
            $this->command?->warn('CSV tidak ditemukan: ' . $csvPath);
            return;
        }

        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            $this->command?->error('Gagal membuka CSV: ' . $csvPath);
            return;
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            $this->command?->error('Header CSV tidak valid atau kosong.');
            return;
        }

        // Normalisasi header ke lowercase untuk pemetaan yang aman
        $normalizedHeader = array_map(function ($h) {
            return strtolower(trim((string) $h));
        }, $header);

        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $data = [];
            foreach ($row as $index => $value) {
                $key = $normalizedHeader[$index] ?? ('col_' . $index);
                $data[$key] = $value;
            }

            try {
                $title = trim((string) ($data['title'] ?? ''));
                if ($title === '') {
                    // Lewati baris tanpa judul
                    continue;
                }

                // content diisi dari kolom 'keterangan' CSV (tanpa fallback khusus)
                $content = isset($data['keterangan']) ? (string) $data['keterangan'] : '';

                // is_active selalu true sesuai instruksi
                $isActive = true;

                // file diisi dengan 'filename'
                // Prefer kolom 'filename' di CSV jika ada; jika tidak, ambil dari tabel files via file_id
                $fileName = null;
                if (array_key_exists('filename', $data) && trim((string) $data['filename']) !== '') {
                    $fileName = (string) $data['filename'];
                } else {
                    $fileIdRaw = $data['file_id'] ?? null;
                    if ($fileIdRaw !== null && strtolower((string) $fileIdRaw) !== 'null' && trim((string) $fileIdRaw) !== '') {
                        $fid = (int) $fileIdRaw;
                        if ($fid > 0) {
                            $file = File::find($fid);
                            if ($file && !empty($file->filename)) {
                                $fileName = $file->filename;
                            }
                        }
                    }
                }

                $createdAtRaw = $data['created_at'] ?? null;
                $updatedAtRaw = $data['updated_at'] ?? null;

                $regulasi = new Regulasi();
                $regulasi->title = $title;
                $regulasi->content = $content;
                $regulasi->file = $fileName; // setter akan handle string/null
                $regulasi->is_active = $isActive;

                // Set timestamps dari CSV jika tersedia
                $regulasi->created_at = $this->parseDate($createdAtRaw) ?? now();
                $regulasi->updated_at = $this->parseDate($updatedAtRaw) ?? $regulasi->created_at;

                $regulasi->save();
                $count++;
            } catch (\Throwable $e) {
                Log::warning('Gagal seed regulasi dari baris CSV', [
                    'error' => $e->getMessage(),
                    'row' => $data,
                ]);
            }
        }

        fclose($handle);
        $this->command?->info("Berhasil men-seed {$count} data regulasi dari CSV.");
    }

    private function parseDate($value): ?\Carbon\Carbon
    {
        try {
            $str = trim((string) $value);
            if ($str === '' || strtolower($str) === 'null') {
                return null;
            }
            return Carbon::parse($str);
        } catch (\Throwable $e) {
            return null;
        }
    }
}