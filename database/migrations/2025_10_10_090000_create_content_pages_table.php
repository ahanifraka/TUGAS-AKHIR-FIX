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
        Schema::create('content_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->text('description2')->nullable();
            $table->text('image')->nullable();
            $table->string('metadata_title')->nullable();
            $table->string('metadata_keywords')->nullable();
            $table->string('metadata_description')->nullable();
            $table->text('upload_files')->nullable();
            $table->boolean('published')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_pages');
    }
};