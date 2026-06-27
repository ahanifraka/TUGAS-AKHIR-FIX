<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure title can hold JSON by converting it to TEXT
        // Use raw SQL to avoid requiring doctrine/dbal
        $connection = DB::connection()->getDriverName();

        if ($connection === 'mysql') {
            DB::statement('ALTER TABLE content_sliders MODIFY COLUMN title TEXT NOT NULL');
            // description is already TEXT and nullable
        } elseif ($connection === 'pgsql') {
            DB::statement('ALTER TABLE content_sliders ALTER COLUMN title TYPE TEXT');
        } else {
            // For sqlite or others, attempt schema change via builder
            Schema::table('content_sliders', function ($table) {
                // No-op: SQLite handles type affinity dynamically
            });
        }

        // Backfill existing plain strings into JSON for id locale
        $sliders = DB::table('content_sliders')->select('id', 'title', 'description')->get();
        foreach ($sliders as $s) {
            $title = $s->title;
            $desc = $s->description;

            $needsTitleWrap = !is_null($title) && !(is_string($title) && str_starts_with(trim($title), '{'));
            $needsDescWrap = !is_null($desc) && !(is_string($desc) && str_starts_with(trim($desc), '{'));

            $updates = [];
            if ($needsTitleWrap) {
                $updates['title'] = json_encode(['id' => $title], JSON_UNESCAPED_UNICODE);
            }
            if ($needsDescWrap) {
                $updates['description'] = json_encode(['id' => $desc], JSON_UNESCAPED_UNICODE);
            }

            if (!empty($updates)) {
                DB::table('content_sliders')->where('id', $s->id)->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Attempt to extract `id` locale back to scalar values
        $sliders = DB::table('content_sliders')->select('id', 'title', 'description')->get();
        foreach ($sliders as $s) {
            $updates = [];
            // Title
            if (is_string($s->title) && str_starts_with(trim($s->title), '{')) {
                $decoded = json_decode($s->title, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $updates['title'] = $decoded['id'] ?? ($decoded['en'] ?? reset($decoded) ?? '');
                }
            }
            // Description
            if (!is_null($s->description) && is_string($s->description) && str_starts_with(trim($s->description), '{')) {
                $decoded = json_decode($s->description, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $updates['description'] = $decoded['id'] ?? ($decoded['en'] ?? reset($decoded) ?? null);
                }
            }
            if (!empty($updates)) {
                DB::table('content_sliders')->where('id', $s->id)->update($updates);
            }
        }

        // Change title back to VARCHAR(255) where supported
        $connection = DB::connection()->getDriverName();
        if ($connection === 'mysql') {
            DB::statement('ALTER TABLE content_sliders MODIFY COLUMN title VARCHAR(255) NOT NULL');
        } elseif ($connection === 'pgsql') {
            DB::statement('ALTER TABLE content_sliders ALTER COLUMN title TYPE VARCHAR(255)');
        }
    }
};
