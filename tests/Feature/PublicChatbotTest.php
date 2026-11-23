<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

uses(LazilyRefreshDatabase::class);

it('rejects an empty message or invalid input', function () {
    $this->postJson(route('chatbot.chat'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['message']);

    $this->postJson(route('chatbot.chat'), [
        'message' => str_repeat('a', 501),
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['message']);
});

it('responds to user questions via OpenAI without saving conversation to database', function () {
    Http::fake([
        'https://api.openai.com/v1/chat/completions' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => 'Halo! Empirise menyediakan layanan Tour, Gathering, Outbound, Rental Armada, dan Team Building. Layanan apa yang Anda butuhkan?',
                    ],
                ],
            ],
        ], 200),
    ]);

    $response = $this->postJson(route('chatbot.chat'), [
        'message' => 'Layanan apa saja yang tersedia di Empirise?',
    ]);

    $response->assertOk()
        ->assertJson([
            'answered' => true,
        ])
        ->assertJsonStructure(['reply', 'answered']);

    expect($response->json('reply'))->toContain('Empirise menyediakan layanan Tour');

    // Pastikan tidak ada tabel log percakapan yang menyimpan data ke database (hemat storage)
    expect(Schema::hasTable('chatbot_log'))->toBeFalse();
    expect(Schema::hasTable('faq_chatbot'))->toBeFalse();
});

it('answers authenticated client questions without storing conversation history', function () {
    Http::fake([
        'https://api.openai.com/v1/chat/completions' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => 'Tentu, silakan mulai konsultasi untuk merencanakan kegiatan Anda.',
                    ],
                ],
            ],
        ], 200),
    ]);

    $client = User::factory()->create(['role' => 'klien']);

    $this->actingAs($client)
        ->postJson(route('chatbot.chat'), [
            'message' => 'Bagaimana cara mulai konsultasi?',
        ])
        ->assertOk()
        ->assertJson([
            'answered' => true,
        ]);
});

it('applies guardrail to replace any price quote with standard disclaimer', function () {
    Http::fake([
        'https://api.openai.com/v1/chat/completions' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => 'Biaya paket wisata kami mulai dari Rp 1.500.000 per orang.',
                    ],
                ],
            ],
        ], 200),
    ]);

    $response = $this->postJson(route('chatbot.chat'), [
        'message' => 'Berapa harga paket Solo?',
    ]);

    $response->assertOk();

    // R2: Chatbot publik tidak boleh memuat harga
    expect($response->json('reply'))->not->toContain('Rp 1.500.000')
        ->and($response->json('reply'))->toContain('Harga disusun oleh tim Empirise dalam penawaran');
});

it('returns intelligent in-memory knowledge base answer when OpenAI fails or quota is exhausted', function () {
    Http::fake([
        'https://api.openai.com/v1/chat/completions' => Http::response(['error' => 'Rate limit'], 500),
    ]);

    $response = $this->postJson(route('chatbot.chat'), [
        'message' => 'Kendaraan apa saja yang tersedia untuk rental armada bus?',
    ]);

    $response->assertOk()
        ->assertJson([
            'answered' => true,
        ]);

    expect($response->json('reply'))->toContain('Big Bus')
        ->and($response->json('reply'))->toContain('Medium Bus');
});
