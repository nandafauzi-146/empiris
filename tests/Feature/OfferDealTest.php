<?php

use App\Enums\KonsultasiStatus;
use App\Enums\PenawaranStatus;
use App\Models\Consultation;
use App\Models\Penawaran;
use App\Models\User;
use App\Services\PenawaranService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

function buatPenawaranUntukKonsultasi(Consultation $consultation, int $price = 1500000): Penawaran
{
    return app(PenawaranService::class)->buat($consultation, [[
        'nama_item' => 'Armada',
        'deskripsi' => 'Sewa armada',
        'jumlah' => 1,
        'satuan' => 'paket',
        'harga_satuan' => $price,
        'asal' => 'manual',
    ]]);
}

it('creates a sent offer and prevents sending offers with empty prices', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'klien']);
    $consultation = Consultation::factory()->create([
        'id_klien' => $client->id,
        'status' => KonsultasiStatus::Dibalas,
    ]);

    $offer = buatPenawaranUntukKonsultasi($consultation);
    app(PenawaranService::class)->kirim($offer);

    $this->assertDatabaseHas('penawaran', [
        'id_penawaran' => $offer->id_penawaran,
        'status' => 'dikirim',
    ]);
    $this->assertDatabaseHas('konsultasi', [
        'id_konsultasi' => $consultation->id_konsultasi,
        'status' => 'penawaran',
    ]);

    $emptyOffer = app(PenawaranService::class)->buat($consultation, [[
        'nama_item' => 'Hotel',
        'jumlah' => 1,
        'satuan' => 'paket',
        'harga_satuan' => null,
    ]]);

    $this->actingAs($admin)
        ->post(route('admin.consultations.offers.send', [$consultation, $emptyOffer]))
        ->assertStatus(422);
});

it('returns not found when a client responds to another clients offer', function () {
    $client = User::factory()->create(['role' => 'klien']);
    $otherClient = User::factory()->create(['role' => 'klien']);
    $consultation = Consultation::factory()->create(['id_klien' => $otherClient->id]);
    $offer = buatPenawaranUntukKonsultasi($consultation);
    app(PenawaranService::class)->kirim($offer);

    $this->actingAs($client)
        ->postJson(route('api.offers.respond', $offer), ['aksi' => 'setuju'])
        ->assertNotFound();

    $this->assertDatabaseMissing('proyek', ['id_penawaran' => $offer->id_penawaran]);
});

it('approves an offer atomically and creates exactly one project', function () {
    $client = User::factory()->create(['role' => 'klien']);
    $consultation = Consultation::factory()->create([
        'id_klien' => $client->id,
        'tanggal_rencana' => '2026-12-10',
        'durasi_hari' => 3,
    ]);
    $offer = buatPenawaranUntukKonsultasi($consultation);
    app(PenawaranService::class)->kirim($offer);

    $this->actingAs($client)
        ->postJson(route('api.offers.respond', $offer), ['aksi' => 'setuju'])
        ->assertOk()
        ->assertJsonPath('status', 'disetujui')
        ->assertJsonPath('url', route('account.projects.index'));

    $this->assertDatabaseHas('penawaran', [
        'id_penawaran' => $offer->id_penawaran,
        'status' => PenawaranStatus::Disetujui->value,
    ]);
    $this->assertDatabaseHas('konsultasi', [
        'id_konsultasi' => $consultation->id_konsultasi,
        'status' => KonsultasiStatus::Deal->value,
    ]);
    $this->assertDatabaseCount('proyek', 1);
    $this->assertDatabaseHas('proyek', [
        'id_penawaran' => $offer->id_penawaran,
        'id_klien' => $client->id,
        'tgl_mulai' => '2026-12-10 00:00:00',
        'tgl_selesai' => '2026-12-12 00:00:00',
        'total_nilai' => 1500000,
    ]);

    $this->actingAs($client)
        ->postJson(route('api.offers.respond', $offer), ['aksi' => 'setuju'])
        ->assertStatus(409);

    $this->assertDatabaseCount('proyek', 1);
});

it('requires revision notes and records revision and rejection responses', function () {
    $client = User::factory()->create(['role' => 'klien']);
    $consultation = Consultation::factory()->create(['id_klien' => $client->id]);

    $revisionOffer = buatPenawaranUntukKonsultasi($consultation);
    app(PenawaranService::class)->kirim($revisionOffer);

    $this->actingAs($client)
        ->postJson(route('api.offers.respond', $revisionOffer), [
            'aksi' => 'revisi',
            'catatan' => '',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('catatan');

    $this->actingAs($client)
        ->postJson(route('api.offers.respond', $revisionOffer), [
            'aksi' => 'revisi',
            'catatan' => 'Tambahkan satu malam menginap.',
        ])
        ->assertOk()
        ->assertJsonPath('status', 'revisi');

    $this->assertDatabaseHas('penawaran', [
        'id_penawaran' => $revisionOffer->id_penawaran,
        'status' => 'revisi',
        'catatan_klien' => 'Tambahkan satu malam menginap.',
    ]);

    $rejectionOffer = buatPenawaranUntukKonsultasi($consultation);
    app(PenawaranService::class)->kirim($rejectionOffer);

    $this->actingAs($client)
        ->postJson(route('api.offers.respond', $rejectionOffer), [
            'aksi' => 'tolak',
            'catatan' => 'Di luar anggaran.',
        ])
        ->assertOk()
        ->assertJsonPath('status', 'ditolak');

    $this->assertDatabaseHas('penawaran', [
        'id_penawaran' => $rejectionOffer->id_penawaran,
        'status' => 'ditolak',
    ]);
});

test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
