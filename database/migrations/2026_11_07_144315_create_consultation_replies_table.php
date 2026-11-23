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
        Schema::create('balasan_konsultasi', function (Blueprint $table) {
            $table->id('id_balasan');
            $table->foreignId('id_konsultasi')
                ->constrained('konsultasi', 'id_konsultasi')
                ->cascadeOnDelete();
            $table->foreignId('id_pengirim')->constrained('users')->cascadeOnDelete();
            $table->text('pesan');
            $table->boolean('terkirim_email')->default(false);
            $table->timestamps();

            $table->index(['id_konsultasi', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balasan_konsultasi');
    }
};
