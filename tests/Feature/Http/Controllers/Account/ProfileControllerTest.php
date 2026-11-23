<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(LazilyRefreshDatabase::class);

it('redirects guests to login before showing the profile form', function () {
    $this->get(route('account.profile.edit'))
        ->assertRedirect(route('login'));
});

it('renders an editable form for the authenticated client', function () {
    $client = User::factory()->create([
        'name' => 'Client Empirise',
        'phone' => '08123456789',
        'role' => UserRole::Klien,
    ]);

    $this->actingAs($client)
        ->get(route('account.profile.edit'))
        ->assertOk()
        ->assertSeeText('Profil saya')
        ->assertSee('name="name"', false)
        ->assertSee('name="email"', false)
        ->assertSee('name="phone"', false)
        ->assertSee('href="'.route('account.projects.index').'"', false)
        ->assertSee('href="'.route('account.profile.edit').'"', false);
});

it('updates the authenticated clients profile without changing protected account data', function () {
    $client = User::factory()->create([
        'name' => 'Nama lama',
        'phone' => '0811111111',
        'role' => UserRole::Klien,
    ]);
    $password = $client->password;

    $this->actingAs($client)
        ->patch(route('account.profile.update'), [
            'name' => 'Nama baru',
            'email' => $client->email,
            'phone' => '0822222222',
            'role' => 'admin',
            'password' => 'password-lain',
        ])
        ->assertRedirect(route('account.profile.edit'))
        ->assertSessionHas('status', 'Profil berhasil diperbarui.');

    $client->refresh();

    expect($client->name)->toBe('Nama baru')
        ->and($client->phone)->toBe('0822222222')
        ->and($client->role)->toBe(UserRole::Klien)
        ->and($client->password)->toBe($password);
});

it('requires email reverification when the client changes their email', function () {
    Notification::fake();

    $client = User::factory()->create([
        'role' => UserRole::Klien,
    ]);
    $newEmail = 'new-client@example.com';

    $this->actingAs($client)
        ->patch(route('account.profile.update'), [
            'name' => $client->name,
            'email' => $newEmail,
            'phone' => '08123456789',
        ])
        ->assertRedirect(route('verification.notice'));

    $client->refresh();

    expect($client->email)->toBe($newEmail)
        ->and($client->email_verified_at)->toBeNull();

    Notification::assertSentTo($client, VerifyEmail::class);
});

it('rejects a profile email already used by another account', function () {
    $client = User::factory()->create([
        'role' => UserRole::Klien,
    ]);
    $anotherClient = User::factory()->create([
        'role' => UserRole::Klien,
    ]);

    $this->actingAs($client)
        ->from(route('account.profile.edit'))
        ->patch(route('account.profile.update'), [
            'name' => $client->name,
            'email' => $anotherClient->email,
            'phone' => '08123456789',
        ])
        ->assertSessionHasErrors([
            'email' => 'Email ini sudah digunakan akun lain.',
        ]);

    expect($client->fresh()->email)->toBe($client->email);
});

it('requires a valid name, email, and phone number', function () {
    $client = User::factory()->create([
        'role' => UserRole::Klien,
    ]);

    $this->actingAs($client)
        ->from(route('account.profile.edit'))
        ->patch(route('account.profile.update'), [
            'name' => '',
            'email' => 'not-an-email',
            'phone' => '',
        ])
        ->assertSessionHasErrors([
            'name' => 'Nama wajib diisi.',
            'email' => 'Masukkan alamat email yang valid.',
            'phone' => 'Nomor telepon wajib diisi.',
        ]);

    expect($client->fresh()->name)->toBe($client->name)
        ->and($client->fresh()->email)->toBe($client->email)
        ->and($client->fresh()->phone)->toBe($client->phone);
});

it('forbids admins from using the client profile', function () {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);

    $this->actingAs($admin)
        ->get(route('account.profile.edit'))
        ->assertForbidden();
});
