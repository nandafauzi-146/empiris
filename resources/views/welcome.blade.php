<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Jelajahi destinasi pilihan dan rancang perjalanan Indonesia bersama Empirise.">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Empirise — Jelajahi Liburan Alam Premium</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        @php
            $destinations = [
                [
                    'name' => 'Yogyakarta',
                    'region' => 'Jawa Tengah & DIY',
                    'category' => 'Budaya',
                    'image' => asset('images/figma/yogyakarta.png'),
                    'description' => 'Kota budaya dengan candi, keraton, kuliner, dan pusat kerajinan yang ramah untuk rombongan besar.',
                ],
                [
                    'name' => 'Solo',
                    'region' => 'Jawa Tengah & DIY',
                    'category' => 'Budaya',
                    'image' => asset('images/figma/solo.png'),
                    'description' => 'Kota batik dan keraton dengan kuliner khas, pasar tradisional, dan suasana yang tenang.',
                ],
                [
                    'name' => 'Semarang',
                    'region' => 'Jawa Tengah & DIY',
                    'category' => 'Budaya',
                    'image' => asset('images/figma/semarang.png'),
                    'description' => 'Jelajahi kota tua, kekayaan kuliner, dan bangunan bersejarah di Semarang.',
                ],
                [
                    'name' => 'Dieng',
                    'region' => 'Jawa Tengah & DIY',
                    'category' => 'Alam',
                    'image' => asset('images/figma/dieng.png'),
                    'description' => 'Nikmati udara pegunungan, telaga, dan lanskap alam dataran tinggi Dieng.',
                ],
                [
                    'name' => 'Malang',
                    'region' => 'Jawa Timur',
                    'category' => 'Alam',
                    'image' => asset('images/figma/malang.png'),
                    'description' => 'Temukan suasana sejuk, wisata alam, dan ragam kuliner khas Malang.',
                ],
                [
                    'name' => 'Bali',
                    'region' => 'Bali',
                    'category' => 'Alam',
                    'image' => asset('images/figma/bali.png'),
                    'description' => 'Rancang perjalanan untuk menikmati alam, budaya, dan pesisir Bali.',
                ],
                [
                    'name' => 'Lombok',
                    'region' => 'Nusa Tenggara',
                    'category' => 'Alam',
                    'image' => asset('images/figma/lombok.png'),
                    'description' => 'Jelajahi pantai, pulau-pulau kecil, dan pemandangan Lombok.',
                ],
                [
                    'name' => 'Labuan Bajo',
                    'region' => 'Nusa Tenggara',
                    'category' => 'Alam',
                    'image' => asset('images/figma/labuan-bajo.png'),
                    'description' => 'Mulai perjalanan laut dan jelajahi pesona kepulauan Labuan Bajo.',
                ],
                [
                    'name' => 'Bandung',
                    'region' => 'Jawa Barat',
                    'category' => 'Alam',
                    'image' => asset('images/figma/bandung.png'),
                    'description' => 'Nikmati udara sejuk, suasana kota, dan destinasi sekitar Bandung.',
                ],
                [
                    'name' => 'Jakarta',
                    'region' => 'DKI Jakarta',
                    'category' => 'Kota',
                    'image' => asset('images/figma/jakarta.png'),
                    'description' => 'Jelajahi ragam pengalaman kota dan destinasi bersejarah di Jakarta.',
                ],
                [
                    'name' => 'Banyuwangi',
                    'region' => 'Jawa Timur',
                    'category' => 'Alam',
                    'image' => asset('images/figma/banyuwangi.png'),
                    'description' => 'Temukan wisata alam dan budaya di ujung timur Pulau Jawa.',
                ],
                [
                    'name' => 'Karimunjawa',
                    'region' => 'Jawa Tengah & DIY',
                    'category' => 'Alam',
                    'image' => asset('images/figma/karimunjawa.png'),
                    'description' => 'Rencanakan perjalanan ke gugusan pulau dan perairan Karimunjawa.',
                ],
            ];

            $services = [
                ['title' => 'Tour & Wisata', 'description' => 'Perjalanan wisata domestik dan internasional dengan itinerary yang disesuaikan.', 'slug' => 'tour-wisata', 'icon' => 'map'],
                ['title' => 'Event & Gathering', 'description' => 'Perencanaan acara perusahaan, gathering, dan perayaan dari konsep hingga pelaksanaan.', 'slug' => 'event-gathering', 'icon' => 'calendar'],
                ['title' => 'Outbound', 'description' => 'Program outbound dan fun games dengan fasilitator berpengalaman.', 'slug' => 'outbound', 'icon' => 'gamepad'],
                ['title' => 'Rental Armada', 'description' => 'Bus dan minibus untuk perjalanan kelompok bersama pengemudi profesional.', 'slug' => 'rental-armada', 'icon' => 'bus'],
                ['title' => 'Team Building', 'description' => 'Kegiatan kolaboratif untuk mengembangkan kerja sama dan kebersamaan tim.', 'slug' => 'team-building', 'icon' => 'users'],
            ];

            $portfolioImages = [
                'rapat-kerja-nasional-instansi' => 'images/figma/bandung.png',
                'gathering-tahunan-400-karyawan' => 'images/figma/yogyakarta.png',
                'studi-lapangan-mahasiswa-arsitektur' => 'images/figma/bali.png',
                'outbound-pegawai-dinas' => 'images/figma/malang.png',
                'team-building-startup-teknologi' => 'images/figma/labuan-bajo.png',
                'wisuda-wisata-angkatan' => 'images/figma/malang.png',
            ];

            $portfolios = [
                ['Rapat Kerja Nasional Instansi', 'Bandung', 'Instansi', '2025', 'rapat-kerja-nasional-instansi', $portfolioImages['rapat-kerja-nasional-instansi']],
                ['Gathering Tahunan 400 Karyawan', 'Yogyakarta', 'Korporat', '2024', 'gathering-tahunan-400-karyawan', $portfolioImages['gathering-tahunan-400-karyawan']],
                ['Studi Lapangan Mahasiswa Arsitektur', 'Bali', 'Kampus', '2024', 'studi-lapangan-mahasiswa-arsitektur', $portfolioImages['studi-lapangan-mahasiswa-arsitektur']],
                ['Outbound Pegawai Dinas', 'Bogor', 'Instansi', '2023', 'outbound-pegawai-dinas', $portfolioImages['outbound-pegawai-dinas']],
                ['Team Building Startup Teknologi', 'Labuan Bajo', 'Korporat', '2025', 'team-building-startup-teknologi', $portfolioImages['team-building-startup-teknologi']],
                ['Wisuda & Wisata Angkatan', 'Malang', 'Kampus', '2025', 'wisuda-wisata-angkatan', $portfolioImages['wisuda-wisata-angkatan']],
            ];

            $clients = [
                'SMP Insan Rabbani', 'UTY', 'UAD', 'UNY', 'KPU', "Mu'allimin", 'BAZNAS', 'The Rain',
                'PN Wates', 'Ngatmombilung', 'Pendheza', 'Lembaga Yogya', 'Kemendikdasmen', 'KAI',
                'Pertamina', 'ADHI', 'IALF', 'Samsung', 'KONE', 'Rocket Chicken',
                'Muhammadiyah Wirobrajan', 'Poltek YKPN', 'Kemenag Lembaga',
            ];

            $testimonials = [
                ['Eksekusi rapi, tim sangat responsif sampai hari terakhir.', 'HR Manager, perusahaan logistik'],
                ['Itinerary edukatif dan sesuai anggaran kampus.', 'Dosen Pembimbing'],
                ['Peserta antusias dan kegiatan berjalan aman.', 'Kepala Bagian Umum'],
            ];

            $metadataString = static function ($content, array $keys, string $fallback = ''): string {
                foreach ($keys as $key) {
                    $value = $content->metadata[$key] ?? null;
                    if (is_scalar($value)) {
                        return (string) $value;
                    }
                }

                return $fallback;
            };

            $destinationEntries = in_array('destinasi', $managedContentTypes, true)
                ? collect()
                : collect($destinations)->mapWithKeys(static function (array $destination): array {
                    $slug = str()->slug($destination['name']);

                    return [$slug => [...$destination, 'slug' => $slug]];
                });

            foreach ($managedContent->get('destinasi', collect()) as $content) {
                $slug = $content->slug ?: str()->slug($content->judul);
                $destinationEntries->put($slug, [
                    'name' => $content->judul,
                    'region' => $metadataString($content, ['region', 'wilayah'], 'Indonesia'),
                    'category' => $metadataString($content, ['category', 'kategori'], 'Wisata'),
                    'slug' => $slug,
                    'description' => $content->deskripsi ?? '',
                    'image' => $metadataString($content, ['image', 'gambar'], 'images/figma/solo.png'),
                ]);
            }

            $destinations = $destinationEntries->values()->all();
            $initialDestinationIndex = collect($destinations)->search(
                static fn (array $destination): bool => $destination['name'] === 'Malang',
            );
            $initialDestinationIndex = is_int($initialDestinationIndex) ? $initialDestinationIndex : 0;
            $initialDestination = $destinations[$initialDestinationIndex] ?? null;

            if (in_array('layanan', $managedContentTypes, true)) {
                $services = $managedContent->get('layanan', collect())
                    ->take(5)
                    ->map(fn ($content): array => [
                        'title' => $content->judul,
                        'description' => $content->deskripsi ?? '',
                        'slug' => $content->slug ?? '',
                        'icon' => $metadataString($content, ['icon'], 'map'),
                    ])->all();
            }

            if (in_array('portofolio', $managedContentTypes, true)) {
                $portfolios = $managedContent->get('portofolio', collect())
                    ->take(6)
                    ->map(fn ($content): array => [
                        $content->judul,
                        $metadataString($content, ['location', 'lokasi']),
                        $metadataString($content, ['category', 'kategori']),
                        $metadataString($content, ['year', 'tahun']),
                        $content->slug ?? '',
                        $metadataString(
                            $content,
                            ['image', 'gambar'],
                            $portfolioImages[$content->slug ?? ''] ?? 'images/figma/solo.png',
                        ),
                    ])->all();
            }

            if (in_array('klien', $managedContentTypes, true)) {
                $clients = $managedContent->get('klien', collect())
                    ->filter(fn ($content): bool => ($content->metadata['bagian'] ?? 'beranda') === 'beranda')
                    ->take(24)
                    ->pluck('judul')
                    ->all();
            }

            if (in_array('testimoni', $managedContentTypes, true)) {
                $testimonials = $managedContent->get('testimoni', collect())
                    ->filter(fn ($content): bool => ($content->metadata['bagian'] ?? 'beranda') === 'beranda')
                    ->take(3)
                    ->map(fn ($content): array => [
                        $content->deskripsi ?? '',
                        $metadataString($content, ['author', 'penulis'], $content->judul),
                    ])->all();
            }
        @endphp

        <x-site-header />

        <main>
            <section class="hero" id="beranda" aria-labelledby="hero-title" data-hero>
                @if ($initialDestination)
                    <img class="hero-image" src="{{ str_starts_with($initialDestination['image'], 'http') ? $initialDestination['image'] : asset($initialDestination['image']) }}" alt="" fetchpriority="high" data-hero-image>
                    <img class="hero-image-next" src="{{ str_starts_with($initialDestination['image'], 'http') ? $initialDestination['image'] : asset($initialDestination['image']) }}" alt="" aria-hidden="true" data-hero-image-next>
                @endif
                <div class="hero-overlay" aria-hidden="true"></div>
                <div class="hero-bottom-shade" aria-hidden="true"></div>

                <div class="page-shell hero-layout">
                    <div class="hero-copy">
                        <p class="hero-eyebrow">
                            <img src="{{ asset('images/figma/icon-location.svg') }}" alt="" aria-hidden="true">
                            <span data-hero-region>{{ $initialDestination['region'] ?? '' }}</span>
                            <span class="hero-divider" aria-hidden="true">/</span>
                            <span data-hero-category>{{ $initialDestination['category'] ?? '' }}</span>
                        </p>

                        <h1 id="hero-title" data-hero-title>{{ $initialDestination['name'] ?? 'Destinasi pilihan' }}</h1>

                        <p class="hero-description" data-hero-description>{{ $initialDestination['description'] ?? 'Destinasi pilihan sedang diperbarui.' }}</p>

                        <div class="hero-actions">
                            <a class="button-primary" href="#destinasi">
                                Jelajahi
                                <img src="{{ asset('images/figma/icon-arrow-right.svg') }}" alt="" aria-hidden="true">
                            </a>
                            <a class="button-secondary" href="#destinasi">Lihat Semua</a>
                        </div>
                    </div>

                    <div class="hero-destinations" id="destinasi">
                        <div class="destination-track" data-destination-track aria-label="Destinasi pilihan">
                            @foreach ($destinations as $index => $destination)
                                <x-destination-card
                                    :name="$destination['name']"
                                    :region="$destination['region']"
                                    :category="$destination['category']"
                                    :description="$destination['description']"
                                    :image="str_starts_with($destination['image'], 'http') ? $destination['image'] : asset($destination['image'])"
                                    :featured="$destination['name'] === 'Malang'"
                                />
                            @endforeach
                            @if ($destinations === [])
                                <p class="text-white">Belum ada destinasi yang ditampilkan.</p>
                            @endif
                        </div>

                        @if ($destinations !== [])
                            <div class="carousel-controls" aria-label="Kontrol destinasi">
                            <button class="carousel-button" type="button" aria-label="Destinasi sebelumnya" data-destination-prev>
                                <img src="{{ asset('images/figma/icon-chevron-left.svg') }}" alt="" aria-hidden="true">
                            </button>
                            <button class="carousel-button" type="button" aria-label="Destinasi berikutnya" data-destination-next>
                                <img src="{{ asset('images/figma/icon-chevron-right.svg') }}" alt="" aria-hidden="true">
                            </button>
                                <div
                                class="carousel-progress"
                                role="progressbar"
                                aria-label="Posisi destinasi"
                                aria-valuemin="1"
                                aria-valuemax="{{ count($destinations) }}"
                                aria-valuenow="{{ $initialDestinationIndex + 1 }}"
                                data-destination-progress
                            >
                                    <span style="width: {{ (($initialDestinationIndex + 1) / count($destinations)) * 100 }}%"></span>
                                </div>
                                <span class="carousel-count" aria-live="off" data-destination-count>{{ str_pad((string) ($initialDestinationIndex + 1), 2, '0', STR_PAD_LEFT) }} / {{ str_pad((string) count($destinations), 2, '0', STR_PAD_LEFT) }}</span>
                                <button
                                class="carousel-button carousel-autoplay-toggle"
                                type="button"
                                aria-label="Jeda slide otomatis"
                                aria-pressed="false"
                                data-destination-autoplay
                                >
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path class="autoplay-pause-icon" d="M8 5h3v14H8zM15 5h3v14h-3z" />
                                        <path class="autoplay-play-icon" d="M7 4.8v14.4L19 12 7 4.8z" />
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            <x-story-planner />

            <x-trust-strip />

            <section class="content-section" id="layanan">
                <div class="page-shell">
                    <x-section-heading
                        eyebrow="LAYANAN"
                        title="Satu mitra untuk seluruh kebutuhan kegiatan Anda"
                    />
                    <div class="service-grid">
                        @foreach ($services as $index => $service)
                            <x-service-card
                                :title="$service['title']"
                                :description="$service['description']"
                                :icon="$service['icon']"
                                :number="$index + 1"
                                :href="in_array($service['slug'], ['tour-wisata', 'event-gathering', 'outbound', 'rental-armada', 'team-building'], true) ? route('services.show', $service['slug']) : route('services.index')"
                            />
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="content-section content-section-tint home-portfolio" id="portofolio">
                <div class="page-shell">
                    <x-section-heading
                        eyebrow="PORTOFOLIO PILIHAN"
                        title="Kegiatan yang telah kami wujudkan"
                        subtitle="Klik kegiatan untuk melihat dokumentasi lengkapnya."
                    />
                    <div
                        class="portfolio-carousel"
                        role="region"
                        aria-label="Portofolio pilihan"
                        aria-roledescription="carousel"
                        data-portfolio-carousel
                        data-active-index="{{ count($portfolios) + min(3, max(0, count($portfolios) - 1)) }}"
                    >
                        <div class="portfolio-carousel-viewport">
                            @foreach ([0, 1, 2] as $copyIndex)
                                @foreach ($portfolios as $index => [$title, $location, $category, $year, $slug, $image])
                                    <div
                                        class="portfolio-slide {{ $copyIndex === 1 && $index === min(3, max(0, count($portfolios) - 1)) ? 'is-active' : '' }}"
                                        role="group"
                                        aria-roledescription="slide"
                                        aria-label="{{ $index + 1 }} dari {{ count($portfolios) }}"
                                        aria-hidden="{{ $copyIndex === 1 && $index === min(3, max(0, count($portfolios) - 1)) ? 'false' : 'true' }}"
                                        data-portfolio-slide
                                        data-index="{{ $index }}"
                                        data-copy="{{ $copyIndex }}"
                                    >
                                        <x-portfolio-card
                                            :title="$title"
                                            :location="$location"
                                            :category="$category"
                                            :year="$year"
                                            :image="str_starts_with($image, 'http') ? $image : asset($image)"
                                            :href="in_array($slug, ['rapat-kerja-nasional-instansi', 'gathering-tahunan-400-karyawan', 'studi-lapangan-mahasiswa-arsitektur', 'outbound-pegawai-dinas', 'team-building-startup-teknologi', 'wisuda-wisata-angkatan'], true) ? route('portfolios.show', $slug) : route('portfolios.index')"
                                        />
                                    </div>
                                @endforeach
                            @endforeach

                            <button
                                class="portfolio-carousel-arrow portfolio-carousel-prev"
                                type="button"
                                aria-label="Portofolio sebelumnya"
                                data-portfolio-prev
                            >
                                <img src="{{ asset('images/figma/icon-chevron-left.svg') }}" alt="" aria-hidden="true">
                            </button>
                            <button
                                class="portfolio-carousel-arrow portfolio-carousel-next"
                                type="button"
                                aria-label="Portofolio berikutnya"
                                data-portfolio-next
                            >
                                <img src="{{ asset('images/figma/icon-chevron-right.svg') }}" alt="" aria-hidden="true">
                            </button>
                        </div>

                        <div class="portfolio-carousel-dots" role="group" aria-label="Pilih portofolio">
                            @foreach ($portfolios as $index => [$title])
                                <button
                                    class="portfolio-carousel-dot {{ $index === min(3, max(0, count($portfolios) - 1)) ? 'is-active' : '' }}"
                                    type="button"
                                    aria-label="Tampilkan {{ $title }}"
                                    aria-pressed="{{ $index === min(3, max(0, count($portfolios) - 1)) ? 'true' : 'false' }}"
                                    data-portfolio-dot="{{ $index }}"
                                ></button>
                            @endforeach
                        </div>

                        <div class="section-actions">
                            <a class="button-secondary button-secondary-dark" href="{{ route('portfolios.index') }}">
                                Semua portofolio <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content-section page-shell" id="klien">
                <x-section-heading
                    eyebrow="KLIEN KAMI"
                    title="Dipercaya berbagai instansi dan perusahaan"
                />
                <div class="client-grid">
                    @foreach ($clients as $client)
                        <x-client-logo :name="$client" />
                    @endforeach
                </div>
            </section>

            <x-why-empirise />

            <section class="content-section content-section-tint">
                <div class="page-shell">
                    <x-section-heading
                        eyebrow="TESTIMONI TERBARU"
                        title="Apa kata klien kami"
                        align="left"
                    />
                    <div class="testimonial-grid">
                        @foreach ($testimonials as [$quote, $author])
                            <x-testimonial-card :quote="$quote" :author="$author" />
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="consultation-section" id="kontak">
                <div class="page-shell consultation-content">
                    <h2>Siap merancang kegiatan berikutnya?</h2>
                    <x-consultation-button
                        :href="route('consultation.create')"
                        :show-arrow="false"
                    />
                </div>
            </section>
        </main>

        <x-site-footer />

        <x-assistant-widget />
    </body>
</html>
