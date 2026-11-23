<?php

use App\Models\ChatbotFaq;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;

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

it('responds to user questions via OpenAI and logs the conversation', function () {
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

    ChatbotFaq::create([
        'kata_kunci' => ['layanan', 'paket'],
        'pertanyaan' => 'Layanan apa saja yang ada?',
        'jawaban' => 'Tour, Gathering, Outbound, Rental Armada, Team Building.',
        'aktif' => true,
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

    $this->assertDatabaseHas('chatbot_log', [
        'mode' => 'publik',
        'ringkasan_pertanyaan' => 'Layanan apa saja yang tersedia di Empirise?',
        'terjawab' => true,
        'status' => 'ok',
        'id_klien' => null,
    ]);
});

it('records authenticated client id in chatbot logs', function () {
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
        ->assertOk();

    $this->assertDatabaseHas('chatbot_log', [
        'mode' => 'publik',
        'id_klien' => $client->id,
        'terjawab' => true,
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

it('returns intelligent knowledge base answer when OpenAI fails or times out', function () {
    Http::fake([
        'https://api.openai.com/v1/chat/completions' => Http::response(['error' => 'Rate limit'], 500),
    ]);

    ChatbotFaq::create([
        'kata_kunci' => ['armada', 'bus'],
        'pertanyaan' => 'Kendaraan apa saja yang tersedia untuk rental armada?',
        'jawaban' => 'Kami menyediakan Big Bus, Medium Bus, dan Minibus dengan fasilitas lengkap.',
        'aktif' => true,
    ]);

    $response = $this->postJson(route('chatbot.chat'), [
        'message' => 'Apakah armada bus tersedia?',
    ]);

    $response->assertOk()
        ->assertJson([
            'answered' => true,
        ]);

    expect($response->json('reply'))->toContain('Big Bus, Medium Bus');

    $this->assertDatabaseHas('chatbot_log', [
        'mode' => 'publik',
        'ringkasan_pertanyaan' => 'Apakah armada bus tersedia?',
        'terjawab' => true,
        'status' => 'error',
    ]);
});
