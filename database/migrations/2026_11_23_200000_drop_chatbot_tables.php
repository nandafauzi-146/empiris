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
        Schema::dropIfExists('chatbot_log');
        Schema::dropIfExists('faq_chatbot');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('faq_chatbot', function (Blueprint $table) {
            $table->id('id_faq');
            $table->json('kata_kunci');
            $table->string('pertanyaan', 500);
            $table->text('jawaban');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('chatbot_log', function (Blueprint $table) {
            $table->id('id_log');
            $table->string('mode', 20);
            $table->foreignId('id_klien')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ringkasan_pertanyaan', 255);
            $table->boolean('terjawab')->default(false);
            $table->string('status', 20)->default('ok');
            $table->unsignedInteger('latensi_ms')->nullable();
            $table->timestamps();

            $table->index(['terjawab', 'created_at']);
        });
    }
};
