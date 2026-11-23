<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penawaran', function (Blueprint $table) {
            $table->id('id_penawaran');
            $table->foreignId('id_konsultasi')->constrained('konsultasi', 'id_konsultasi')->restrictOnDelete();
            $table->unsignedSmallInteger('versi')->default(1);
            $table->string('status', 20)->default('draft');
            $table->string('judul', 190);
            $table->text('catatan_admin')->nullable();
            $table->text('catatan_klien')->nullable();
            $table->unsignedBigInteger('total')->default(0);
            $table->timestamp('dikirim_at')->nullable();
            $table->timestamp('ditanggapi_at')->nullable();
            $table->timestamp('disetujui_at')->nullable();
            $table->timestamps();

            $table->unique(['id_konsultasi', 'versi']);
        });

        Schema::create('detail_penawaran', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_penawaran')->constrained('penawaran', 'id_penawaran')->cascadeOnDelete();
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->string('nama_item', 190);
            $table->text('deskripsi')->nullable();
            $table->decimal('jumlah', 10, 2)->default(1);
            $table->string('satuan', 30)->default('paket');
            $table->unsignedBigInteger('harga_satuan')->nullable();
            $table->string('asal', 20)->default('manual');
            $table->timestamps();

            $table->index(['id_penawaran', 'urutan']);
        });

        Schema::table('proyek', function (Blueprint $table) {
            $table->foreignId('id_penawaran')
                ->nullable()
                ->unique()
                ->after('id_konsultasi')
                ->constrained('penawaran', 'id_penawaran')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('proyek', fn (Blueprint $table) => $table->dropConstrainedForeignId('id_penawaran'));
        Schema::dropIfExists('detail_penawaran');
        Schema::dropIfExists('penawaran');
    }
};
