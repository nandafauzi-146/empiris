<?php

use App\Enums\UserRole;
use App\Models\Consultation;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('redirects guests to login before showing projects and payments', function () {
    $this->get(route('account.projects.index'))
        ->assertRedirect(route('login'));
});

it('opens the projects and payments page with an honest empty state', function () {
    $client = User::factory()->create([
        'role' => UserRole::Klien,
    ]);

    $this->actingAs($client)
        ->get(route('account.projects.index'))
        ->assertOk()
        ->assertSeeText('Proyek & Pembayaran')
        ->assertSeeText('Belum ada proyek atau pembayaran.')
        ->assertSeeText('Belum ada data proyek dan pembayaran yang tercatat untuk akun Anda.')
        ->assertSee('aria-current="page"', false)
        ->assertSee('href="'.route('account.profile.edit').'"', false)
        ->assertSee('href="'.route('account.projects.index').'"', false);
});

it('shows only projects and payment terms owned by the authenticated client', function () {
    $client = User::factory()->create(['role' => UserRole::Klien]);
    $otherClient = User::factory()->create(['role' => UserRole::Klien]);
    $ownProject = createClientProject($client, 'Proyek Milik Saya');
    $otherProject = createClientProject($otherClient, 'Proyek Milik Klien Lain');
    $ownProject->terms()->create([
        'urutan' => 1,
        'nama' => 'Termin milik saya',
        'nominal' => 100000,
        'jatuh_tempo' => '2026-12-01',
        'status' => 'belum_bayar',
    ]);
    $otherProject->terms()->create([
        'urutan' => 1,
        'nama' => 'Termin rahasia klien lain',
        'nominal' => 900000,
        'jatuh_tempo' => '2026-12-01',
        'status' => 'belum_bayar',
    ]);

    $this->actingAs($client)
        ->get(route('account.projects.index'))
        ->assertOk()
        ->assertSeeText('Proyek Milik Saya')
        ->assertSeeText('Termin milik saya')
        ->assertDontSeeText('Proyek Milik Klien Lain')
        ->assertDontSeeText('Termin rahasia klien lain')
        ->assertDontSeeText('900.000');
});

it('forbids admins from opening the client projects and payments page', function () {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);

    $this->actingAs($admin)
        ->get(route('account.projects.index'))
        ->assertForbidden();
});

function createClientProject(User $client, string $name): Project
{
    $consultation = Consultation::factory()->create(['id_klien' => $client->id]);

    return Project::query()->create([
        'nomor_proyek' => 'PR-'.fake()->unique()->numerify('####'),
        'id_konsultasi' => $consultation->id_konsultasi,
        'id_klien' => $client->id,
        'nama' => $name,
        'tgl_mulai' => '2026-12-10',
        'tgl_selesai' => '2026-12-12',
        'status' => 'terjadwal',
        'total_nilai' => 1000000,
    ]);
}
