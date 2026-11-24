<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('intercepts guest accessing /pesan and stores pending action before redirecting to login', function () {
    $response = $this->get(route('order.gate', ['layanan' => 'Tour & Wisata', 'durasi' => 3]))
        ->assertRedirect(route('login'))
        ->assertSessionHas('status', 'Silakan masuk atau daftar terlebih dahulu untuk melanjutkan pemesanan dan deal harga.')
        ->assertSessionHas('pending_action');

    $pending = session('pending_action');
    expect($pending['type'])->toBe('pesan')
        ->and($pending['redirect_url'])->toContain('layanan=Tour')
        ->and($pending['redirect_url'])->toContain('durasi=3');
});

it('resumes pending action after client logs in successfully', function () {
    $user = User::factory()->create([
        'role' => UserRole::Klien,
        'email_verified_at' => now(),
        'password' => bcrypt('password123'),
    ]);

    $response = $this->withSession([
        'pending_action' => [
            'type' => 'pesan',
            'redirect_url' => route('consultation.create', ['cerita' => 'Gathering kantor']),
        ],
    ])->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('consultation.create', ['cerita' => 'Gathering kantor']))
        ->assertSessionMissing('pending_action')
        ->assertSessionHas('status', 'Selamat datang! Tindakan pemesanan Anda dilanjutkan.');
});

it('redirects authenticated client accessing /pesan directly to consultation form', function () {
    $user = User::factory()->create([
        'role' => UserRole::Klien,
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('order.gate', ['layanan' => 'Outbound']))
        ->assertRedirect(route('consultation.create', ['layanan' => 'Outbound']));
});
