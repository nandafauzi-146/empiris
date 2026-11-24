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
        Schema::table('penawaran', function (Blueprint $table) {
            $table->text('pemahaman')->nullable()->after('judul');
            $table->text('cakupan_termasuk')->nullable()->after('catatan_admin');
            $table->text('cakupan_tidak')->nullable()->after('cakupan_termasuk');
            $table->text('keselamatan')->nullable()->after('cakupan_tidak');
            $table->text('syarat')->nullable()->after('keselamatan');
            $table->unsignedSmallInteger('berlaku_hari')->default(14)->after('total');
            $table->string('logo_path', 255)->nullable()->after('berlaku_hari');
        });

        Schema::create('penawaran_hari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_penawaran')->constrained('penawaran', 'id_penawaran')->cascadeOnDelete();
            $table->unsignedTinyInteger('urutan');
            $table->string('judul', 60);
            $table->text('isi');
            $table->timestamps();

            $table->unique(['id_penawaran', 'urutan']);
        });

        Schema::create('penawaran_foto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_penawaran')->constrained('penawaran', 'id_penawaran')->cascadeOnDelete();
            $table->string('jenis', 20); // sampul, destinasi, galeri
            $table->string('nama_destinasi', 150)->nullable();
            $table->string('path', 255);
            $table->string('keterangan', 200)->nullable();
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->timestamps();

            $table->index(['id_penawaran', 'jenis', 'urutan']);
        });

        Schema::table('konsultasi', function (Blueprint $table) {
            $table->string('tipe_klien', 20)->nullable()->after('instansi'); // korporat, kampus, instansi, umum
            $table->text('hasil_diinginkan')->nullable()->after('cerita_kebutuhan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('konsultasi', function (Blueprint $table) {
            $table->dropColumn(['tipe_klien', 'hasil_diinginkan']);
        });

        Schema::dropIfExists('penawaran_foto');
        Schema::dropIfExists('penawaran_hari');

        Schema::table('penawaran', function (Blueprint $table) {
            $table->dropColumn([
                'pemahaman',
                'cakupan_termasuk',
                'cakupan_tidak',
                'keselamatan',
                'syarat',
                'berlaku_hari',
                'logo_path',
            ]);
        });
    }
};
