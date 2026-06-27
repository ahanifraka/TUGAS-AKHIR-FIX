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
        Schema::create('pengajuan_keberatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('alamat');
            $table->string('pekerjaan');
            $table->string('no_telepon');
            $table->string('nomor_induk_kependudukan');
            $table->string('nomor_pokok_wajib_pajak')->nullable();
            $table->json('alasan_keberatan');
            $table->text('kasus_posisi');
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
        Schema::dropIfExists('pengajuan_keberatan');
    }
};
