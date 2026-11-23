<?php

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('redirects guests from the admin dashboard to login', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('login'));
});

it('forbids clients from viewing the admin dashboard', function () {
    $client = User::factory()->create(['role' => 'klien']);

    $this->actingAs($client)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

it('shows accurate consultation counts and the five most recently updated records', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Consultation::factory()->create(['status' => 'baru']);
    Consultation::factory()->create(['status' => 'dibalas']);
    Consultation::factory()->create(['status' => 'penawaran']);
    Consultation::factory()->create(['status' => 'deal']);
    Consultation::factory()->create(['status' => 'batal']);
    $latestConsultation = Consultation::factory()->create([
        'nama' => 'Konsultasi Terbaru',
        'status' => 'deal',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSeeText('Konsultasi Terbaru')
        ->assertSeeText('Konsultasi baru')
        ->assertSeeText('Penawaran menunggu')
        ->assertSeeText('Perlu ditanggapi')
        ->assertSeeText('Proyek berjalan')
        ->assertSeeText('Pembayaran jatuh tempo')
        ->assertViewHas('newConsultationCount', 1)
        ->assertViewHas('activeConsultationCount', 3)
        ->assertViewHas('totalConsultationCount', 6)
        ->assertViewHas(
            'recentConsultations',
            fn ($consultations): bool => $consultations->count() === 5
                && $consultations->first()->is($latestConsultation),
        );
});

it('escapes consultation names in the recent activity list', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Consultation::factory()->create([
        'nama' => '<script>alert("xss")</script>',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', false)
        ->assertDontSee('<script>alert("xss")</script>', false);
});
