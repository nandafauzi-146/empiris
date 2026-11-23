<?php

use App\Jobs\SendConsultationReplyNotification;
use App\Mail\ConsultationReplyReceived;
use App\Models\Consultation;
use App\Models\ConsultationReply;
use App\Models\EmailLog;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

uses(LazilyRefreshDatabase::class);

it('redirects guests from the admin consultation inbox to login', function () {
    $this->get(route('admin.consultations.index'))
        ->assertRedirect(route('login'));
});

it('forbids clients from viewing the admin consultation inbox', function () {
    $client = User::factory()->create(['role' => 'klien']);

    $this->actingAs($client)
        ->get(route('admin.consultations.index'))
        ->assertForbidden();
});

it('lists consultations for an admin with their reply counts', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $consultation = Consultation::factory()->create();
    $sender = User::factory()->create(['role' => 'klien']);
    ConsultationReply::query()->create([
        'id_konsultasi' => $consultation->id_konsultasi,
        'id_pengirim' => $sender->id,
        'pesan' => 'Apakah tersedia tanggal tersebut?',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.consultations.index'))
        ->assertOk()
        ->assertSeeText($consultation->nomor_konsultasi)
        ->assertSeeText('1');
});

it('shows consultation details and conversation to an admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'klien']);
    $consultation = Consultation::factory()->create([
        'nama' => 'Sari Puspita',
        'cerita_kebutuhan' => 'Kegiatan untuk 24 peserta.',
    ]);
    ConsultationReply::query()->create([
        'id_konsultasi' => $consultation->id_konsultasi,
        'id_pengirim' => $client->id,
        'pesan' => 'Mohon rekomendasi untuk bulan Desember.',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.consultations.show', $consultation))
        ->assertOk()
        ->assertSeeText('Sari Puspita')
        ->assertSeeText('Kegiatan untuk 24 peserta.')
        ->assertSeeText('Mohon rekomendasi untuk bulan Desember.');
});

it('shows structured rental details in the admin inbox', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Consultation::factory()->create([
        'layanan' => 'Rental Armada',
        'tujuan' => 'Bandung - Lembang',
        'jenis_kendaraan' => 'Bus',
        'lokasi_jemput' => 'Hotel Bandung',
        'jam_jemput' => '07:45:00',
        'tanggal_rencana' => '2026-12-10',
        'tanggal_kembali' => '2026-12-12',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.consultations.index'))
        ->assertOk()
        ->assertSeeText('Hotel Bandung')
        ->assertSeeText('Bandung - Lembang');
});

it('shows structured rental details and the unconfirmed status in admin consultation detail', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $consultation = Consultation::factory()->create([
        'layanan' => 'Rental Armada',
        'tujuan' => 'Bandung - Lembang',
        'jenis_kendaraan' => 'Bus',
        'lokasi_jemput' => 'Hotel Bandung',
        'jam_jemput' => '07:45:00',
        'tanggal_rencana' => '2026-12-10',
        'tanggal_kembali' => '2026-12-12',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.consultations.show', $consultation))
        ->assertOk()
        ->assertSeeText('Bus')
        ->assertSeeText('Hotel Bandung')
        ->assertSeeText('07:45')
        ->assertSeeText('12/12/2026')
        ->assertSeeText('Perlu dikonfirmasi admin; permintaan ini bukan reservasi terkonfirmasi.');
});

it('stores an admin reply and queues an email notification to the client', function () {
    Queue::fake([SendConsultationReplyNotification::class]);
    $admin = User::factory()->create(['role' => 'admin']);
    $consultation = Consultation::factory()->create([
        'email' => 'klien@example.test',
        'status' => 'baru',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.consultations.replies.store', $consultation), [
            'pesan' => 'Tim kami akan menyiapkan rekomendasi perjalanan.',
        ])
        ->assertRedirect(route('admin.consultations.show', $consultation))
        ->assertSessionHas('status');

    $reply = ConsultationReply::query()->firstOrFail();
    $emailLog = EmailLog::query()->firstOrFail();

    $this->assertDatabaseHas('balasan_konsultasi', [
        'id_balasan' => $reply->id_balasan,
        'id_konsultasi' => $consultation->id_konsultasi,
        'id_pengirim' => $admin->id,
        'pesan' => 'Tim kami akan menyiapkan rekomendasi perjalanan.',
        'terkirim_email' => false,
    ]);
    $this->assertDatabaseHas('konsultasi', [
        'id_konsultasi' => $consultation->id_konsultasi,
        'status' => 'dibalas',
    ]);
    $this->assertDatabaseHas('email_log', [
        'id_email' => $emailLog->id_email,
        'jenis' => 'balasan_konsultasi',
        'ke_email' => 'klien@example.test',
        'status' => 'terjadwal',
    ]);
    Queue::assertPushed(
        SendConsultationReplyNotification::class,
        fn (SendConsultationReplyNotification $job): bool => (
            $job->emailLogId === $emailLog->id_email && $job->replyId === $reply->id_balasan
        ),
    );
});

it('rejects empty admin replies without creating a message or email log', function () {
    Queue::fake([SendConsultationReplyNotification::class]);
    $admin = User::factory()->create(['role' => 'admin']);
    $consultation = Consultation::factory()->create();

    $this->actingAs($admin)
        ->from(route('admin.consultations.show', $consultation))
        ->post(route('admin.consultations.replies.store', $consultation), ['pesan' => ''])
        ->assertSessionHasErrors([
            'pesan' => 'Pesan balasan wajib diisi.',
        ]);

    $this->assertDatabaseCount('balasan_konsultasi', 0);
    $this->assertDatabaseCount('email_log', 0);
    Queue::assertNothingPushed();
});

it('sends and records a queued reply notification', function () {
    Mail::fake();
    $admin = User::factory()->create(['role' => 'admin']);
    $consultation = Consultation::factory()->create();
    $reply = ConsultationReply::query()->create([
        'id_konsultasi' => $consultation->id_konsultasi,
        'id_pengirim' => $admin->id,
        'pesan' => 'Balasan aman untuk klien.',
    ]);
    $emailLog = $consultation->emailLogs()->create([
        'jenis' => 'balasan_konsultasi',
        'ke_email' => $consultation->email,
        'subjek' => 'Balasan baru untuk konsultasi Anda',
    ]);

    (new SendConsultationReplyNotification($emailLog->id_email, $reply->id_balasan))->handle();

    Mail::assertSent(ConsultationReplyReceived::class);
    $this->assertDatabaseHas('balasan_konsultasi', [
        'id_balasan' => $reply->id_balasan,
        'terkirim_email' => true,
    ]);
    $this->assertDatabaseHas('email_log', [
        'id_email' => $emailLog->id_email,
        'status' => 'terkirim',
    ]);
});

it('includes the consultation excerpt, link, and signature in both email formats', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $consultation = Consultation::factory()->create();
    $reply = ConsultationReply::query()->create([
        'id_konsultasi' => $consultation->id_konsultasi,
        'id_pengirim' => $admin->id,
        'pesan' => 'Kami akan mengirimkan rekomendasi besok.',
    ]);
    $consultationUrl = route('account.consultations.show', $consultation);
    $mail = new ConsultationReplyReceived($reply);

    $mail
        ->assertSeeInHtml('Ada balasan untuk Anda')
        ->assertSeeInHtml('Kami akan mengirimkan rekomendasi besok.')
        ->assertSeeInHtml($consultationUrl)
        ->assertSeeInHtml('Tim Empirise Jalan-Jalan')
        ->assertSeeInText('Ada balasan untuk Anda')
        ->assertSeeInText('Kami akan mengirimkan rekomendasi besok.')
        ->assertSeeInText($consultationUrl)
        ->assertSeeInText('Tim Empirise Jalan-Jalan');
});

it('escapes client message content in the reply notification email', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $consultation = Consultation::factory()->create();
    $reply = ConsultationReply::query()->create([
        'id_konsultasi' => $consultation->id_konsultasi,
        'id_pengirim' => $admin->id,
        'pesan' => '<script>alert("xss")</script>',
    ]);

    $rendered = (new ConsultationReplyReceived($reply))->render();

    expect($rendered)
        ->toContain('&lt;script&gt;')
        ->not->toContain('<script>alert("xss")</script>');
});

it('limits the email notification to a 400 character reply excerpt and links to the conversation', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $consultation = Consultation::factory()->create();
    $reply = ConsultationReply::query()->create([
        'id_konsultasi' => $consultation->id_konsultasi,
        'id_pengirim' => $admin->id,
        'pesan' => str_repeat('a', 401),
    ]);

    $mail = new ConsultationReplyReceived($reply);
    $rendered = $mail->render();

    expect($rendered)
        ->toContain(str_repeat('a', 400).'...')
        ->not->toContain(str_repeat('a', 401))
        ->toContain(route('account.consultations.show', $consultation))
        ->toContain('Tim Empirise Jalan-Jalan');

    $mail
        ->assertSeeInText(str_repeat('a', 400).'...')
        ->assertDontSeeInText(str_repeat('a', 401));
});

it('marks a reply notification failed after the final queue attempt', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $consultation = Consultation::factory()->create();
    $reply = ConsultationReply::query()->create([
        'id_konsultasi' => $consultation->id_konsultasi,
        'id_pengirim' => $admin->id,
        'pesan' => 'Balasan untuk klien.',
    ]);
    $emailLog = $consultation->emailLogs()->create([
        'jenis' => 'balasan_konsultasi',
        'ke_email' => $consultation->email,
        'subjek' => 'Balasan baru untuk konsultasi Anda',
    ]);

    (new SendConsultationReplyNotification($emailLog->id_email, $reply->id_balasan))->failed(
        new RuntimeException('SMTP unavailable'),
    );

    $this->assertDatabaseHas('email_log', [
        'id_email' => $emailLog->id_email,
        'status' => 'gagal',
        'error' => 'SMTP unavailable',
    ]);
});
