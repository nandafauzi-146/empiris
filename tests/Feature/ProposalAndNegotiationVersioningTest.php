<?php

use App\Enums\PenawaranStatus;
use App\Enums\UserRole;
use App\Models\Consultation;
use App\Models\Penawaran;
use App\Models\User;
use App\Services\PenawaranService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('creates full proposal with days and details from admin', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $client = User::factory()->create(['role' => UserRole::Klien]);
    $consultation = Consultation::factory()->create([
        'id_klien' => $client->id,
        'layanan' => 'Tour & Wisata',
        'durasi_hari' => 2,
    ]);

    $response = $this->actingAs($admin)
        ->post(route('admin.consultations.offers.store', $consultation), [
            'judul' => 'Proposal Wisata Yogyakarta 2 Hari',
            'pemahaman' => 'Klien merencanakan kegiatan wisata budaya 2 hari untuk rombongan.',
            'cakupan_termasuk' => 'Transportasi, makan, tiket wisata.',
            'cakupan_tidak' => 'Pengeluaran pribadi.',
            'keselamatan' => 'SOP P3K dan asuransi perjalanan aktif.',
            'syarat' => 'Berlaku 14 hari kalender.',
            'berlaku_hari' => 14,
            'days' => [
                ['judul' => 'Hari 1: Keraton & Malioboro', 'isi' => 'Kunjungan keraton dan belanja di Malioboro.'],
                ['judul' => 'Hari 2: Candi Prambanan', 'isi' => 'Kunjungan Candi Prambanan dan wisata kuliner.'],
            ],
            'items' => [
                [
                    'nama_item' => 'Paket Tour 2 Hari',
                    'jumlah' => 25,
                    'satuan' => 'orang',
                    'harga_satuan' => 450000,
                ],
            ],
        ]);

    $response->assertRedirect(route('admin.consultations.show', $consultation))
        ->assertSessionHas('status', 'Draf penawaran versi 1 berhasil dibuat.');

    $offer = Penawaran::first();
    expect($offer)->not->toBeNull()
        ->and($offer->judul)->toBe('Proposal Wisata Yogyakarta 2 Hari')
        ->and($offer->pemahaman)->toBe('Klien merencanakan kegiatan wisata budaya 2 hari untuk rombongan.')
        ->and($offer->keselamatan)->toBe('SOP P3K dan asuransi perjalanan aktif.')
        ->and($offer->days)->toHaveCount(2)
        ->and($offer->details)->toHaveCount(1)
        ->and($offer->total)->toBe(25 * 450000);
});

it('supports client negotiation via minta revisi and admin creates duplicated new version', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $client = User::factory()->create(['role' => UserRole::Klien, 'email_verified_at' => now()]);
    $consultation = Consultation::factory()->create(['id_klien' => $client->id]);

    $service = app(PenawaranService::class);
    $offer = $service->buat(
        $consultation,
        [
            ['nama_item' => 'Paket Gathering', 'jumlah' => 30, 'satuan' => 'orang', 'harga_satuan' => 500000],
        ],
        'Catatan admin awal',
        [
            'judul' => 'Proposal Gathering 2026',
            'pemahaman' => 'Kebutuhan gathering 30 peserta.',
            'cakupan_termasuk' => 'All in',
            'cakupan_tidak' => 'Personal expenses',
            'keselamatan' => 'Standar operasional aman',
            'syarat' => 'Berlaku 14 hari',
            'berlaku_hari' => 14,
            'days' => [
                ['judul' => 'Hari 1', 'isi' => 'Check in hotel'],
            ],
        ]
    );

    $service->kirim($offer);

    // Klien meminta revisi / negosiasi
    $response = $this->actingAs($client)
        ->post(route('api.offers.respond', $offer), [
            'aksi' => 'revisi',
            'catatan' => 'Mohon pertimbangkan potongan harga menjadi Rp 450.000 per orang untuk 30 peserta.',
        ]);

    $response->assertRedirect(route('account.consultations.show', $consultation));

    $offer->refresh();
    expect($offer->status)->toBe(PenawaranStatus::Revisi)
        ->and($offer->catatan_klien)->toContain('Rp 450.000 per orang');

    // Admin membuat versi baru dari penawaran yang direvisi
    $adminResponse = $this->actingAs($admin)
        ->post(route('admin.consultations.offers.new-version', [$consultation, $offer]));

    $adminResponse->assertRedirect(route('admin.consultations.show', $consultation))
        ->assertSessionHas('status');

    $allOffers = Penawaran::orderBy('versi')->get();
    expect($allOffers)->toHaveCount(2);

    $v1 = $allOffers[0];
    $v2 = $allOffers[1];

    expect($v1->versi)->toBe(1)
        ->and($v1->status)->toBe(PenawaranStatus::Revisi) // Versi 1 tidak berubah (R12)
        ->and($v2->versi)->toBe(2)
        ->and($v2->status)->toBe(PenawaranStatus::Draft)
        ->and($v2->judul)->toBe('Proposal Gathering 2026')
        ->and($v2->pemahaman)->toBe('Kebutuhan gathering 30 peserta.')
        ->and($v2->days)->toHaveCount(1)
        ->and($v2->details)->toHaveCount(1)
        ->and($v2->total)->toBe(30 * 500000);
});
