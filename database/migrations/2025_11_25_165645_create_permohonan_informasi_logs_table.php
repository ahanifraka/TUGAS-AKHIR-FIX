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
        Schema::create('permohonan_informasi_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_informasi_id')->constrained('permohonan_informasi')->onDelete('cascade');
            $table->string('action'); // 'status_changed', 'note_added', 'created'
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('user_name')->nullable(); // Store name in case user is deleted
            $table->timestamps();
            
            $table->index('permohonan_informasi_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_informasi_logs');
    }
};
