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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('aktif')->default(true);
        });

        Schema::create('konten_admin', function (Blueprint $table) {
            $table->id('id_konten');
            $table->string('jenis', 32);
            $table->string('judul', 190);
            $table->string('slug')->nullable();
            $table->text('deskripsi')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status', 20)->default('tampil');
            $table->boolean('aktif')->default(true);
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();

            $table->index(['jenis', 'aktif', 'urutan']);
        });

        Schema::create('audit_log', function (Blueprint $table) {
            $table->id('id_audit');
            $table->foreignId('id_user')->nullable()->constrained('users')->nullOnDelete();
            $table->string('aksi', 100);
            $table->string('objek', 120);
            $table->unsignedBigInteger('id_objek')->nullable();
            $table->json('sebelum')->nullable();
            $table->json('sesudah')->nullable();
            $table->timestamps();

            $table->index(['objek', 'id_objek']);
            $table->index(['aksi', 'created_at']);
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

        Schema::create('rekomendasi_log', function (Blueprint $table) {
            $table->id('id_log');
            $table->json('preferensi_json');
            $table->json('hasil_json');
            $table->unsignedInteger('latensi_ms')->nullable();
            $table->timestamps();
        });

        Schema::create('faq_chatbot', function (Blueprint $table) {
            $table->id('id_faq');
            $table->json('kata_kunci');
            $table->string('pertanyaan', 500);
            $table->text('jawaban');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('pengaturan_bisnis', function (Blueprint $table) {
            $table->string('kunci')->primary();
            $table->string('nilai', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_bisnis');
        Schema::dropIfExists('faq_chatbot');
        Schema::dropIfExists('rekomendasi_log');
        Schema::dropIfExists('chatbot_log');
        Schema::dropIfExists('audit_log');
        Schema::dropIfExists('konten_admin');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('aktif');
        });
    }
};
