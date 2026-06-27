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
        Schema::create('permohonan_informasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('alamat');
            $table->string('no_telepon_email');
            $table->text('rincian_informasi');
            $table->text('tujuan_penggunaan');
            $table->json('cara_memperoleh');
            $table->json('cara_mendapatkan_salinan')->nullable();
            $table->string('lampiran_path')->nullable();
            $table->enum('status', ['pending', 'processed', 'completed', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_informasi');
    }
};
