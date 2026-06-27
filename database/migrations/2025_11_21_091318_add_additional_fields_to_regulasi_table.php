<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('regulasi', function (Blueprint $table) {
            $table->string('teu_badan')->nullable()->after('keterangan_dokumen');
            $table->string('bidang_hukum')->nullable()->after('teu_badan');
            $table->string('bahasa')->nullable()->after('bidang_hukum');
            $table->string('lokasi')->nullable()->after('bahasa');
            $table->text('keterangan_status')->nullable()->after('lokasi');
            $table->text('tag')->nullable()->after('keterangan_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regulasi', function (Blueprint $table) {
            $table->dropColumn([
                'teu_badan',
                'bidang_hukum',
                'bahasa',
                'lokasi',
                'keterangan_status',
                'tag',
            ]);
        });
    }
};
