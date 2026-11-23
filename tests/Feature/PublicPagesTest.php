<?php

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('renders each public page', function (string $url, string $heading) {
    $this->get($url)
        ->assertOk()
        ->assertSeeText($heading);
})->with([
    'home' => ['/', 'Malang'],
    'about' => ['/tentang', 'PT Empirise Karya Hutama'],
    'services' => ['/layanan', 'Satu mitra untuk seluruh kebutuhan kegiatan Anda.'],
    'service details' => ['/layanan/tour-wisata', 'Tour & Wisata'],
    'destinations' => ['/destinasi', 'Temukan tempat untuk cerita perjalanan berikutnya.'],
    'destination details' => ['/destinasi/solo', 'Solo'],
    'package details' => ['/paket/jelajah-solo', 'Jelajah Solo'],
    'vehicles' => ['/armada', 'Perjalanan kelompok, lebih nyaman bersama.'],
    'portfolios' => ['/portofolio', 'Kegiatan yang telah kami wujudkan.'],
    'portfolio details' => ['/portofolio/rapat-kerja-nasional-instansi', 'Rapat Kerja Nasional Instansi'],
    'clients' => ['/klien', 'Dipercaya berbagai instansi dan perusahaan.'],
    'consultation' => ['/konsultasi', 'Mari rencanakan kegiatan berikutnya.'],
    'contact' => ['/faq-kontak', 'Ada yang ingin Anda tanyakan?'],
]);

it('renders an auto-rotating destination hero with synchronized background images', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('data-hero-image', false)
        ->assertSee('data-hero-image-next', false)
        ->assertSee('data-destination-autoplay', false)
        ->assertSee('aria-label="Jeda slide otomatis"', false)
        ->assertSee('data-featured', false)
        ->assertSeeText('Jawa Timur')
        ->assertSeeText('Malang')
        ->assertSeeText('05 / 12');
});

it('renders the animated navigation header on public content pages', function (string $url) {
    $this->get($url)
        ->assertOk()
        ->assertSee('data-scroll-header', false)
        ->assertSee('class="nav-link', false);
})->with([
    'services' => '/layanan',
    'destinations' => '/destinasi',
    'consultation' => '/konsultasi',
    'contact' => '/faq-kontak',
]);

it('renders the home portfolio as a featured carousel', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('data-portfolio-carousel', false)
        ->assertSee('data-portfolio-slide', false)
        ->assertSee('data-portfolio-prev', false)
        ->assertSee('data-portfolio-next', false)
        ->assertSee('data-portfolio-dot', false)
        ->assertSeeText('Klik kegiatan untuk melihat dokumentasi lengkapnya.');
});

it('keeps the assistant conversation closed until the visitor opens it', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('class="assistant-panel hidden"', false)
        ->assertSee('aria-label="Buka asisten"', false)
        ->assertSee('aria-expanded="false"', false);
});

it('returns not found for a destination outside the published list', function () {
    $this->get('/destinasi/not-a-destination')
        ->assertNotFound();
});

it('links the home consultation call to the consultation page', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('href="'.route('consultation.create').'"', false);
});

it('provides a server-submitted consultation form', function () {
    $this->get('/konsultasi')
        ->assertOk()
        ->assertSee('data-consultation-form', false)
        ->assertSee('action="'.route('consultation.store').'"', false)
        ->assertSee('name="email"', false)
        ->assertSee('name="telepon"', false)
        ->assertSeeText('Informasi Anda disimpan untuk menindaklanjuti konsultasi.');
});

it('uses the PRD consultation URL and keeps the existing URL available', function () {
    expect(route('consultation.create'))->toEndWith('/konsultasi/baru');

    $this->get('/konsultasi/baru')
        ->assertOk()
        ->assertSeeText('Mari rencanakan kegiatan berikutnya.');

    $this->get('/konsultasi')
        ->assertOk()
        ->assertSeeText('Mari rencanakan kegiatan berikutnya.');
});

it('provides local filters for destination and portfolio listings', function () {
    $this->get('/destinasi')
        ->assertOk()
        ->assertSee('data-destination-search', false)
        ->assertSee('data-destination-region', false)
        ->assertSee('data-destination-category', false);

    $this->get('/portofolio')
        ->assertOk()
        ->assertSee('data-portfolio-category', false)
        ->assertSee('data-portfolio-year', false)
        ->assertSeeText('Bandung · 2025');
});
