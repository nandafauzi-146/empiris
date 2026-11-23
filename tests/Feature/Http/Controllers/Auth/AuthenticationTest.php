<?php

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

uses(LazilyRefreshDatabase::class);

it('renders the login page in the split welcome-card layout', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('class="auth-card"', false)
        ->assertSee('data-auth-page="login"', false)
        ->assertSeeText('Pantau konsultasi dan proyekmu dalam satu tempat')
        ->assertSeeText('Masuk ke akun')
        ->assertSee('action="'.route('login.store').'"', false)
        ->assertSee('href="'.route('password.request').'"', false)
        ->assertSee('data-password-toggle="password"', false)
        ->assertSee('data-auth-transition="register"', false);
});

it('renders the registration page in the matching split card layout', function () {
    $this->get(route('register'))
        ->assertOk()
        ->assertSee('class="auth-card auth-card-register"', false)
        ->assertSee('data-auth-page="register"', false)
        ->assertSeeText('Buat akun klien')
        ->assertSee('action="'.route('register.store').'"', false)
        ->assertSee('name="name"', false)
        ->assertSee('name="email"', false)
        ->assertSee('name="phone"', false)
        ->assertSeeText('Tautan verifikasi akan dikirim ke email Anda.')
        ->assertSee('data-password-toggle="password_confirmation"', false)
        ->assertSee('data-auth-transition="login"', false);
});

it('registers a client and sends an email verification notification without claiming consultations', function () {
    Notification::fake();
    $consultation = Consultation::factory()->create([
        'email' => 'sari@example.test',
        'id_klien' => null,
    ]);

    $response = $this->post(route('register.store'), [
        'name' => 'Sari Puspita',
        'email' => 'SARI@EXAMPLE.TEST',
        'phone' => '081234567890',
        'password' => 'secure-pass-123',
        'password_confirmation' => 'secure-pass-123',
    ]);

    $user = User::query()->where('email', 'sari@example.test')->firstOrFail();

    $response->assertRedirect(route('verification.notice'));
    $this->assertAuthenticatedAs($user);
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'phone' => '081234567890',
        'role' => 'klien',
    ]);
    $this->assertDatabaseHas('konsultasi', [
        'id_konsultasi' => $consultation->id_konsultasi,
        'id_klien' => null,
    ]);
    expect(Hash::check('secure-pass-123', $user->password))->toBeTrue();
    Notification::assertSentTo($user, VerifyEmail::class);
});

it('links matching guest consultations only after the client verifies their email', function () {
    Notification::fake();
    $consultation = Consultation::factory()->create([
        'email' => 'sari@example.test',
        'id_klien' => null,
    ]);

    $this->post(route('register.store'), [
        'name' => 'Sari Puspita',
        'email' => 'sari@example.test',
        'phone' => '081234567890',
        'password' => 'secure-pass-123',
        'password_confirmation' => 'secure-pass-123',
    ]);

    $user = User::query()->where('email', 'sari@example.test')->firstOrFail();
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->getKey(),
            'hash' => sha1($user->getEmailForVerification()),
        ],
    );

    $this->get($verificationUrl)
        ->assertRedirect(route('account.consultations.index'))
        ->assertSessionHas('status', 'Email berhasil diverifikasi. Konsultasi Anda sudah ditautkan ke akun.');

    $this->assertDatabaseHas('konsultasi', [
        'id_konsultasi' => $consultation->id_konsultasi,
        'id_klien' => $user->id,
    ]);
});

it('does not let an unverified client claim matching guest consultations', function () {
    $user = User::factory()->unverified()->create([
        'email' => 'sari@example.test',
    ]);
    $consultation = Consultation::factory()->create([
        'email' => 'sari@example.test',
        'id_klien' => null,
    ]);

    $user->claimGuestConsultations();

    $this->assertDatabaseHas('konsultasi', [
        'id_konsultasi' => $consultation->id_konsultasi,
        'id_klien' => null,
    ]);
});

it('rejects a signed verification link with a mismatched email hash', function () {
    $user = User::factory()->unverified()->create([
        'email' => 'sari@example.test',
    ]);
    $consultation = Consultation::factory()->create([
        'email' => 'sari@example.test',
        'id_klien' => null,
    ]);
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->getKey(),
            'hash' => 'invalid-hash',
        ],
    );

    $this->actingAs($user)
        ->get($verificationUrl)
        ->assertForbidden();

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
    $this->assertDatabaseHas('konsultasi', [
        'id_konsultasi' => $consultation->id_konsultasi,
        'id_klien' => null,
    ]);
});

it('rejects short passwords during registration with an Indonesian message', function () {
    $response = $this->from(route('register'))
        ->post(route('register.store'), [
            'name' => 'Sari Puspita',
            'email' => 'sari@example.test',
            'phone' => '081234567890',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

    $response->assertSessionHasErrors([
        'password' => 'Kata sandi minimal 8 karakter.',
    ]);
    $this->assertDatabaseCount('users', 0);
    $this->assertGuest();
});

it('logs in with a case-insensitive email and claims matching guest consultations', function () {
    $user = User::factory()->create([
        'email' => 'sari@example.test',
    ]);
    $consultation = Consultation::factory()->create([
        'email' => 'SARI@EXAMPLE.TEST',
        'id_klien' => null,
    ]);

    $response = $this->post(route('login.store'), [
        'email' => 'SARI@EXAMPLE.TEST',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('account.consultations.index'));
    $this->assertAuthenticatedAs($user);
    $this->assertDatabaseHas('konsultasi', [
        'id_konsultasi' => $consultation->id_konsultasi,
        'id_klien' => $user->id,
    ]);
});

it('redirects admins to the admin dashboard after login', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this->post(route('login.store'), [
        'email' => $admin->email,
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard'));
});

it('requires email verification before a client can access their account', function () {
    $user = User::factory()->unverified()->create([
        'email' => 'sari@example.test',
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('verification.notice'));
    $this->assertAuthenticatedAs($user);
});

it('rejects incorrect credentials with a generic message', function () {
    User::factory()->create([
        'email' => 'sari@example.test',
    ]);

    $response = $this->from(route('login'))
        ->post(route('login.store'), [
            'email' => 'sari@example.test',
            'password' => 'incorrect-password',
        ]);

    $response->assertSessionHasErrors([
        'email' => 'Email atau kata sandi tidak cocok.',
    ]);
    $this->assertGuest();
});

it('limits repeated login attempts', function () {
    User::factory()->create([
        'email' => 'sari@example.test',
    ]);

    foreach (range(1, 5) as $attempt) {
        $this->post(route('login.store'), [
            'email' => 'sari@example.test',
            'password' => 'incorrect-password',
        ])->assertSessionHasErrors('email');
    }

    $this->post(route('login.store'), [
        'email' => 'sari@example.test',
        'password' => 'incorrect-password',
    ])->assertTooManyRequests();

    $this->assertGuest();
});

it('logs out and clears the authenticated session', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('home'));
    $this->assertGuest();
});
