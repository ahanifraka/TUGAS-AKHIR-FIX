<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('albums', function (Blueprint $table) {
            // Ensure columns can hold JSON strings
            $table->text('title')->change();
            $table->longText('description')->nullable()->change();
        });

        // Backfill existing scalar values into JSON objects {"id": "..."}
        $rows = DB::table('albums')->select('id', 'title', 'description')->get();
        foreach ($rows as $row) {
            $title = $row->title;
            $desc = $row->description;
            $titleJson = null;
            $descJson = null;
            // Only wrap non-JSON scalars
            if (is_string($title)) {
                $trim = trim($title);
                $titleJson = $this->isJson($trim) ? $trim : json_encode(['id' => $trim], JSON_UNESCAPED_UNICODE);
            }
            if (is_string($desc) || $desc === null) {
                $trim = is_string($desc) ? trim($desc) : null;
                if ($trim === null || $trim === '') {
                    $descJson = json_encode(['id' => ''], JSON_UNESCAPED_UNICODE);
                } else {
                    $descJson = $this->isJson($trim) ? $trim : json_encode(['id' => $trim], JSON_UNESCAPED_UNICODE);
                }
            }
            DB::table('albums')->where('id', $row->id)->update([
                'title' => $titleJson,
                'description' => $descJson,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Extract Indonesian ('id') locale back to scalar columns
        $rows = DB::table('albums')->select('id', 'title', 'description')->get();
        foreach ($rows as $row) {
            $title = $row->title;
            $desc = $row->description;
            $titleOut = $title;
            $descOut = $desc;
            if (is_string($title) && $this->isJson($title)) {
                $arr = json_decode($title, true);
                $titleOut = $arr['id'] ?? reset($arr) ?? '';
            }
            if (is_string($desc) && $this->isJson($desc)) {
                $arr = json_decode($desc, true);
                $descOut = $arr['id'] ?? reset($arr) ?? '';
            }
            DB::table('albums')->where('id', $row->id)->update([
                'title' => $titleOut,
                'description' => $descOut,
            ]);
        }

        Schema::table('albums', function (Blueprint $table) {
            $table->string('title')->change();
            $table->text('description')->nullable()->change();
        });
    }

    private function isJson(?string $value): bool
    {
        if ($value === null || $value === '') return false;
        json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE && (str_starts_with(trim($value), '{') || str_starts_with(trim($value), '['));
    }
};
