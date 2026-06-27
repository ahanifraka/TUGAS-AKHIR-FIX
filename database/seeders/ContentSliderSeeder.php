<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContentSlider;
use Carbon\Carbon;
use function array_map;

class ContentSliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dir = database_path('seeders/contentslider');
        $csvPath = $this->resolveLatestCsv($dir, 'content_sliders*.csv');
        if (!$csvPath || !is_file($csvPath)) {
            $this->command?->warn("CSV file not found in: {$dir}");
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
            'title' => $getIndex('title'),
            'description' => $getIndex('description'),
            'image' => $getIndex('image'),
            'published' => $getIndex('published'),
            'created_at' => $getIndex('created_at'),
            'updated_at' => $getIndex('updated_at'),
        ];

        while (($row = fgetcsv($handle)) !== false) {
            $title = $indices['title'] !== null ? ($row[$indices['title']] ?? null) : null;
            if (!$title) {
                continue;
            }

            $description = $indices['description'] !== null ? ($row[$indices['description']] ?? null) : null;

            // Parse image column (JSON or raw string)
            $imageField = $indices['image'] !== null ? ($row[$indices['image']] ?? null) : null;
            $image = null;
            if (!empty($imageField)) {
                $decoded = json_decode($imageField, true);
                if (is_array($decoded)) {
                    $image = $decoded['original']['url']
                        ?? $decoded['large']['url']
                        ?? $decoded['medium']['url']
                        ?? $decoded['small']['url']
                        ?? $decoded['thumbnail']['url']
                        ?? null;
                } else {
                    $image = $imageField;
                }
            }

            // Normalize published
            $publishedRaw = $indices['published'] !== null ? ($row[$indices['published']] ?? null) : null;
            $published = null;
            if ($publishedRaw !== null) {
                $published = in_array(strtolower(trim((string) $publishedRaw)), ['1', 'true', 'yes', 'y'], true);
            }

            $data = [
                'title' => $title,
                'description' => ($description === '' ? null : $description),
                'image' => $image,
                'published' => $published ?? true,
            ];

            $slider = ContentSlider::firstOrNew(['title' => $title]);
            $slider->fill($data);

            // Apply timestamps from CSV if available
            $createdAtCsv = $indices['created_at'] !== null ? ($row[$indices['created_at']] ?? null) : null;
            $updatedAtCsv = $indices['updated_at'] !== null ? ($row[$indices['updated_at']] ?? null) : null;

            try {
                if (!empty($createdAtCsv)) {
                    $slider->created_at = Carbon::parse($createdAtCsv);
                }
            } catch (\Throwable $e) {
                // ignore parse errors
            }

            try {
                if (!empty($updatedAtCsv)) {
                    $slider->updated_at = Carbon::parse($updatedAtCsv);
                }
            } catch (\Throwable $e) {
                // ignore parse errors
            }

            // Persist without auto-updating timestamps
            $slider->timestamps = false;
            $slider->save();
            $slider->timestamps = true;
        }

        fclose($handle);
    }

    /**
     * Resolve the most recent CSV file matching the pattern in the given directory.
     */
    private function resolveLatestCsv(string $dir, string $pattern): ?string
    {
        if (!is_dir($dir)) {
            return null;
        }

        $files = glob($dir . DIRECTORY_SEPARATOR . $pattern);
        if (!$files || count($files) === 0) {
            return null;
        }

        usort($files, static function ($a, $b) {
            return (filemtime($b) <=> filemtime($a));
        });

        return $files[0] ?? null;
    }
}