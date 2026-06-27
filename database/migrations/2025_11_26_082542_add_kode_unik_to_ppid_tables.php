<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('permohonan_informasi', function (Blueprint $table) {
            $table->string('kode_unik')->unique()->nullable()->after('id');
        });

        Schema::table('pengajuan_keberatan', function (Blueprint $table) {
            $table->string('kode_unik')->unique()->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_informasi', function (Blueprint $table) {
            $table->dropColumn('kode_unik');
        });

        Schema::table('pengajuan_keberatan', function (Blueprint $table) {
            $table->dropColumn('kode_unik');
        });
    }
};
