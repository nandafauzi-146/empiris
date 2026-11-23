<?php

use App\Enums\UserRole;
use App\Models\CapacityMarker;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('allows admins to toggle capacity markers and audits each change', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $payload = [
        'tanggal' => '2026-12-15',
        'jenis' => 'armada',
        'month' => '2026-12',
    ];

    $this->actingAs($admin)
        ->post(route('admin.calendar.markers.toggle'), $payload)
        ->assertRedirect(route('admin.calendar.index', ['month' => '2026-12']));

    $marker = CapacityMarker::query()->firstOrFail();
    expect($marker->tanggal->toDateString())->toBe('2026-12-15')
        ->and($marker->jenis)->toBe('armada')
        ->and($marker->dibuat_oleh)->toBe($admin->id);

    $this->post(route('admin.calendar.markers.toggle'), $payload)
        ->assertRedirect(route('admin.calendar.index', ['month' => '2026-12']));

    $this->assertDatabaseCount('penanda_kapasitas', 0);
    $this->assertDatabaseCount('audit_log', 2);
});

it('rejects invalid calendar capacity marker types', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->from(route('admin.calendar.index'))
        ->post(route('admin.calendar.markers.toggle'), [
            'tanggal' => '2026-12-15',
            'jenis' => 'klien',
            'month' => '2026-12',
        ])
        ->assertSessionHasErrors([
            'jenis' => 'Jenis penanda tidak valid.',
        ]);

    $this->assertDatabaseCount('penanda_kapasitas', 0);
    $this->assertDatabaseCount('audit_log', 0);
});

it('forbids clients from viewing the admin calendar', function () {
    $client = User::factory()->create(['role' => UserRole::Klien]);

    $this->actingAs($client)
        ->get(route('admin.calendar.index'))
        ->assertForbidden();
});
