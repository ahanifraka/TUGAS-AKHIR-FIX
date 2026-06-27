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
        Schema::create('kondisi_keuangans', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('bumd_id')
                  ->constrained('bumd')
                  ->onDelete('cascade');

            $table->integer('no_akun');
            $table->string('nama_akun');
            $table->bigInteger('tahun_2020')->default(0);
            $table->bigInteger('tahun_2021')->default(0);
            $table->bigInteger('tahun_2022')->default(0);
            $table->bigInteger('tahun_2023')->default(0);
            $table->bigInteger('tahun_2024')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kondisi_keuangans');
    }
};
