<?php

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Gate;

uses(LazilyRefreshDatabase::class);

it('redirects guests to login before showing client consultations', function () {
    $this->get(route('account.consultations.index'))
        ->assertRedirect(route('login'));
});

it('redirects unverified clients to the verification notice', function () {
    $client = User::factory()->unverified()->create([
        'role' => 'klien',
    ]);

    $this->actingAs($client)
        ->get(route('account.consultations.index'))
        ->assertRedirect(route('verification.notice'));
});

it('lists only consultations owned by the authenticated client', function () {
    $client = User::factory()->create([
        'role' => 'klien',
    ]);
    Consultation::factory()->create([
        'id_klien' => $client->id,
        'tujuan' => 'Yogyakarta',
    ]);
    Consultation::factory()->create([
        'tujuan' => 'Lombok',
    ]);

    $this->actingAs($client)
        ->get(route('account.consultations.index'))
        ->assertOk()
        ->assertSeeText('Konsultasi saya')
        ->assertSeeText('Konsultasi baru')
        ->assertSeeText($client->name)
        ->assertSeeText($client->email)
        ->assertSee('aria-label="Navigasi akun klien"', false)
        ->assertSee('aria-current="page"', false)
        ->assertSee('href="'.route('account.projects.index').'"', false)
        ->assertSee('href="'.route('account.profile.edit').'"', false)
        ->assertDontSee('aria-label="Navigasi utama"', false)
        ->assertSeeText('Yogyakarta')
        ->assertDontSeeText('Lombok');
});

it('returns not found when a client opens another clients consultation', function () {
    $client = User::factory()->create([
        'role' => 'klien',
    ]);
    $anotherClientsConsultation = Consultation::factory()->create();

    $this->actingAs($client)
        ->get(route('account.consultations.show', $anotherClientsConsultation))
        ->assertNotFound();
});

it('renders consultation notes as escaped text for their owner', function () {
    $client = User::factory()->create([
        'role' => 'klien',
    ]);
    $consultation = Consultation::factory()->create([
        'id_klien' => $client->id,
        'cerita_kebutuhan' => '<script>alert("xss")</script>',
    ]);

    $this->actingAs($client)
        ->get(route('account.consultations.show', $consultation))
        ->assertOk()
        ->assertSee('aria-label="Navigasi akun klien"', false)
        ->assertSeeText('Ringkasan kebutuhan')
        ->assertSee('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', false)
        ->assertDontSee('<script>alert("xss")</script>', false);
});

it('shows structured rental details and the availability status to the client', function () {
    $client = User::factory()->create([
        'role' => 'klien',
    ]);
    $consultation = Consultation::factory()->create([
        'id_klien' => $client->id,
        'layanan' => 'Rental Armada',
        'tujuan' => 'Bandung - Lembang',
        'jenis_kendaraan' => 'Minibus',
        'lokasi_jemput' => 'Stasiun Bandung',
        'jam_jemput' => '08:15:00',
        'tanggal_rencana' => '2026-12-10',
        'tanggal_kembali' => '2026-12-11',
    ]);

    $this->actingAs($client)
        ->get(route('account.consultations.show', $consultation))
        ->assertOk()
        ->assertSeeText('Minibus')
        ->assertSeeText('Stasiun Bandung')
        ->assertSeeText('08:15')
        ->assertSeeText('11/12/2026')
        ->assertSeeText('Menunggu konfirmasi ketersediaan dari tim Empirise');
});

it('allows clients to cancel consultations that have not reached deal', function (string $status) {
    $client = User::factory()->create([
        'role' => 'klien',
    ]);
    $consultation = Consultation::factory()->create([
        'id_klien' => $client->id,
        'status' => $status,
    ]);

    $this->actingAs($client)
        ->post(route('account.consultations.cancel', $consultation))
        ->assertRedirect(route('account.consultations.show', $consultation))
        ->assertSessionHas('status', 'Konsultasi berhasil dibatalkan.');

    $this->assertDatabaseHas('konsultasi', [
        'id_konsultasi' => $consultation->id_konsultasi,
        'status' => 'batal',
    ]);
})->with([
    'baru',
    'dibalas',
    'penawaran',
]);

it('does not allow clients to cancel consultations that are already closed', function (string $status) {
    $client = User::factory()->create([
        'role' => 'klien',
    ]);
    $consultation = Consultation::factory()->create([
        'id_klien' => $client->id,
        'status' => $status,
    ]);

    $this->actingAs($client)
        ->post(route('account.consultations.cancel', $consultation))
        ->assertForbidden();

    $this->assertDatabaseHas('konsultasi', [
        'id_konsultasi' => $consultation->id_konsultasi,
        'status' => $status,
    ]);
})->with([
    'deal',
    'batal',
]);

it('returns not found when a client cancels another clients consultation', function () {
    $client = User::factory()->create([
        'role' => 'klien',
    ]);
    $anotherClientsConsultation = Consultation::factory()->create();

    $this->actingAs($client)
        ->post(route('account.consultations.cancel', $anotherClientsConsultation))
        ->assertNotFound();

    $this->assertDatabaseHas('konsultasi', [
        'id_konsultasi' => $anotherClientsConsultation->id_konsultasi,
        'status' => 'baru',
    ]);
});

it('shows the cancel action only while the consultation can be cancelled', function (string $status, bool $isCancelable) {
    $client = User::factory()->create([
        'role' => 'klien',
    ]);
    $consultation = Consultation::factory()->create([
        'id_klien' => $client->id,
        'status' => $status,
    ]);

    $response = $this->actingAs($client)
        ->get(route('account.consultations.show', $consultation))
        ->assertOk()
        ->assertSeeText($status === 'batal' ? 'Dibatalkan' : ucfirst($status));

    if ($isCancelable) {
        $response->assertSeeText('Batalkan konsultasi');
    } else {
        $response->assertDontSeeText('Batalkan konsultasi');
    }
})->with([
    'baru' => ['baru', true],
    'dibalas' => ['dibalas', true],
    'penawaran' => ['penawaran', true],
    'deal' => ['deal', false],
    'batal' => ['batal', false],
]);

it('forbids admins from opening the client account area', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this->actingAs($admin)
        ->get(route('account.consultations.index'))
        ->assertForbidden();
});

it('forbids admins from cancelling a client consultation', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $consultation = Consultation::factory()->create();

    $this->actingAs($admin)
        ->post(route('account.consultations.cancel', $consultation))
        ->assertForbidden();

    $this->assertDatabaseHas('konsultasi', [
        'id_konsultasi' => $consultation->id_konsultasi,
        'status' => 'baru',
    ]);
});

it('authorizes cancellation only for the owner with a consultation that is not closed', function (
    string $role,
    bool $isOwner,
    string $status,
    bool $isAllowed,
) {
    $user = User::factory()->create([
        'role' => $role,
    ]);
    $consultation = Consultation::factory()->create([
        'id_klien' => $isOwner ? $user->id : User::factory()->create(['role' => 'klien'])->id,
        'status' => $status,
    ]);

    expect(Gate::forUser($user)->allows('batal', $consultation))->toBe($isAllowed);
})->with([
    'client owns a new consultation' => ['klien', true, 'baru', true],
    'client owns a replied consultation' => ['klien', true, 'dibalas', true],
    'client owns an offer consultation' => ['klien', true, 'penawaran', true],
    'client owns a deal consultation' => ['klien', true, 'deal', false],
    'client owns a cancelled consultation' => ['klien', true, 'batal', false],
    'client does not own the consultation' => ['klien', false, 'baru', false],
    'admin owns the consultation' => ['admin', true, 'baru', false],
]);
