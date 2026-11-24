<?php

use App\Enums\UserRole;
use App\Models\Consultation;
use App\Models\User;
use App\Services\PenawaranService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

it('allows admin to download proposal pdf and view preview', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $client = User::factory()->create(['role' => UserRole::Klien]);
    $consultation = Consultation::factory()->create([
        'id_klien' => $client->id,
        'layanan' => 'Tour & Wisata',
    ]);

    $service = app(PenawaranService::class);
    $offer = $service->buat(
        $consultation,
        [
            ['nama_item' => 'Paket Wisata Solo 2 Hari', 'jumlah' => 20, 'satuan' => 'orang', 'harga_satuan' => 400000],
        ],
        'Catatan admin',
        [
            'judul' => 'Proposal Solo Wisata Budaya',
            'pemahaman' => 'Klien merencanakan wisata budaya Solo untuk 20 orang.',
            'cakupan_termasuk' => 'Transportasi, makan, tiket.',
            'cakupan_tidak' => 'Belanja pribadi.',
            'keselamatan' => 'SOP P3K dan asuransi aktif.',
            'syarat' => 'Berlaku 14 hari.',
            'berlaku_hari' => 14,
            'days' => [
                ['judul' => 'Hari 1', 'isi' => 'Kraton Solo dan Pasar Klewer'],
                ['judul' => 'Hari 2', 'isi' => 'Museum Radya Pustaka'],
            ],
        ]
    );

    // Pratinjau Proposal oleh Admin
    $previewResponse = $this->actingAs($admin)
        ->get(route('admin.consultations.offers.preview', [$consultation, $offer]));
    $previewResponse->assertOk()
        ->assertSeeText('EMPIRISE JALAN-JALAN')
        ->assertSeeText('Proposal Solo Wisata Budaya')
        ->assertSeeText('Paket Wisata Solo 2 Hari');

    // Unduh PDF Proposal oleh Admin
    $pdfResponse = $this->actingAs($admin)
        ->get(route('admin.consultations.offers.pdf', [$consultation, $offer]));
    $pdfResponse->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

it('allows client to download own proposal pdf and forbids accessing other client proposal (R1)', function () {
    $clientA = User::factory()->create(['role' => UserRole::Klien, 'email_verified_at' => now()]);
    $clientB = User::factory()->create(['role' => UserRole::Klien, 'email_verified_at' => now()]);

    $consultationA = Consultation::factory()->create(['id_klien' => $clientA->id]);
    $service = app(PenawaranService::class);
    $offerA = $service->buat(
        $consultationA,
        [['nama_item' => 'Paket Outbound', 'jumlah' => 15, 'satuan' => 'orang', 'harga_satuan' => 350000]],
        null,
        ['judul' => 'Proposal Outbound Klien A']
    );

    // Klien A bisa mengunduh PDF proposal miliknya
    $responseA = $this->actingAs($clientA)
        ->get(route('account.offers.pdf', $offerA));
    $responseA->assertOk()
        ->assertHeader('content-type', 'application/pdf');

    // Klien B tidak bisa mengakses proposal milik Klien A (R1 Isolasi Data -> 404)
    $responseB = $this->actingAs($clientB)
        ->get(route('account.offers.pdf', $offerA));
    $responseB->assertNotFound();
});

it('allows admin to upload client logo and photos to proposal', function () {
    Storage::fake('public');

    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $client = User::factory()->create(['role' => UserRole::Klien]);
    $consultation = Consultation::factory()->create(['id_klien' => $client->id]);

    $service = app(PenawaranService::class);
    $offer = $service->buat(
        $consultation,
        [['nama_item' => 'Paket Family Gathering', 'jumlah' => 10, 'satuan' => 'orang', 'harga_satuan' => 300000]]
    );

    // Upload logo
    $logoFile = UploadedFile::fake()->image('client-logo.png');
    $this->actingAs($admin)
        ->post(route('admin.consultations.offers.logo', [$consultation, $offer]), [
            'logo' => $logoFile,
        ])
        ->assertRedirect(route('admin.consultations.show', $consultation));

    $offer->refresh();
    expect($offer->logo_path)->not->toBeNull();
    Storage::disk('public')->assertExists($offer->logo_path);

    // Upload foto destinasi
    $photoFile = UploadedFile::fake()->image('destination.jpg');
    $this->actingAs($admin)
        ->post(route('admin.consultations.offers.photos.store', [$consultation, $offer]), [
            'foto' => $photoFile,
            'jenis' => 'destinasi',
            'nama_destinasi' => 'Prambanan',
            'keterangan' => 'Foto candi utama',
        ])
        ->assertRedirect(route('admin.consultations.show', $consultation));

    $offer->refresh();
    expect($offer->photos)->toHaveCount(1)
        ->and($offer->photos->first()->jenis)->toBe('destinasi')
        ->and($offer->photos->first()->nama_destinasi)->toBe('Prambanan');
});
