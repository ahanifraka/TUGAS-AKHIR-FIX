<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('konten');
            $table->text('excerpt')->nullable();
            $table->string('gambar')->nullable();
            $table->string('dokumen')->nullable();
            $table->string('nomor_pengumuman')->nullable();
            $table->date('tanggal_terbit');
            $table->date('tanggal_berakhir')->nullable();
            $table->boolean('is_penting')->default(false);
            $table->boolean('is_aktif')->default(true);
            $table->string('tipe')->default('umum');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};