<?php

use App\Models\Consultation;
use App\Models\ConsultationReply;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('redirects guests before they can reply to a consultation', function () {
    $consultation = Consultation::factory()->create();

    $this->post(route('account.consultations.replies.store', $consultation), [
        'pesan' => 'Mohon informasi lebih lanjut.',
    ])->assertRedirect(route('login'));
});

it('stores a client reply in their consultation conversation', function () {
    $client = User::factory()->create(['role' => 'klien']);
    $consultation = Consultation::factory()->create([
        'id_klien' => $client->id,
    ]);

    $this->actingAs($client)
        ->post(route('account.consultations.replies.store', $consultation), [
            'pesan' => 'Kami memiliki beberapa pertanyaan.',
        ])
        ->assertRedirect(route('account.consultations.show', $consultation))
        ->assertSessionHas('status', 'Pesan Anda sudah dikirim.');

    $this->assertDatabaseHas('balasan_konsultasi', [
        'id_konsultasi' => $consultation->id_konsultasi,
        'id_pengirim' => $client->id,
        'pesan' => 'Kami memiliki beberapa pertanyaan.',
        'terkirim_email' => false,
    ]);
});

it('returns not found when a client replies to another clients consultation', function () {
    $client = User::factory()->create(['role' => 'klien']);
    $consultation = Consultation::factory()->create();

    $this->actingAs($client)
        ->post(route('account.consultations.replies.store', $consultation), [
            'pesan' => 'Saya mencoba mengirim pesan.',
        ])
        ->assertNotFound();

    $this->assertDatabaseCount('balasan_konsultasi', 0);
});

it('rejects client replies longer than 2000 characters', function () {
    $client = User::factory()->create(['role' => 'klien']);
    $consultation = Consultation::factory()->create([
        'id_klien' => $client->id,
    ]);

    $this->actingAs($client)
        ->from(route('account.consultations.show', $consultation))
        ->post(route('account.consultations.replies.store', $consultation), [
            'pesan' => str_repeat('a', 2001),
        ])
        ->assertSessionHasErrors([
            'pesan' => 'Pesan balasan tidak boleh lebih dari 2000 karakter.',
        ]);

    $this->assertDatabaseCount('balasan_konsultasi', 0);
});

it('renders consultation replies as escaped text for their owner', function () {
    $client = User::factory()->create(['role' => 'klien']);
    $consultation = Consultation::factory()->create([
        'id_klien' => $client->id,
    ]);
    ConsultationReply::query()->create([
        'id_konsultasi' => $consultation->id_konsultasi,
        'id_pengirim' => $client->id,
        'pesan' => '<script>alert("xss")</script>',
    ]);

    $this->actingAs($client)
        ->get(route('account.consultations.show', $consultation))
        ->assertOk()
        ->assertSee('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', false)
        ->assertDontSee('<script>alert("xss")</script>', false);
});
