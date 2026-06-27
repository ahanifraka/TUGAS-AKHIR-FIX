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
        Schema::create('album_images', function (Blueprint $table) {
            $table->id();
            $table->text('image');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->boolean('published')->default(true);
            $table->foreignId('album_id')->constrained('albums')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('album_images');
    }
};
