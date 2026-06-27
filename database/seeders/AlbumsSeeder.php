<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Album;
use App\Models\AlbumImage;
use Carbon\Carbon;

class AlbumsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Albums
        $albumsCsvPath = database_path('seeders/albums/albums-20251027.csv');
        if (!is_file($albumsCsvPath)) {
            $this->command?->warn("CSV file not found at: {$albumsCsvPath}");
        } else {
            $handle = fopen($albumsCsvPath, 'r');
            if ($handle === false) {
                $this->command?->warn("Unable to open CSV file: {$albumsCsvPath}");
            } else {
                // Read header
                $header = fgetcsv($handle);
                if ($header === false) {
                    fclose($handle);
                    $this->command?->warn("CSV header missing for albums.");
                } else {
                    // Normalize header: strip BOM and trim
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
                            $this->command?->warn('Skipping album row: missing title');
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
                            $published = in_array(strtolower((string) $publishedRaw), ['1', 'true', 'yes'], true);
                        }

                        $data = [
                            'title' => $title,
                            'description' => ($description === '' ? null : $description),
                            'image' => $image,
                            'published' => $published ?? true,
                        ];

                        // Prefer seeding by CSV id to keep associations stable
                        $idCsv = $indices['id'] !== null ? ($row[$indices['id']] ?? null) : null;
                        $album = null;
                        if (!empty($idCsv)) {
                            $album = Album::find($idCsv) ?? new Album();
                            $album->id = $idCsv;
                        } else {
                            $album = Album::firstOrNew(['title' => $title]);
                        }

                        $album->fill($data);

                        // Guard: ensure title not empty (already checked), and avoid empty strings
                        $album->title = trim((string) $album->title);

                        // Apply timestamps from CSV if available
                        $createdAtCsv = $indices['created_at'] !== null ? ($row[$indices['created_at']] ?? null) : null;
                        $updatedAtCsv = $indices['updated_at'] !== null ? ($row[$indices['updated_at']] ?? null) : null;

                        try {
                            if (!empty($createdAtCsv)) {
                                $album->created_at = Carbon::parse($createdAtCsv);
                            }
                        } catch (\Throwable $e) {
                            // ignore parse errors
                        }

                        try {
                            if (!empty($updatedAtCsv)) {
                                $album->updated_at = Carbon::parse($updatedAtCsv);
                            }
                        } catch (\Throwable $e) {
                            // ignore parse errors
                        }

                        // Persist without auto-updating timestamps
                        $album->timestamps = false;
                        $album->save();
                        $album->timestamps = true;
                    }

                    fclose($handle);
                }
            }
        }

        // Seed Album Images
        $imagesCsvPath = database_path('seeders/albums/album_images-20251027.csv');
        if (!is_file($imagesCsvPath)) {
            $this->command?->warn("CSV file not found at: {$imagesCsvPath}");
            return;
        }

        $imagesHandle = fopen($imagesCsvPath, 'r');
        if ($imagesHandle === false) {
            $this->command?->warn("Unable to open CSV file: {$imagesCsvPath}");
            return;
        }

        $imagesHeader = fgetcsv($imagesHandle);
        if ($imagesHeader === false) {
            fclose($imagesHandle);
            $this->command?->warn("CSV header missing for album images.");
            return;
        }

        // Normalize header: strip BOM and trim
        if (isset($imagesHeader[0]) && is_string($imagesHeader[0])) {
            $imagesHeader[0] = preg_replace('/^\xEF\xBB\xBF/', '', $imagesHeader[0]);
        }
        $imagesHeader = array_map(static function ($h) {
            return is_string($h) ? trim($h) : $h;
        }, $imagesHeader);

        $getImagesIndex = function (string $name) use ($imagesHeader): ?int {
            $idx = array_search($name, $imagesHeader, true);
            return $idx === false ? null : $idx;
        };

        $imgIndices = [
            'id' => $getImagesIndex('id'),
            'album_id' => $getImagesIndex('album_id'),
            'image' => $getImagesIndex('image'),
            'title' => $getImagesIndex('title'),
            'description' => $getImagesIndex('description'),
            'published' => $getImagesIndex('published'),
            'created_at' => $getImagesIndex('created_at'),
            'updated_at' => $getImagesIndex('updated_at'),
        ];

        while (($row = fgetcsv($imagesHandle)) !== false) {
            $albumIdCsv = $imgIndices['album_id'] !== null ? ($row[$imgIndices['album_id']] ?? null) : null;
            if (empty($albumIdCsv)) {
                $this->command?->warn('Skipping image row: missing album_id');
                continue;
            }

            // Ensure album exists before creating image
            $albumExists = Album::find($albumIdCsv);
            if (!$albumExists) {
                // If albums were not seeded by id, skip to avoid broken FK
                $this->command?->warn("Skipping image row: album_id {$albumIdCsv} not found");
                continue;
            }

            // Parse image column (JSON or raw string)
            $imageField = $imgIndices['image'] !== null ? ($row[$imgIndices['image']] ?? null) : null;
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

            if (empty($image)) {
                $this->command?->warn("Skipping image row for album_id {$albumIdCsv}: missing image");
                continue;
            }

            // Normalize published
            $publishedRaw = $imgIndices['published'] !== null ? ($row[$imgIndices['published']] ?? null) : null;
            $published = null;
            if ($publishedRaw !== null) {
                $published = in_array(strtolower((string) $publishedRaw), ['1', 'true', 'yes'], true);
            }

            $title = $imgIndices['title'] !== null ? ($row[$imgIndices['title']] ?? null) : null;
            $description = $imgIndices['description'] !== null ? ($row[$imgIndices['description']] ?? null) : null;

            $data = [
                'album_id' => (int) $albumIdCsv,
                'image' => $image,
                'title' => ($title === '' ? null : $title),
                'description' => ($description === '' ? null : $description),
                'published' => $published ?? true,
            ];

            $idCsv = $imgIndices['id'] !== null ? ($row[$imgIndices['id']] ?? null) : null;
            $albumImage = null;
            if (!empty($idCsv)) {
                $albumImage = AlbumImage::find($idCsv) ?? new AlbumImage();
                $albumImage->id = $idCsv;
                $albumImage->fill($data);
            } else {
                $albumImage = new AlbumImage($data);
            }

            // Apply timestamps from CSV if available
            $createdAtCsv = $imgIndices['created_at'] !== null ? ($row[$imgIndices['created_at']] ?? null) : null;
            $updatedAtCsv = $imgIndices['updated_at'] !== null ? ($row[$imgIndices['updated_at']] ?? null) : null;

            try {
                if (!empty($createdAtCsv)) {
                    $albumImage->created_at = Carbon::parse($createdAtCsv);
                }
            } catch (\Throwable $e) {
                // ignore parse errors
            }

            try {
                if (!empty($updatedAtCsv)) {
                    $albumImage->updated_at = Carbon::parse($updatedAtCsv);
                }
            } catch (\Throwable $e) {
                // ignore parse errors
            }

            // Persist without auto-updating timestamps
            $albumImage->timestamps = false;
            $albumImage->save();
            $albumImage->timestamps = true;
        }

        fclose($imagesHandle);
    }
}
