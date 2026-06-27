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
        Schema::create('bumd', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique();
            $table->string('nama_pendek')->unique();
            $table->string('nama')->unique();
            $table->string('kategory');
            $table->string('sektor');
            $table->string('bidang_usaha');
            $table->string('hasil_usaha')->nullable();
            $table->string('alamat');
            $table->string('hotline')->nullable();
            $table->string('telp')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();
            $table->text('tujuan')->nullable();
            $table->text('logo')->nullable();

            $table->text('akta_pendirian')->nullable();
            $table->text('akta_perubahan')->nullable();
            $table->text('dasar_hukum')->nullable();
            $table->text('nilai_saham')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bumd');
    }
};
