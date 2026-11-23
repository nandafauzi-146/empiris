<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(LazilyRefreshDatabase::class);

it('sends a reset notification without revealing whether an email is registered', function () {
    Notification::fake();
    $user = User::factory()->create([
        'email' => 'sari@example.test',
    ]);

    $knownEmailResponse = $this->post(route('password.email'), [
        'email' => 'sari@example.test',
    ]);
    $unknownEmailResponse = $this->post(route('password.email'), [
        'email' => 'unknown@example.test',
    ]);

    $knownEmailResponse->assertSessionHas(
        'status',
        'Jika alamat email terdaftar, tautan reset kata sandi akan segera dikirim.',
    );
    $unknownEmailResponse->assertSessionHas(
        'status',
        'Jika alamat email terdaftar, tautan reset kata sandi akan segera dikirim.',
    );
    Notification::assertSentTo($user, ResetPassword::class);
});

it('resets a password with a valid token and rejects reuse of that token', function () {
    $user = User::factory()->create([
        'email' => 'sari@example.test',
    ]);
    $token = Password::createToken($user);
    $resetPayload = [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-secure-pass-456',
        'password_confirmation' => 'new-secure-pass-456',
    ];

    $response = $this->post(route('password.update'), $resetPayload);

    $response
        ->assertRedirect(route('login'))
        ->assertSessionHas('status', 'Kata sandi berhasil diubah. Silakan masuk.');
    expect(Hash::check('new-secure-pass-456', $user->fresh()->password))->toBeTrue();

    $reuseResponse = $this->from(route('password.reset', ['token' => $token]))
        ->post(route('password.update'), $resetPayload);

    $reuseResponse->assertSessionHasErrors([
        'email' => 'Tautan reset kata sandi tidak valid atau sudah kedaluwarsa.',
    ]);
    expect(Hash::check('new-secure-pass-456', $user->fresh()->password))->toBeTrue();
});

it('rejects a password reset with a short password', function () {
    $response = $this->from(route('password.reset', ['token' => 'reset-token']))
        ->post(route('password.update'), [
            'token' => 'reset-token',
            'email' => 'sari@example.test',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

    $response->assertSessionHasErrors([
        'password' => 'Kata sandi minimal 8 karakter.',
    ]);
});
