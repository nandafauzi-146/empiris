<?php

use App\Jobs\SendConsultationNotification;
use App\Mail\ConsultationReceived;
use App\Models\Consultation;
use App\Models\EmailLog;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

uses(LazilyRefreshDatabase::class);

it('stores a guest consultation and queues both notification emails', function () {
    Queue::fake([SendConsultationNotification::class]);

    $response = $this->from(route('consultation.create'))
        ->post(route('consultation.store'), [
            'nama' => 'Sari Puspita',
            'email' => 'sari@example.test',
            'telepon' => '081234567890',
            'instansi' => 'PT Contoh',
            'layanan' => 'Tour & Wisata',
            'tujuan' => 'Yogyakarta',
            'jumlah_peserta' => 24,
            'durasi_hari' => 3,
            'tanggal_rencana' => '2026-12-10',
            'minat' => 'Wisata alam dan kuliner',
            'cerita_kebutuhan' => 'Perjalanan untuk kegiatan tim.',
        ]);

    $response
        ->assertRedirect(route('consultation.create'))
        ->assertSessionHas('consultation_number');

    $this->get(route('consultation.create'))
        ->assertOk()
        ->assertSeeText('daftar menggunakan email yang sama')
        ->assertSee(route('register'));

    $this->assertDatabaseHas('konsultasi', [
        'nama' => 'Sari Puspita',
        'email' => 'sari@example.test',
        'telepon' => '081234567890',
        'layanan' => 'Tour & Wisata',
        'tujuan' => 'Yogyakarta',
        'jumlah_peserta' => 24,
        'durasi_hari' => 3,
        'status' => 'baru',
    ]);

    $this->assertDatabaseCount('email_log', 2);
    $this->assertDatabaseHas('email_log', [
        'jenis' => 'konfirmasi_klien',
        'ke_email' => 'sari@example.test',
        'status' => 'terjadwal',
    ]);
    $this->assertDatabaseHas('email_log', [
        'jenis' => 'notifikasi_admin',
        'ke_email' => config('services.empirise.admin_email'),
        'status' => 'terjadwal',
    ]);

    Queue::assertPushed(SendConsultationNotification::class, 2);
});

it('limits new consultation submissions per IP and returns retry headers', function () {
    Queue::fake([SendConsultationNotification::class]);
    $payload = [
        'nama' => 'Sari Puspita',
        'email' => 'sari@example.test',
        'telepon' => '081234567890',
        'layanan' => 'Tour & Wisata',
    ];

    foreach (range(1, 5) as $attempt) {
        $this->post(route('consultation.store'), $payload)
            ->assertRedirect(route('consultation.create'));
    }

    $this->post(route('consultation.store'), $payload)
        ->assertTooManyRequests()
        ->assertHeader('Retry-After');

    $this->assertDatabaseCount('konsultasi', 5);
    Queue::assertPushed(SendConsultationNotification::class, 10);
});

it('opens the consultation form with rental selected from the armada page', function () {
    $this->get(route('consultation.create', ['layanan' => 'Rental Armada']))
        ->assertOk()
        ->assertSee('value="Rental Armada" selected', false)
        ->assertSee('data-rental-details', false)
        ->assertSeeText('Permintaan ini belum mengunci kendaraan atau tanggal.');
});

it('stores structured rental details and queues the consultation notifications', function () {
    Queue::fake([SendConsultationNotification::class]);

    $response = $this->post(route('consultation.store'), [
        'nama' => 'Sari Puspita',
        'email' => 'sari@example.test',
        'telepon' => '081234567890',
        'layanan' => 'Rental Armada',
        'tujuan' => 'Bandung - Lembang - Bandung',
        'jumlah_peserta' => 5,
        'tanggal_rencana' => '2026-12-10',
        'jenis_kendaraan' => 'Mobil',
        'lokasi_jemput' => 'Stasiun Bandung',
        'jam_jemput' => '09:30',
        'tanggal_kembali' => '2026-12-11',
    ]);

    $response
        ->assertRedirect(route('consultation.create'))
        ->assertSessionHas('consultation_number');

    $this->assertDatabaseHas('konsultasi', [
        'nama' => 'Sari Puspita',
        'layanan' => 'Rental Armada',
        'tujuan' => 'Bandung - Lembang - Bandung',
        'jumlah_peserta' => 5,
        'jenis_kendaraan' => 'Mobil',
        'lokasi_jemput' => 'Stasiun Bandung',
        'status' => 'baru',
    ]);
    $consultation = Consultation::query()->sole();

    expect($consultation->jam_jemput)->toBe('09:30')
        ->and($consultation->tanggal_kembali->toDateString())->toBe('2026-12-11');
    $this->assertDatabaseCount('email_log', 2);
    Queue::assertPushed(SendConsultationNotification::class, 2);
});

it('requires a vehicle preference and pickup location for rental consultations', function () {
    Queue::fake([SendConsultationNotification::class]);

    $this->from(route('consultation.create'))
        ->post(route('consultation.store'), [
            'nama' => 'Sari Puspita',
            'email' => 'sari@example.test',
            'telepon' => '081234567890',
            'layanan' => 'Rental Armada',
        ])
        ->assertSessionHasErrors([
            'jenis_kendaraan' => 'Pilih jenis kendaraan atau pilih belum menentukan.',
            'lokasi_jemput' => 'Lokasi penjemputan wajib diisi untuk rental armada.',
        ]);

    $this->assertDatabaseCount('konsultasi', 0);
    $this->assertDatabaseCount('email_log', 0);
    Queue::assertNothingPushed();
});

it('rejects a return date before the rental departure date', function () {
    Queue::fake([SendConsultationNotification::class]);

    $this->from(route('consultation.create'))
        ->post(route('consultation.store'), [
            'nama' => 'Sari Puspita',
            'email' => 'sari@example.test',
            'telepon' => '081234567890',
            'layanan' => 'Rental Armada',
            'tanggal_rencana' => '2026-12-10',
            'jenis_kendaraan' => 'Minibus',
            'lokasi_jemput' => 'Stasiun Bandung',
            'tanggal_kembali' => '2026-12-09',
        ])
        ->assertSessionHasErrors([
            'tanggal_kembali' => 'Tanggal kembali harus pada atau setelah tanggal rencana.',
        ]);

    $this->assertDatabaseCount('konsultasi', 0);
    Queue::assertNothingPushed();
});

it('does not persist rental-only fields for a different consultation service', function () {
    Queue::fake([SendConsultationNotification::class]);

    $this->post(route('consultation.store'), [
        'nama' => 'Sari Puspita',
        'email' => 'sari@example.test',
        'telepon' => '081234567890',
        'layanan' => 'Tour & Wisata',
        'jenis_kendaraan' => 'Bus',
        'lokasi_jemput' => 'Stasiun Bandung',
    ])->assertRedirect(route('consultation.create'));

    $consultation = Consultation::query()->sole();

    expect($consultation->jenis_kendaraan)->toBeNull()
        ->and($consultation->lokasi_jemput)->toBeNull();
});

it('links a verified client consultation to their account and shows its conversation link', function () {
    Queue::fake([SendConsultationNotification::class]);
    $client = User::factory()->create(['role' => 'klien']);

    $this->actingAs($client)
        ->post(route('consultation.store'), [
            'nama' => $client->name,
            'email' => $client->email,
            'telepon' => '081234567890',
            'layanan' => 'Tour & Wisata',
        ])
        ->assertRedirect(route('consultation.create'));

    $consultation = Consultation::query()->where('email', $client->email)->firstOrFail();

    $this->assertDatabaseHas('konsultasi', [
        'id_konsultasi' => $consultation->id_konsultasi,
        'id_klien' => $client->id,
    ]);

    $this->get(route('consultation.create'))
        ->assertOk()
        ->assertSee(route('account.consultations.show', $consultation));
});

it('rejects missing guest details with Indonesian validation messages and no side effects', function () {
    Queue::fake([SendConsultationNotification::class]);

    $response = $this->from(route('consultation.create'))
        ->post(route('consultation.store'), []);

    $response->assertSessionHasErrors([
        'nama' => 'Nama wajib diisi.',
        'email' => 'Email wajib diisi.',
        'telepon' => 'Nomor telepon wajib diisi.',
        'layanan' => 'Pilih kebutuhan kegiatan Anda.',
    ]);

    $this->assertDatabaseCount('konsultasi', 0);
    $this->assertDatabaseCount('email_log', 0);
    Queue::assertNothingPushed();
});

it('rejects consultation stories longer than 1500 characters', function () {
    Queue::fake([SendConsultationNotification::class]);

    $response = $this->from(route('consultation.create'))
        ->post(route('consultation.store'), [
            'nama' => 'Sari Puspita',
            'email' => 'sari@example.test',
            'telepon' => '081234567890',
            'layanan' => 'Tour & Wisata',
            'cerita_kebutuhan' => str_repeat('a', 1501),
        ]);

    $response->assertSessionHasErrors([
        'cerita_kebutuhan' => 'Cerita kebutuhan tidak boleh lebih dari 1500 karakter.',
    ]);

    $this->assertDatabaseCount('konsultasi', 0);
    Queue::assertNothingPushed();
});

it('accepts a consultation story containing exactly 1500 characters', function () {
    Queue::fake([SendConsultationNotification::class]);
    $story = str_repeat('r', 1500);

    $response = $this->post(route('consultation.store'), [
        'nama' => 'Sari Puspita',
        'email' => 'sari@example.test',
        'telepon' => '081234567890',
        'layanan' => 'Tour & Wisata',
        'cerita_kebutuhan' => $story,
    ]);

    $response->assertRedirect(route('consultation.create'));
    $this->assertDatabaseHas('konsultasi', ['cerita_kebutuhan' => $story]);
    Queue::assertPushed(SendConsultationNotification::class, 2);
});

it('rejects an invalid email address without saving the consultation', function () {
    Queue::fake([SendConsultationNotification::class]);

    $response = $this->from(route('consultation.create'))
        ->post(route('consultation.store'), [
            'nama' => 'Sari Puspita',
            'email' => 'bukan-alamat-email',
            'telepon' => '081234567890',
            'layanan' => 'Tour & Wisata',
        ]);

    $response->assertSessionHasErrors([
        'email' => 'Masukkan alamat email yang valid.',
    ]);
    $this->assertDatabaseCount('konsultasi', 0);
    Queue::assertNothingPushed();
});

it('sends and records a queued client confirmation', function () {
    Mail::fake();

    $consultation = Consultation::factory()->create();
    $emailLog = EmailLog::factory()
        ->for($consultation, 'consultation')
        ->create([
            'jenis' => 'konfirmasi_klien',
            'ke_email' => $consultation->email,
        ]);

    (new SendConsultationNotification($emailLog->id_email))->handle();

    Mail::assertSent(ConsultationReceived::class, fn (ConsultationReceived $mail): bool => (
        $mail->consultation->is($consultation) && ! $mail->forAdmin
    ));

    $this->assertDatabaseHas('email_log', [
        'id_email' => $emailLog->id_email,
        'status' => 'terkirim',
    ]);
});

it('includes the rental availability disclaimer and trip details in consultation emails', function () {
    $consultation = Consultation::factory()->create([
        'layanan' => 'Rental Armada',
        'jenis_kendaraan' => 'Mobil',
        'lokasi_jemput' => 'Stasiun Bandung',
        'jam_jemput' => '09:30:00',
        'tanggal_kembali' => '2026-12-11',
    ]);

    (new ConsultationReceived($consultation, true))
        ->assertSeeInHtml('Stasiun Bandung')
        ->assertSeeInHtml('09:30')
        ->assertSeeInHtml('11/12/2026')
        ->assertSeeInHtml('belum menjadi reservasi');

    (new ConsultationReceived($consultation))
        ->assertSeeInHtml('Stasiun Bandung')
        ->assertSeeInHtml('belum menjadi reservasi terkonfirmasi');
});

it('marks a consultation email failed after the final queue attempt', function () {
    $emailLog = EmailLog::factory()->create();

    (new SendConsultationNotification($emailLog->id_email))->failed(
        new RuntimeException('SMTP unavailable'),
    );

    $this->assertDatabaseHas('email_log', [
        'id_email' => $emailLog->id_email,
        'status' => 'gagal',
        'error' => 'SMTP unavailable',
    ]);
});
