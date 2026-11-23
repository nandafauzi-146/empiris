<?php

use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('allows only admins to manage users', function () {
    $client = User::factory()->create(['role' => UserRole::Klien]);

    $this->actingAs($client)
        ->get(route('admin.users.index'))
        ->assertForbidden();
});

it('creates an active admin account and records the action', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->post(route('admin.users.store'), [
            'name' => 'Admin Baru',
            'email' => 'admin.baru@example.test',
            'password' => 'strong-password-123',
            'password_confirmation' => 'strong-password-123',
        ])
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('status', 'Akun admin berhasil dibuat.');

    $createdUser = User::query()->where('email', 'admin.baru@example.test')->firstOrFail();

    expect($createdUser->role)->toBe(UserRole::Admin)
        ->and($createdUser->aktif)->toBeTrue();
    $this->assertDatabaseHas('audit_log', [
        'id_user' => $admin->id,
        'aksi' => 'admin_dibuat',
        'objek' => 'users',
        'id_objek' => $createdUser->id,
    ]);
});

it('rejects invalid admin credentials without creating a user or audit entry', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->from(route('admin.users.index'))
        ->post(route('admin.users.store'), [
            'name' => 'Admin Baru',
            'email' => 'admin.baru@example.test',
            'password' => 'short',
            'password_confirmation' => 'does-not-match',
        ])
        ->assertSessionHasErrors([
            'password' => 'Kata sandi admin minimal 12 karakter.',
        ]);

    $this->assertDatabaseMissing('users', ['email' => 'admin.baru@example.test']);
    $this->assertDatabaseCount('audit_log', 0);
});

it('changes another user role and stores the before and after values in the audit log', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $client = User::factory()->create(['role' => UserRole::Klien]);

    $this->actingAs($admin)
        ->patch(route('admin.users.role', $client), ['role' => UserRole::Admin->value])
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('status', 'Peran pengguna berhasil diperbarui.');

    expect($client->fresh()->role)->toBe(UserRole::Admin);
    $auditLog = AuditLog::query()->where('aksi', 'peran_pengguna_diubah')->firstOrFail();
    expect($auditLog->id_user)->toBe($admin->id)
        ->and($auditLog->id_objek)->toBe($client->id)
        ->and($auditLog->sebelum)->toBe(['role' => UserRole::Klien->value])
        ->and($auditLog->sesudah)->toBe(['role' => UserRole::Admin->value]);
});

it('prevents an admin from changing or deactivating their own account', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->patch(route('admin.users.role', $admin), ['role' => UserRole::Klien->value])
        ->assertForbidden();

    $this->patch(route('admin.users.active', $admin))
        ->assertForbidden();

    expect($admin->fresh()->role)->toBe(UserRole::Admin)
        ->and($admin->fresh()->aktif)->toBeTrue();
    $this->assertDatabaseCount('audit_log', 0);
});

it('forbids guests from managing users', function () {
    $this->get(route('admin.users.index'))
        ->assertRedirect(route('login'));
});
