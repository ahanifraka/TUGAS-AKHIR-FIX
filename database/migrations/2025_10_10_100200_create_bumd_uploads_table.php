<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bumd_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bumd');
            $table->unsignedBigInteger('kategori_id')->nullable();
            $table->string('periode')->nullable();
            $table->string('title');
            $table->text('keterangan')->nullable();
            $table->text('cover')->nullable();
            $table->unsignedBigInteger('file_id')->nullable();
            $table->string('akses')->default('public');
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('kategori_id')->references('id')->on('upload_categories')->nullOnDelete();
            $table->foreign('file_id')->references('id')->on('files')->nullOnDelete();
            $table->foreign('kode_bumd')->references('kode')->on('bumd')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bumd_uploads');
    }
};