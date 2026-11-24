<?php

use App\Enums\PenawaranStatus;
use App\Models\AdminContent;
use App\Models\Consultation;
use App\Models\Penawaran;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('persists full proposal schema fields and relations', function () {
    $consultation = Consultation::factory()->create([
        'tipe_klien' => 'korporat',
        'hasil_diinginkan' => 'Meningkatkan keakraban tim dan sinergi antar divisi',
    ]);

    expect($consultation->tipe_klien)->toBe('korporat')
        ->and($consultation->hasil_diinginkan)->toBe('Meningkatkan keakraban tim dan sinergi antar divisi');

    $offer = Penawaran::create([
        'id_konsultasi' => $consultation->getKey(),
        'versi' => 1,
        'status' => PenawaranStatus::Draft,
        'judul' => 'Proposal Gathering PT Maju Bersama',
        'pemahaman' => 'Klien membutuhkan gathering 3 hari dengan fokus kebersamaan tim.',
        'cakupan_termasuk' => 'Transportasi, akomodasi, konsumsi, fasilitator.',
        'cakupan_tidak' => 'Pengeluaran pribadi.',
        'keselamatan' => 'Tim tersertifikasi P3K dan asuransi aktif.',
        'syarat' => 'Harga berlaku 14 hari.',
        'berlaku_hari' => 14,
        'logo_path' => 'proposals/logos/sample.png',
        'total' => 15000000,
    ]);

    $day = $offer->days()->create([
        'urutan' => 1,
        'judul' => 'Hari 1: Kedatangan & Ice Breaking',
        'isi' => 'Penjemputan di bandara, check-in hotel, sesi ice breaking sore.',
    ]);

    $photo = $offer->photos()->create([
        'jenis' => 'sampul',
        'nama_destinasi' => 'Yogyakarta',
        'path' => 'proposals/photos/sample.jpg',
        'keterangan' => 'Foto lokasi utama',
        'urutan' => 1,
    ]);

    expect($offer->fresh()->pemahaman)->toBe('Klien membutuhkan gathering 3 hari dengan fokus kebersamaan tim.')
        ->and($offer->fresh()->berlaku_hari)->toBe(14)
        ->and($offer->days)->toHaveCount(1)
        ->and($offer->days->first()->judul)->toBe('Hari 1: Kedatangan & Ice Breaking')
        ->and($offer->photos)->toHaveCount(1)
        ->and($offer->photos->first()->jenis)->toBe('sampul');
});

it('seeds the three new services in website content', function () {
    $this->artisan('db:seed', ['--class' => 'WebsiteContentSeeder']);

    $services = AdminContent::query()
        ->where('jenis', 'layanan')
        ->pluck('slug')
        ->all();

    expect($services)->toContain('kunjungan-industri-edukasi')
        ->toContain('pelatihan-pengembangan-sdm')
        ->toContain('hiburan-awarding');
});
