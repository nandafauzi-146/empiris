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
        Schema::create('email_log', function (Blueprint $table) {
            $table->id('id_email');
            $table->foreignId('id_konsultasi')
                ->constrained('konsultasi', 'id_konsultasi')
                ->cascadeOnDelete();
            $table->string('jenis', 64);
            $table->string('ke_email');
            $table->string('subjek');
            $table->string('status', 32)->default('terjadwal');
            $table->timestamp('jadwal_kirim')->useCurrent();
            $table->timestamp('terkirim_at')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['id_konsultasi', 'jenis']);
            $table->index(['status', 'jadwal_kirim']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_log');
    }
};
