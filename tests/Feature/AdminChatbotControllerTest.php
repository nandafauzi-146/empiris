<?php

use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\BusinessSetting;
use App\Models\ChatbotFaq;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('shows default recommendation settings without writing them during a page view', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->get(route('admin.chatbot.index', ['tab' => 'rekomendasi']))
        ->assertOk()
        ->assertViewHas('settings', [
            'ambang_skor' => '0.15',
            'jumlah_hasil' => '5',
        ]);

    $this->assertDatabaseCount('pengaturan_bisnis', 0);
});

it('stores FAQ keywords and audit data', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->post(route('admin.chatbot.faq.store'), [
            'kata_kunci' => 'transportasi, bus',
            'pertanyaan' => 'Apakah tersedia bus?',
            'jawaban' => 'Tim akan membantu menyesuaikan kebutuhan armada.',
            'aktif' => '1',
        ])
        ->assertRedirect(route('admin.chatbot.index', ['tab' => 'faq']))
        ->assertSessionHas('status');

    $faq = ChatbotFaq::query()->firstOrFail();
    expect($faq->kata_kunci)->toBe(['transportasi', 'bus'])
        ->and($faq->aktif)->toBeTrue();
    $this->assertDatabaseHas('audit_log', [
        'id_user' => $admin->id,
        'aksi' => 'faq_chatbot_dibuat',
        'objek' => 'faq_chatbot',
        'id_objek' => $faq->id_faq,
    ]);
});

it('records the actual previous state when deactivating an already inactive FAQ', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $faq = ChatbotFaq::query()->create([
        'kata_kunci' => ['jadwal'],
        'pertanyaan' => 'Kapan jadwal tersedia?',
        'jawaban' => 'Silakan konsultasikan tanggal rencana.',
        'aktif' => false,
    ]);

    $this->actingAs($admin)
        ->delete(route('admin.chatbot.faq.destroy', $faq))
        ->assertRedirect(route('admin.chatbot.index', ['tab' => 'faq']));

    expect($faq->fresh()->aktif)->toBeFalse();
    $auditLog = AuditLog::query()->where('aksi', 'faq_chatbot_dinonaktifkan')->firstOrFail();
    expect($auditLog->sebelum)->toBe(['aktif' => false])
        ->and($auditLog->sesudah)->toBe(['aktif' => false]);
});

it('saves valid recommendation settings and audits defaults accurately', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->patch(route('admin.chatbot.settings'), [
            'ambang_skor' => '0.25',
            'jumlah_hasil' => 8,
        ])
        ->assertRedirect(route('admin.chatbot.index', ['tab' => 'rekomendasi']))
        ->assertSessionHas('status');

    expect(BusinessSetting::query()->where('kunci', 'ambang_skor_rekomendasi')->value('nilai'))->toBe('0.25')
        ->and(BusinessSetting::query()->where('kunci', 'jumlah_hasil_rekomendasi')->value('nilai'))->toBe('8');
    $auditLog = AuditLog::query()->where('aksi', 'pengaturan_rekomendasi_diubah')->firstOrFail();
    expect($auditLog->sebelum)->toBe([
        'ambang_skor_rekomendasi' => null,
        'jumlah_hasil_rekomendasi' => null,
    ]);
});

it('rejects invalid recommendation settings without writing settings or audit data', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->from(route('admin.chatbot.index', ['tab' => 'rekomendasi']))
        ->patch(route('admin.chatbot.settings'), [
            'ambang_skor' => '1.1',
            'jumlah_hasil' => 8,
        ])
        ->assertSessionHasErrors('ambang_skor');

    $this->assertDatabaseCount('pengaturan_bisnis', 0);
    $this->assertDatabaseCount('audit_log', 0);
});

it('forbids clients from managing chatbot settings', function () {
    $client = User::factory()->create(['role' => UserRole::Klien]);

    $this->actingAs($client)
        ->get(route('admin.chatbot.index'))
        ->assertForbidden();
});
