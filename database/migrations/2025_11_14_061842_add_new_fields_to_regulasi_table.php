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
            $table->string('tipe_dokumen')->nullable()->after('title');
            $table->string('judul_peraturan')->nullable()->after('tipe_dokumen');
            $table->string('nomor_peraturan')->nullable()->after('judul_peraturan');
            $table->string('tahun_peraturan', 4)->nullable()->after('nomor_peraturan');
            $table->string('jenis_peraturan')->nullable()->after('tahun_peraturan');
            $table->string('singkatan_jenis')->nullable()->after('jenis_peraturan');
            $table->string('tempat_penetapan')->nullable()->after('singkatan_jenis');
            $table->date('tanggal_penetapan')->nullable()->after('tempat_penetapan');
            $table->date('tanggal_pengundangan')->nullable()->after('tanggal_penetapan');
            $table->string('sumber')->nullable()->after('tanggal_pengundangan');
            $table->text('subjek')->nullable()->after('sumber');
            $table->string('status_peraturan')->nullable()->after('subjek');
            $table->text('keterangan_dokumen')->nullable()->after('status_peraturan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regulasi', function (Blueprint $table) {
            $table->dropColumn([
                'tipe_dokumen',
                'judul_peraturan',
                'nomor_peraturan',
                'tahun_peraturan',
                'jenis_peraturan',
                'singkatan_jenis',
                'tempat_penetapan',
                'tanggal_penetapan',
                'tanggal_pengundangan',
                'sumber',
                'subjek',
                'status_peraturan',
                'keterangan_dokumen',
            ]);
        });
    }
};
