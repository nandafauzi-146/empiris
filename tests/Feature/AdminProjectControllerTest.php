<?php

use App\Enums\UserRole;
use App\Models\Consultation;
use App\Models\PaymentTerm;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

it('creates a project with exact thirty forty thirty payment terms', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $client = User::factory()->create(['role' => UserRole::Klien]);
    $consultation = Consultation::factory()->create(['id_klien' => $client->id]);

    $this->actingAs($admin)
        ->post(route('admin.projects.store'), [
            'id_konsultasi' => $consultation->id_konsultasi,
            'nama' => 'Team Building Empirise',
            'tgl_mulai' => '2026-12-10',
            'tgl_selesai' => '2026-12-12',
            'total_nilai' => 100001,
        ])
        ->assertRedirect(route('admin.projects.index'))
        ->assertSessionHas('status');

    $project = Project::query()->firstOrFail();
    $terms = $project->terms;

    expect($project->nomor_proyek)->toBe('PR-0001')
        ->and($project->id_klien)->toBe($client->id)
        ->and($terms->pluck('nominal')->all())->toBe([30000, 40000, 30001])
        ->and($terms->sum('nominal'))->toBe(100001)
        ->and($terms->every(fn (PaymentTerm $term): bool => $term->jatuh_tempo->toDateString() === '2026-12-10'))->toBeTrue();
    $this->assertDatabaseCount('termin_pembayaran', 3);
    $this->assertDatabaseHas('audit_log', [
        'aksi' => 'proyek_dibuat',
        'objek' => 'proyek',
        'id_objek' => $project->id_proyek,
    ]);
});

it('does not create another project for a consultation that already has one', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $client = User::factory()->create(['role' => UserRole::Klien]);
    $consultation = Consultation::factory()->create(['id_klien' => $client->id]);
    $project = Project::query()->create([
        'nomor_proyek' => 'PR-0042',
        'id_konsultasi' => $consultation->id_konsultasi,
        'id_klien' => $client->id,
        'nama' => 'Proyek Pertama',
        'tgl_mulai' => '2026-12-10',
        'tgl_selesai' => '2026-12-12',
        'status' => 'terjadwal',
        'total_nilai' => 1000000,
    ]);

    $this->actingAs($admin)
        ->from(route('admin.projects.index'))
        ->post(route('admin.projects.store'), [
            'id_konsultasi' => $consultation->id_konsultasi,
            'nama' => 'Proyek Kedua',
            'tgl_mulai' => '2026-12-10',
            'tgl_selesai' => '2026-12-12',
            'total_nilai' => 1000000,
        ])
        ->assertSessionHasErrors([
            'id_konsultasi' => 'Konsultasi ini sudah memiliki proyek.',
        ]);

    expect(Project::query()->count())->toBe(1)
        ->and(Project::query()->first()->is($project))->toBeTrue();
    $this->assertDatabaseCount('termin_pembayaran', 0);
});

it('records payment and evidence exactly once', function () {
    Storage::fake('local');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $client = User::factory()->create(['role' => UserRole::Klien]);
    $consultation = Consultation::factory()->create(['id_klien' => $client->id]);
    $project = Project::query()->create([
        'nomor_proyek' => 'PR-0042',
        'id_konsultasi' => $consultation->id_konsultasi,
        'id_klien' => $client->id,
        'nama' => 'Proyek Pembayaran',
        'tgl_mulai' => '2026-12-10',
        'tgl_selesai' => '2026-12-12',
        'status' => 'terjadwal',
        'total_nilai' => 1000000,
    ]);
    $term = $project->terms()->create([
        'urutan' => 1,
        'nama' => 'Termin 1',
        'nominal' => 300000,
        'jatuh_tempo' => '2026-12-01',
        'status' => 'belum_bayar',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.projects.payments.store', $term), [
            'tgl_bayar' => '2026-11-20',
            'bukti' => UploadedFile::fake()->create('receipt.pdf', 100, 'application/pdf'),
        ])
        ->assertRedirect(route('admin.projects.index'))
        ->assertSessionHas('status');

    $term->refresh();
    expect($term->status)->toBe('lunas')
        ->and($term->jumlah_dibayar)->toBe(300000)
        ->and($term->tgl_bayar->toDateString())->toBe('2026-11-20')
        ->and($term->bukti_path)->not->toBeNull();
    Storage::disk('local')->assertExists($term->bukti_path);

    $this->post(route('admin.projects.payments.store', $term), [
        'tgl_bayar' => '2026-11-21',
    ])->assertStatus(409);

    expect($term->fresh()->tgl_bayar->toDateString())->toBe('2026-11-20');
    $this->assertDatabaseCount('audit_log', 1);
});

it('rejects invalid payment evidence without recording payment', function () {
    Storage::fake('local');
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $client = User::factory()->create(['role' => UserRole::Klien]);
    $consultation = Consultation::factory()->create(['id_klien' => $client->id]);
    $project = Project::query()->create([
        'nomor_proyek' => 'PR-0042',
        'id_konsultasi' => $consultation->id_konsultasi,
        'id_klien' => $client->id,
        'nama' => 'Proyek Pembayaran',
        'tgl_mulai' => '2026-12-10',
        'tgl_selesai' => '2026-12-12',
        'status' => 'terjadwal',
        'total_nilai' => 1000000,
    ]);
    $term = $project->terms()->create([
        'urutan' => 1,
        'nama' => 'Termin 1',
        'nominal' => 300000,
        'jatuh_tempo' => '2026-12-01',
        'status' => 'belum_bayar',
    ]);

    $this->actingAs($admin)
        ->from(route('admin.projects.index'))
        ->post(route('admin.projects.payments.store', $term), [
            'tgl_bayar' => '2026-11-20',
            'bukti' => UploadedFile::fake()->create('receipt.exe', 100, 'application/octet-stream'),
        ])
        ->assertSessionHasErrors('bukti');

    expect($term->fresh()->status)->toBe('belum_bayar')
        ->and($term->fresh()->jumlah_dibayar)->toBe(0);
    $this->assertDatabaseCount('audit_log', 0);
    Storage::disk('local')->assertDirectoryEmpty('bukti-pembayaran');
});

it('forbids clients from creating admin projects', function () {
    $client = User::factory()->create(['role' => UserRole::Klien]);

    $this->actingAs($client)
        ->post(route('admin.projects.store'))
        ->assertForbidden();
});
