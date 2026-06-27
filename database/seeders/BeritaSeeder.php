<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Berita;
use App\Models\BeritaCategory;
use Carbon\Carbon;

class BeritaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = BeritaCategory::first();
        if (!$category) {
            $category = BeritaCategory::create(['category_name' => 'Umum', 'is_active' => true]);
        }

        $csvPath = database_path('seeders/berita/beritas-20251027.csv');
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

        // Build a case-insensitive header index so values can be referenced by name
        $columns = array_map('trim', $header);
        $colIndex = array_change_key_case(array_flip($columns), CASE_LOWER);

        while (($row = fgetcsv($handle)) !== false) {
            // Helper to safely get a value by column name (case-insensitive)
            $get = function (string $name) use ($row, $colIndex) {
                $key = strtolower($name);
                return isset($colIndex[$key]) ? ($row[$colIndex[$key]] ?? null) : null;
            };

            // Map CSV columns by header names
            $title = $get('title');
            if (!$title) {
                continue;
            }
            $slugCsv = $get('slug');
            $teaser = $get('teaser');
            $content = $get('content') ?? '';
            $imageField = $get('image');
            $metaTitleCsv = $get('meta_title');
            $metaKeywordCsv = $get('meta_keyword');
            $metaContentCsv = $get('meta_content');
            $publishedVal = $get('published') ?? 1;
            $createdAtCsv = $get('created_at');
            $updatedAtCsv = $get('updated_at');
            $popularVal = $get('popular');
            $categoryIdCsv = $get('category_id');

            // Normalize booleans
            $published = filter_var($publishedVal, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($published === null) {
                $published = (int)$publishedVal === 1;
            }
            $popular = null;
            if ($popularVal !== null) {
                $popular = filter_var($popularVal, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($popular === null) {
                    $popular = (int)$popularVal === 1;
                }
            }

            // Extract image URL from JSON column if provided
            $image = null;
            if (!empty($imageField)) {
                $decoded = json_decode($imageField, true);
                if (is_array($decoded)) {
                    $image = $decoded['original']['url'] ?? $decoded['large']['url'] ?? $decoded['medium']['url'] ?? $decoded['small']['url'] ?? $decoded['thumbnail']['url'] ?? null;
                } else {
                    // Fallback to raw string
                    $image = $imageField;
                }
            }

            // Generate slug if missing; ensure uniqueness by appending incrementing suffix
            $slugBase = Str::slug($slugCsv ?: $title);
            $slug = $slugBase ?: Str::slug($title) ?: Str::random(8);
            $i = 1;
            while (Berita::where('slug', $slug)->exists()) {
                $slug = $slugBase . '-' . $i;
                $i++;
            }

            // Determine category id from CSV if valid, otherwise use default category
            $categoryId = $category->id;
            if (!empty($categoryIdCsv) && is_numeric($categoryIdCsv)) {
                $foundCategory = BeritaCategory::find((int)$categoryIdCsv);
                if ($foundCategory) {
                    $categoryId = $foundCategory->id;
                }
            }

            $data = [
                'title' => $title,
                'teaser' => $teaser,
                'content' => $content,
                'image' => $image,
                'published' => (bool)$published,
                'category_id' => $categoryId,
                'meta_title' => $metaTitleCsv,
                'meta_keyword' => $metaKeywordCsv,
                'meta_content' => $metaContentCsv,
                'popular' => (bool)($popular ?? false),
                'slug' => $slug,
            ];

            // Create or update model and apply CSV timestamps
            $berita = Berita::firstOrNew(['slug' => $slug]);
            $berita->fill($data);
            // Parse timestamps from CSV if available
            $createdAt = null;
            $updatedAt = null;
            try {
                if (!empty($createdAtCsv)) {
                    $createdAt = Carbon::parse($createdAtCsv);
                }
            } catch (\Throwable $e) {
                $createdAt = null;
            }
            try {
                if (!empty($updatedAtCsv)) {
                    $updatedAt = Carbon::parse($updatedAtCsv);
                }
            } catch (\Throwable $e) {
                $updatedAt = null;
            }
            if ($createdAt) {
                $berita->created_at = $createdAt;
            }
            if ($updatedAt) {
                $berita->updated_at = $updatedAt;
            }
            // Prevent Eloquent from overriding our provided timestamps
            $berita->timestamps = false;
            $berita->save();
            $berita->timestamps = true;
        }

        fclose($handle);
    }
}