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
        Schema::table('konsultasi', function (Blueprint $table) {
            $table->string('jenis_kendaraan', 30)->nullable();
            $table->string('lokasi_jemput')->nullable();
            $table->time('jam_jemput')->nullable();
            $table->date('tanggal_kembali')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('konsultasi', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_kendaraan',
                'lokasi_jemput',
                'jam_jemput',
                'tanggal_kembali',
            ]);
        });
    }
};
