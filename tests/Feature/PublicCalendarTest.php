<?php

use App\Models\CapacityMarker;
use App\Models\Consultation;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('shows public availability without exposing project or client names', function () {
    $client = User::factory()->create(['name' => 'Nama Rahasia Klien']);
    $consultation = Consultation::factory()->create(['id_klien' => $client->id]);
    $admin = User::factory()->create();
    Project::query()->create([
        'nomor_proyek' => 'PR-1001',
        'id_konsultasi' => $consultation->id_konsultasi,
        'id_klien' => $client->id,
        'nama' => 'Proyek Rahasia Klien',
        'tgl_mulai' => '2026-11-14',
        'tgl_selesai' => '2026-11-15',
        'status' => 'terjadwal',
        'total_nilai' => 1000000,
    ]);
    CapacityMarker::query()->create([
        'tanggal' => '2026-11-16',
        'jenis' => 'tim',
        'dibuat_oleh' => $admin->id,
    ]);

    $this->get(route('calendar.public', ['month' => '2026-11']))
        ->assertOk()
        ->assertSee('data-scroll-header', false)
        ->assertSee('aria-current="page"', false)
        ->assertSee('aria-label="14 November: Terbatas"', false)
        ->assertSee('aria-label="16 November: Penuh"', false)
        ->assertSee('aria-label="17 November: Tersedia"', false)
        ->assertDontSeeText('Nama Rahasia Klien')
        ->assertDontSeeText('Proyek Rahasia Klien');
});

it('rejects invalid public calendar month formats', function () {
    $this->get(route('calendar.public', ['month' => 'November-2026']))
        ->assertSessionHasErrors([
            'month' => 'Format bulan harus YYYY-MM.',
        ]);
});
