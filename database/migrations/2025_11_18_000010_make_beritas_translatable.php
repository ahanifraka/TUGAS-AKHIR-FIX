<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        // Alter columns to TEXT to safely store JSON translations (avoid length limits)
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE beritas MODIFY COLUMN title TEXT NOT NULL');
            DB::statement('ALTER TABLE beritas MODIFY COLUMN teaser TEXT NULL');
            DB::statement('ALTER TABLE beritas MODIFY COLUMN content LONGTEXT NOT NULL');
            DB::statement('ALTER TABLE beritas MODIFY COLUMN meta_title TEXT NULL');
            DB::statement('ALTER TABLE beritas MODIFY COLUMN meta_keyword TEXT NULL');
            DB::statement('ALTER TABLE beritas MODIFY COLUMN meta_content TEXT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE beritas ALTER COLUMN title TYPE TEXT');
            DB::statement('ALTER TABLE beritas ALTER COLUMN teaser TYPE TEXT');
            DB::statement('ALTER TABLE beritas ALTER COLUMN content TYPE TEXT');
            DB::statement('ALTER TABLE beritas ALTER COLUMN meta_title TYPE TEXT');
            DB::statement('ALTER TABLE beritas ALTER COLUMN meta_keyword TYPE TEXT');
            DB::statement('ALTER TABLE beritas ALTER COLUMN meta_content TYPE TEXT');
        } else {
            // SQLite: dynamic typing, no changes required
        }

        // Backfill existing scalar values to JSON (Indonesian locale 'id')
        $records = DB::table('beritas')->select('id','title','teaser','content','meta_title','meta_keyword','meta_content')->get();
        foreach ($records as $r) {
            $update = [];
            foreach (['title','teaser','content','meta_title','meta_keyword','meta_content'] as $field) {
                $value = $r->{$field};
                if (is_null($value)) {
                    continue;
                }
                $trim = trim((string)$value);
                // If it already looks like JSON object (starts with {), skip
                if ($trim !== '' && str_starts_with($trim, '{')) {
                    continue;
                }
                $update[$field] = json_encode(['id' => $value], JSON_UNESCAPED_UNICODE);
            }
            if ($update) {
                DB::table('beritas')->where('id', $r->id)->update($update);
            }
        }
    }

    public function down(): void
    {
        // Extract Indonesian locale back to scalar values
        $records = DB::table('beritas')->select('id','title','teaser','content','meta_title','meta_keyword','meta_content')->get();
        foreach ($records as $r) {
            $update = [];
            foreach (['title','teaser','content','meta_title','meta_keyword','meta_content'] as $field) {
                $value = $r->{$field};
                if (is_string($value) && str_starts_with(trim($value), '{')) {
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $update[$field] = $decoded['id'] ?? ($decoded['en'] ?? reset($decoded) ?? null);
                    }
                }
            }
            if ($update) {
                DB::table('beritas')->where('id', $r->id)->update($update);
            }
        }

        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE beritas MODIFY COLUMN title VARCHAR(255) NOT NULL');
            DB::statement('ALTER TABLE beritas MODIFY COLUMN teaser VARCHAR(255) NULL');
            DB::statement('ALTER TABLE beritas MODIFY COLUMN content LONGTEXT NOT NULL');
            DB::statement('ALTER TABLE beritas MODIFY COLUMN meta_title VARCHAR(255) NULL');
            DB::statement('ALTER TABLE beritas MODIFY COLUMN meta_keyword VARCHAR(255) NULL');
            DB::statement('ALTER TABLE beritas MODIFY COLUMN meta_content VARCHAR(255) NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE beritas ALTER COLUMN title TYPE VARCHAR(255)');
            DB::statement('ALTER TABLE beritas ALTER COLUMN teaser TYPE VARCHAR(255)');
            // content remains TEXT originally; leave as is for pgsql if needed
            DB::statement('ALTER TABLE beritas ALTER COLUMN meta_title TYPE VARCHAR(255)');
            DB::statement('ALTER TABLE beritas ALTER COLUMN meta_keyword TYPE VARCHAR(255)');
            DB::statement('ALTER TABLE beritas ALTER COLUMN meta_content TYPE VARCHAR(255)');
        }
    }
};
