<?php

use App\Enums\UserRole;
use App\Models\AdminContent;
use App\Models\AuditLog;
use App\Models\User;
use Database\Seeders\WebsiteContentSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

it('creates admin-managed public content with structured metadata', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->post(route('admin.content.store'), [
            'jenis' => 'layanan',
            'judul' => 'Tour & Wisata',
            'slug' => 'tour-wisata',
            'deskripsi' => 'Layanan perjalanan yang disesuaikan.',
            'metadata_json' => '{"icon":"map"}',
            'status' => 'tampil',
            'aktif' => '1',
            'urutan' => 1,
        ])
        ->assertRedirect(route('admin.content.index', ['jenis' => 'layanan']))
        ->assertSessionHas('status');

    $content = AdminContent::query()->firstOrFail();
    expect($content->metadata)->toBe(['icon' => 'map'])
        ->and($content->aktif)->toBeTrue();
    $this->assertDatabaseHas('audit_log', [
        'id_user' => $admin->id,
        'aksi' => 'konten_dibuat',
        'objek' => 'konten_admin',
        'id_objek' => $content->id_konten,
    ]);
});

it('rejects invalid metadata JSON without creating content', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->from(route('admin.content.index'))
        ->post(route('admin.content.store'), [
            'jenis' => 'layanan',
            'judul' => 'Layanan',
            'status' => 'tampil',
            'urutan' => 1,
            'metadata_json' => 'not-json',
        ])
        ->assertSessionHasErrors('metadata_json');

    $this->assertDatabaseCount('konten_admin', 0);
    $this->assertDatabaseCount('audit_log', 0);
});

it('preserves metadata when editing content and records the actual previous values when disabling it', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $content = AdminContent::query()->create([
        'jenis' => 'destinasi',
        'judul' => 'Yogyakarta',
        'slug' => 'yogyakarta',
        'deskripsi' => 'Deskripsi lama',
        'metadata' => ['wilayah' => 'DIY'],
        'status' => 'draf',
        'aktif' => false,
        'urutan' => 1,
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.content.update', $content), [
            'jenis' => 'destinasi',
            'judul' => 'Yogyakarta',
            'slug' => 'yogyakarta',
            'deskripsi' => 'Deskripsi baru',
            'metadata_json' => '{"wilayah":"DIY","kategori":"Budaya"}',
            'status' => 'tampil',
            'aktif' => '1',
            'urutan' => 2,
        ])
        ->assertRedirect(route('admin.content.index', ['jenis' => 'destinasi']));

    expect($content->fresh()->metadata)->toBe(['wilayah' => 'DIY', 'kategori' => 'Budaya']);

    $content->fresh()->update(['aktif' => false, 'status' => 'draf']);
    $this->delete(route('admin.content.destroy', $content))
        ->assertRedirect(route('admin.content.index', ['jenis' => 'destinasi']));

    $updatedContent = $content->fresh();
    expect($updatedContent->aktif)->toBeFalse()
        ->and($updatedContent->status)->toBe('tersembunyi');
    $auditLog = AuditLog::query()->where('aksi', 'konten_dinonaktifkan')->firstOrFail();
    expect($auditLog->sebelum)->toBe(['aktif' => false, 'status' => 'draf'])
        ->and($auditLog->sesudah)->toBe(['aktif' => false, 'status' => 'tersembunyi']);
});

it('forbids clients from managing public content', function () {
    $client = User::factory()->create(['role' => UserRole::Klien]);

    $this->actingAs($client)
        ->get(route('admin.content.index'))
        ->assertForbidden();
});

it('renders a public-style content card in the live preview dialog', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $content = AdminContent::query()->create([
        'jenis' => 'layanan',
        'judul' => 'Tour & Wisata',
        'slug' => 'tour-wisata',
        'deskripsi' => 'Perjalanan wisata.',
        'metadata' => ['icon' => 'map'],
        'status' => 'tampil',
        'aktif' => true,
        'urutan' => 1,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.content.index', ['jenis' => 'layanan']))
        ->assertOk()
        ->assertSee('data-content-preview-form', false)
        ->assertSee('data-content-preview', false)
        ->assertSee('data-content-preview-dialog', false)
        ->assertSee('data-content-preview-open', false)
        ->assertSee('data-content-type="layanan"', false)
        ->assertSee('data-preview-title', false)
        ->assertSee('data-preview-description', false)
        ->assertSee('data-preview-icon-input', false)
        ->assertSee('data-preview-public-icon', false)
        ->assertSee('class="service-card"', false)
        ->assertSee('data-preview-public-title', false)
        ->assertSee('data-preview-public-description', false)
        ->assertSeeText('Tour & Wisata')
        ->assertSee('data-content-edit-panel', false)
        ->assertSee(route('admin.content.update', $content), false);
});

it('renders a matching public preview card for every content category', function (string $type, string $publicCard) {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    AdminContent::query()->create([
        'jenis' => $type,
        'judul' => 'Konten untuk pratinjau',
        'slug' => 'konten-pratinjau',
        'deskripsi' => 'Deskripsi untuk pratinjau.',
        'metadata' => ['wilayah' => 'Jawa Barat', 'kategori' => 'Alam', 'bagian' => 'beranda'],
        'status' => 'tampil',
        'aktif' => true,
        'urutan' => 1,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.content.index', ['jenis' => $type]))
        ->assertOk()
        ->assertSee('data-content-preview-dialog', false)
        ->assertSee('data-content-type="'.$type.'"', false)
        ->assertSee($publicCard, false)
        ->assertSee('Konten untuk pratinjau');
})->with([
    'destinations' => ['destinasi', 'destination-detail-hero'],
    'services' => ['layanan', 'service-card'],
    'portfolios' => ['portofolio', 'portfolio-card'],
    'clients' => ['klien', 'client-logo'],
    'testimonials' => ['testimoni', 'testimonial-card'],
    'faqs' => ['faq', 'faq-list'],
    'teams' => ['tim', 'about-team-grid'],
]);

it('renders published admin-managed content on the public homepage', function () {
    AdminContent::query()->create([
        'jenis' => 'layanan',
        'judul' => 'Wisata Kantor Khusus',
        'slug' => 'tour-wisata',
        'deskripsi' => 'Program perjalanan untuk kebutuhan kantor.',
        'metadata' => ['icon' => 'bus'],
        'status' => 'tampil',
        'aktif' => true,
        'urutan' => 1,
    ]);
    AdminContent::query()->create([
        'jenis' => 'layanan',
        'judul' => 'Layanan yang belum tayang',
        'slug' => 'outbound',
        'deskripsi' => 'Draf internal.',
        'status' => 'draf',
        'aktif' => true,
        'urutan' => 2,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSeeText('Wisata Kantor Khusus')
        ->assertSeeText('Program perjalanan untuk kebutuhan kantor.')
        ->assertDontSeeText('Layanan yang belum tayang');

    $this->get(route('services.index'))
        ->assertOk()
        ->assertSee('M5 16V6a3 3 0 0 1 3-3h8a3 3 0 0 1 3 3v10M3 16h18M5 16v3M19 16v3M7 19h10M7 7h10v5H7z', false);
});

it('seeds the content currently displayed on the site and preserves admin edits when reseeded', function () {
    $seeder = app(WebsiteContentSeeder::class);
    $seeder->run();

    $malang = AdminContent::query()
        ->where('jenis', 'destinasi')
        ->where('slug', 'malang')
        ->firstOrFail();
    $malang->update(['deskripsi' => 'Konten destinasi yang sudah diperbarui admin.']);
    $service = AdminContent::query()
        ->where('jenis', 'layanan')
        ->where('slug', 'tour-wisata')
        ->firstOrFail();
    $service->update(['deskripsi' => 'Deskripsi layanan yang diperbarui admin.']);

    $seeder->run();

    expect(AdminContent::query()->where('jenis', 'destinasi')->count())->toBe(12)
        ->and(AdminContent::query()->where('jenis', 'layanan')->count())->toBe(8)
        ->and($malang->fresh()->deskripsi)->toBe('Konten destinasi yang sudah diperbarui admin.')
        ->and(AdminContent::query()->where('jenis', 'klien')->count())->toBe(32)
        ->and(AdminContent::query()->where('jenis', 'testimoni')->count())->toBe(6)
        ->and($malang->fresh()->metadata['contoh'])->toBeTrue();

    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->get(route('admin.content.index', ['jenis' => 'layanan']))
        ->assertOk()
        ->assertSeeText('Tour & Wisata')
        ->assertSeeText('CONTOH')
        ->assertSeeText('Deskripsi layanan yang diperbarui admin.');

    $this->get('/')
        ->assertOk()
        ->assertSeeText('Konten destinasi yang sudah diperbarui admin.')
        ->assertSeeText('Deskripsi layanan yang diperbarui admin.')
        ->assertSee('data-name="Yogyakarta"', false);

    $this->get(route('services.index'))
        ->assertOk()
        ->assertSeeText('Tour & Wisata');

    $this->get(route('services.show', 'tour-wisata'))
        ->assertOk()
        ->assertSeeText('Deskripsi layanan yang diperbarui admin.');

    $this->get(route('portfolios.index'))
        ->assertOk()
        ->assertSeeText('Rapat Kerja Nasional Instansi');

    $this->get(route('portfolios.show', 'rapat-kerja-nasional-instansi'))
        ->assertOk()
        ->assertSeeText('Bandung');

    $this->get(route('clients.index'))
        ->assertOk()
        ->assertSeeText('SMP Insan Rabbani');

    $this->get(route('contact'))
        ->assertOk()
        ->assertSeeText('Layanan apa saja yang tersedia?');

    $this->get(route('about'))
        ->assertOk()
        ->assertSeeText('Management Team');
});

it('does not show disabled managed destinations on public destination pages', function () {
    app(WebsiteContentSeeder::class)->run();
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $malang = AdminContent::query()
        ->where('jenis', 'destinasi')
        ->where('slug', 'malang')
        ->firstOrFail();

    $this->actingAs($admin)
        ->delete(route('admin.content.destroy', $malang))
        ->assertRedirect(route('admin.content.index', ['jenis' => 'destinasi']));

    $this->get(route('destinations.index'))
        ->assertOk()
        ->assertDontSeeText('Malang');

    $this->get(route('destinations.show', 'malang'))
        ->assertNotFound();
});

it('publishes a new managed destination to the destination index and detail page', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->post(route('admin.content.store'), [
            'jenis' => 'destinasi',
            'judul' => 'Padang',
            'slug' => 'padang',
            'deskripsi' => 'Pantai, kuliner, dan budaya Minangkabau untuk rombongan.',
            'metadata_json' => '{"wilayah":"Sumatera Barat","kategori":"Alam"}',
            'status' => 'tampil',
            'aktif' => '1',
            'urutan' => 12,
        ])
        ->assertRedirect(route('admin.content.index', ['jenis' => 'destinasi']));

    $this->get(route('destinations.index'))
        ->assertOk()
        ->assertSeeText('Padang');

    $this->get(route('destinations.show', 'padang'))
        ->assertOk()
        ->assertSeeText('Padang')
        ->assertSeeText('Pantai, kuliner, dan budaya Minangkabau');
});

it('stores a CMS image and service icon for live public rendering', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->post(route('admin.content.store'), [
            'jenis' => 'layanan',
            'judul' => 'Wisata Alam',
            'slug' => 'tour-wisata',
            'deskripsi' => 'Perjalanan alam yang disesuaikan.',
            'icon' => 'bus',
            'gambar' => UploadedFile::fake()->image('layanan.jpg'),
            'status' => 'tampil',
            'aktif' => '1',
            'urutan' => 1,
        ])
        ->assertRedirect(route('admin.content.index', ['jenis' => 'layanan']));

    $content = AdminContent::query()->firstOrFail();
    expect($content->metadata['icon'])->toBe('bus')
        ->and($content->metadata['image'])->toStartWith('storage/konten-web/');
    Storage::disk('public')->assertExists(substr($content->metadata['image'], strlen('storage/')));

    $this->get('/')
        ->assertOk()
        ->assertSeeText('Wisata Alam')
        ->assertSee('data-hero-image', false);
});

it('rejects more than five published services', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    foreach (range(1, 5) as $index) {
        AdminContent::query()->create([
            'jenis' => 'layanan',
            'judul' => 'Layanan '.$index,
            'slug' => 'layanan-'.$index,
            'deskripsi' => 'Deskripsi layanan.',
            'status' => 'tampil',
            'aktif' => true,
            'urutan' => $index,
        ]);
    }

    $this->actingAs($admin)
        ->from(route('admin.content.index', ['jenis' => 'layanan']))
        ->post(route('admin.content.store'), [
            'jenis' => 'layanan',
            'judul' => 'Layanan keenam',
            'slug' => 'layanan-keenam',
            'deskripsi' => 'Tidak boleh membuat layout meluber.',
            'status' => 'tampil',
            'aktif' => '1',
            'urutan' => 6,
        ])
        ->assertSessionHasErrors('status');

    $this->assertDatabaseCount('konten_admin', 5);
});
