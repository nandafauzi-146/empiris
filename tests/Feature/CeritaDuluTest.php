<?php

use App\Models\AdminContent;
use App\Models\RecommendationLog;
use App\Services\PiiMasker;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('masks pii email and phone numbers correctly', function () {
    $text = 'Halo saya Budi hubungi saya di budi.santoso@example.com atau whatsapp 081234567890 untuk acara kami.';
    $masked = PiiMasker::mask($text);

    expect($masked)->not->toContain('budi.santoso@example.com')
        ->not->toContain('081234567890')
        ->toContain('[EMAIL_DISAMARKAN]')
        ->toContain('[TELEPON_DISAMARKAN]');
});

it('validates story recommendation endpoint input', function () {
    $response = $this->postJson(route('recommendation.story'), [
        'cerita' => 'pendek',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['cerita']);
});

it('generates story recommendation with pemahaman ringkas and 3 destinations without prices', function () {
    AdminContent::create([
        'jenis' => 'destinasi',
        'judul' => 'Yogyakarta',
        'deskripsi' => 'Wisata budaya dan keraton.',
        'metadata' => ['wilayah' => 'Jawa Tengah & DIY'],
        'aktif' => true,
    ]);
    AdminContent::create([
        'jenis' => 'destinasi',
        'judul' => 'Malang',
        'deskripsi' => 'Wisata alam dan pegunungan.',
        'metadata' => ['wilayah' => 'Jawa Timur'],
        'aktif' => true,
    ]);
    AdminContent::create([
        'jenis' => 'destinasi',
        'judul' => 'Solo',
        'deskripsi' => 'Kota batik dan kuliner.',
        'metadata' => ['wilayah' => 'Jawa Tengah & DIY'],
        'aktif' => true,
    ]);

    $response = $this->postJson(route('recommendation.story'), [
        'cerita' => 'Kami dari tim kantor 30 orang ingin mengadakan gathering santai di daerah sejuk dengan kuliner enak selama 3 hari.',
        'tipe_klien' => 'korporat',
        'durasi_hari' => 3,
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'status',
            'data' => [
                'pemahaman_ringkas',
                'tipe_klien',
                'layanan_disarankan',
                'destinasi',
                'hari',
                'pertanyaan_lanjutan',
                'disclaimer',
                'gambaran_awal',
            ],
        ]);

    $data = $response->json('data');
    expect($data['pemahaman_ringkas'])->not->toBeEmpty()
        ->and($data['destinasi'])->toHaveCount(3)
        ->and($data['gambaran_awal'])->toBeTrue();

    // R2 check: pastikan tidak ada format rupiah
    $rawJson = json_encode($data);
    expect($rawJson)->not->toMatch('/Rp\s*[\d.,]+/')
        ->not->toMatch('/\b\d+\s*(ribu|juta|rb|jt)\b/i');

    // Pastikan tercatat di rekomendasi_log
    expect(RecommendationLog::count())->toBe(1);
});
