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
        Schema::create('proyek', function (Blueprint $table) {
            $table->id('id_proyek');
            $table->string('nomor_proyek')->unique()->nullable();
            $table->foreignId('id_konsultasi')->unique()->constrained('konsultasi', 'id_konsultasi')->restrictOnDelete();
            $table->foreignId('id_klien')->constrained('users')->restrictOnDelete();
            $table->string('nama', 190);
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->string('status', 20)->default('terjadwal');
            $table->unsignedBigInteger('total_nilai');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['status', 'tgl_mulai']);
        });

        Schema::create('termin_pembayaran', function (Blueprint $table) {
            $table->id('id_termin');
            $table->foreignId('id_proyek')->constrained('proyek', 'id_proyek')->restrictOnDelete();
            $table->unsignedTinyInteger('urutan');
            $table->string('nama', 120);
            $table->unsignedBigInteger('nominal');
            $table->date('jatuh_tempo');
            $table->string('status', 20)->default('belum_bayar');
            $table->unsignedBigInteger('jumlah_dibayar')->default(0);
            $table->date('tgl_bayar')->nullable();
            $table->string('bukti_path')->nullable();
            $table->timestamps();

            $table->unique(['id_proyek', 'urutan']);
        });

        Schema::create('penanda_kapasitas', function (Blueprint $table) {
            $table->id('id_penanda');
            $table->date('tanggal');
            $table->string('jenis', 20);
            $table->foreignId('dibuat_oleh')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['tanggal', 'jenis']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penanda_kapasitas');
        Schema::dropIfExists('termin_pembayaran');
        Schema::dropIfExists('proyek');
    }
};
