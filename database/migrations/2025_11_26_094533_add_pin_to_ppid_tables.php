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
        Schema::table('permohonan_informasi', function (Blueprint $table) {
            $table->string('pin', 4)->nullable()->after('kode_unik');
        });

        Schema::table('pengajuan_keberatan', function (Blueprint $table) {
            $table->string('pin', 4)->nullable()->after('kode_unik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_informasi', function (Blueprint $table) {
            $table->dropColumn('pin');
        });

        Schema::table('pengajuan_keberatan', function (Blueprint $table) {
            $table->dropColumn('pin');
        });
    }
};
