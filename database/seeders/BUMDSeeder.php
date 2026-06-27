<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bumd;
use Carbon\Carbon;

class BUMDSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = database_path('seeders/bumd/bumd_202511031314.csv');
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
            'kode' => $getIndex('kode'),
            'nama_pendek' => $getIndex('nama_pendek'),
            'nama' => $getIndex('nama'),
            'kategory' => $getIndex('kategory'),
            'sektor' => $getIndex('sektor'),
            'bidang_usaha' => $getIndex('bidang_usaha'),
            'hasil_usaha' => $getIndex('hasil_usaha'),
            'alamat' => $getIndex('alamat'),
            'hotline' => $getIndex('hotline'),
            'telp' => $getIndex('telp'),
            'fax' => $getIndex('fax'),
            'email' => $getIndex('email'),
            'website' => $getIndex('website'),
            'visi' => $getIndex('visi'),
            'misi' => $getIndex('misi'),
            'tujuan' => $getIndex('tujuan'),
            'logo' => $getIndex('logo'),
            'akta_pendirian' => $getIndex('akta_pendirian'),
            'akta_perubahan' => $getIndex('akta_perubahan'),
            'dasar_hukum' => $getIndex('dasar_hukum'),
            'nilai_saham' => $getIndex('nilai_saham'),
            'created_at' => $getIndex('created_at'),
            'updated_at' => $getIndex('updated_at'),
        ];

        while (($row = fgetcsv($handle)) !== false) {
            $kode = $indices['kode'] !== null ? ($row[$indices['kode']] ?? null) : null;
            if (!$kode) {
                continue;
            }

            // Helper to decode JSON arrays and join into a single text
            $decodeArrayToText = function ($raw) {
                if (!is_string($raw) || $raw === '') {
                    return $raw;
                }
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    // Join array of strings into paragraphs
                    return implode("\n", array_map(static function ($v) {
                        return is_string($v) ? trim($v) : (string)$v;
                    }, $decoded));
                }
                return $raw;
            };

            $data = [
                'nama_pendek' => $indices['nama_pendek'] !== null ? ($row[$indices['nama_pendek']] ?? null) : null,
                'nama' => $indices['nama'] !== null ? ($row[$indices['nama']] ?? null) : null,
                'kategory' => $indices['kategory'] !== null ? ($row[$indices['kategory']] ?? null) : null,
                'sektor' => $indices['sektor'] !== null ? ($row[$indices['sektor']] ?? null) : null,
                'bidang_usaha' => $indices['bidang_usaha'] !== null ? ($row[$indices['bidang_usaha']] ?? null) : null,
                'hasil_usaha' => $indices['hasil_usaha'] !== null ? ($row[$indices['hasil_usaha']] ?? null) : null,
                'alamat' => $indices['alamat'] !== null ? ($row[$indices['alamat']] ?? null) : null,
                'hotline' => $indices['hotline'] !== null ? ($row[$indices['hotline']] ?? null) : null,
                'telp' => $indices['telp'] !== null ? ($row[$indices['telp']] ?? null) : null,
                'fax' => $indices['fax'] !== null ? ($row[$indices['fax']] ?? null) : null,
                'email' => $indices['email'] !== null ? ($row[$indices['email']] ?? null) : null,
                'website' => $indices['website'] !== null ? ($row[$indices['website']] ?? null) : null,
                'visi' => $indices['visi'] !== null ? $decodeArrayToText($row[$indices['visi']] ?? null) : null,
                'misi' => $indices['misi'] !== null ? $decodeArrayToText($row[$indices['misi']] ?? null) : null,
                'tujuan' => $indices['tujuan'] !== null ? $decodeArrayToText($row[$indices['tujuan']] ?? null) : null,
                'akta_pendirian' => $indices['akta_pendirian'] !== null ? ($row[$indices['akta_pendirian']] ?? null) : null,
                'akta_perubahan' => $indices['akta_perubahan'] !== null ? ($row[$indices['akta_perubahan']] ?? null) : null,
                'dasar_hukum' => $indices['dasar_hukum'] !== null ? ($row[$indices['dasar_hukum']] ?? null) : null,
                'nilai_saham' => $indices['nilai_saham'] !== null ? ($row[$indices['nilai_saham']] ?? null) : null,
            ];

            // Parse logo column (JSON or raw string)
            $logoField = $indices['logo'] !== null ? ($row[$indices['logo']] ?? null) : null;
            $logo = null;
            if (!empty($logoField)) {
                $decoded = json_decode($logoField, true);
                if (is_array($decoded)) {
                    $logo = $decoded['original']['url'] ?? $decoded['large']['url'] ?? $decoded['medium']['url'] ?? $decoded['small']['url'] ?? $decoded['thumbnail']['url'] ?? null;
                } else {
                    $logo = $logoField;
                }
            }
            $data['logo'] = $logo;

            // Validate required non-nullable fields to avoid DB errors
            $required = ['nama_pendek', 'nama', 'kategory', 'sektor', 'bidang_usaha', 'alamat'];
            foreach ($required as $field) {
                $val = $data[$field] ?? null;
                if ($val === null || $val === '') {
                    $this->command?->warn("Skipping '{$kode}': missing required field '{$field}'.");
                    continue 2; // skip to next CSV row
                }
            }

            // Create or update by unique 'kode'
            $bumd = Bumd::firstOrNew(['kode' => $kode]);
            $bumd->fill(array_map(function ($v) {
                return $v === '' ? null : $v;
            }, $data));

            // Apply timestamps from CSV if available
            $createdAtCsv = $indices['created_at'] !== null ? ($row[$indices['created_at']] ?? null) : null;
            $updatedAtCsv = $indices['updated_at'] !== null ? ($row[$indices['updated_at']] ?? null) : null;

            try {
                if (!empty($createdAtCsv)) {
                    $bumd->created_at = Carbon::parse($createdAtCsv);
                }
            } catch (\Throwable $e) {
                // ignore parse errors
            }

            try {
                if (!empty($updatedAtCsv)) {
                    $bumd->updated_at = Carbon::parse($updatedAtCsv);
                }
            } catch (\Throwable $e) {
                // ignore parse errors
            }

            // Persist without auto-updating timestamps
            $bumd->timestamps = false;
            $bumd->save();
            $bumd->timestamps = true;
        }

        fclose($handle);
    }
}
