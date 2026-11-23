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
        Schema::create('konsultasi', function (Blueprint $table) {
            $table->id('id_konsultasi');
            $table->foreignId('id_klien')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nomor_konsultasi')->unique();
            $table->string('nama');
            $table->string('email');
            $table->string('telepon', 30);
            $table->string('instansi')->nullable();
            $table->string('layanan');
            $table->string('tujuan')->nullable();
            $table->unsignedInteger('jumlah_peserta')->nullable();
            $table->unsignedSmallInteger('durasi_hari')->nullable();
            $table->date('tanggal_rencana')->nullable();
            $table->text('minat')->nullable();
            $table->text('cerita_kebutuhan')->nullable();
            $table->string('status', 32)->default('baru');
            $table->timestamps();

            $table->index(['id_klien', 'status']);
            $table->index(['status', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konsultasi');
    }
};
