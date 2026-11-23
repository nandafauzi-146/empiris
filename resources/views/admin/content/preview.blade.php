@php
    $previewMetadata = $content?->metadata ?? [];
    $previewTitle = $content?->judul ?? '';
    $previewDescription = $content?->deskripsi ?? '';
    $previewImage = $previewMetadata['image'] ?? $previewMetadata['gambar'] ?? null;
    $previewImageUrl = is_string($previewImage) && $previewImage !== ''
        ? (str_starts_with($previewImage, 'http') ? $previewImage : asset($previewImage))
        : null;
    $previewIcon = $previewMetadata['icon'] ?? 'map';
    $previewRegion = $previewMetadata['region'] ?? $previewMetadata['wilayah'] ?? 'Indonesia';
    $previewLocation = $previewMetadata['location'] ?? $previewMetadata['lokasi'] ?? '';
    $previewCategory = $previewMetadata['category'] ?? $previewMetadata['kategori'] ?? '';
    $previewYear = $previewMetadata['year'] ?? $previewMetadata['tahun'] ?? '';
    $previewAuthor = $previewMetadata['author'] ?? $previewMetadata['penulis'] ?? $previewTitle;
    $previewRole = $previewMetadata['jabatan'] ?? '';
    $previewPart = $previewMetadata['bagian'] ?? 'beranda';
    $previewNumber = $previewNumber ?? 1;
    $previewId = 'content-preview-'.($content?->id_konten ?? 'new');
@endphp

<div class="sm:col-span-2">
    <button class="rounded-full border border-ink px-5 py-2.5 text-sm font-bold text-ink transition hover:bg-ink hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand" type="button" data-content-preview-open aria-haspopup="dialog">
        Pratinjau tampilan publik
    </button>
</div>

<dialog
    class="content-preview-dialog m-auto max-h-[92dvh] w-[min(92vw,56rem)] max-w-none overflow-hidden rounded-3xl border-0 bg-white p-0 text-ink shadow-2xl backdrop:bg-black/60"
    data-content-preview-dialog
    aria-labelledby="{{ $previewId }}-heading"
>
    <div class="flex items-start justify-between gap-4 border-b border-line bg-white px-5 py-4 sm:px-7">
        <div>
            <p class="text-xs font-bold tracking-[0.18em] text-brand">PRATINJAU PUBLIK</p>
            <h2 class="mt-1 text-lg font-bold" id="{{ $previewId }}-heading">Tampilan {{ $types[$contentType] }}</h2>
        </div>
        <button class="grid size-10 shrink-0 place-items-center rounded-full text-2xl leading-none text-copy transition hover:bg-neutral-100 hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand" type="button" data-content-preview-close aria-label="Tutup pratinjau">&times;</button>
    </div>
    <div
        class="content-preview-stage max-h-[calc(92dvh-5rem)] overflow-auto p-5 sm:p-8"
        data-content-preview-stage
        data-content-preview
        data-content-type="{{ $contentType }}"
        data-image-prefix="{{ url('/') }}/"
    >
        @switch($contentType)
            @case('destinasi')
                <section class="destination-detail-hero">
                    <img
                        src="{{ $previewImageUrl ?? asset('images/figma/solo.png') }}"
                        alt=""
                        data-preview-public-image
                    >
                    <div class="destination-detail-shade" aria-hidden="true"></div>
                    <div class="page-shell destination-detail-copy">
                        <p class="section-eyebrow">
                            <span data-preview-public-region>{{ $previewRegion }}</span>
                            <span aria-hidden="true"> / </span>
                            <span data-preview-public-category>{{ $previewCategory ?: 'Wisata' }}</span>
                        </p>
                        <h1 data-preview-public-title>{{ $previewTitle ?: 'Nama destinasi' }}</h1>
                        <p data-preview-public-description>{{ $previewDescription ?: 'Deskripsi destinasi akan tampil di sini.' }}</p>
                        <a class="consultation-button" href="#" tabindex="-1" aria-label="Mulai Konsultasi">
                            Mulai Konsultasi
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                </section>
                @break
            @case('layanan')
                <div class="service-grid">
                    <article class="service-card">
                        <span class="service-card-icon" aria-hidden="true" data-preview-public-icon>
                            @switch($previewIcon)
                                @case('calendar')
                                    <svg viewBox="0 0 24 24"><path d="M7 2v3M17 2v3M3.5 9h17M5 4h14a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/></svg>
                                    @break
                                @case('gamepad')
                                    <svg viewBox="0 0 24 24"><path d="M7 16H5.5A3.5 3.5 0 0 1 2 12.5v-1A3.5 3.5 0 0 1 5.5 8h13A3.5 3.5 0 0 1 22 11.5v1a3.5 3.5 0 0 1-3.5 3.5H17l-2 3h-6l-2-3ZM7 11v4M5 13h4M16.5 12h.01M18.5 14h.01"/></svg>
                                    @break
                                @case('bus')
                                    <svg viewBox="0 0 24 24"><path d="M5 16V6a3 3 0 0 1 3-3h8a3 3 0 0 1 3 3v10M3 16h18M5 16v3M19 16v3M7 19h10M7 7h10v5H7z"/></svg>
                                    @break
                                @case('users')
                                    <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    @break
                                @default
                                    <svg viewBox="0 0 24 24"><path d="M3 6l6-3 6 3 6-3v15l-6 3-6-3-6 3V6ZM9 3v15M15 6v15"/></svg>
                            @endswitch
                        </span>
                        <span class="service-card-number" aria-hidden="true">{{ str_pad((string) $previewNumber, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 data-preview-public-title>{{ $previewTitle ?: 'Nama layanan' }}</h3>
                        <p data-preview-public-description>{{ $previewDescription ?: 'Deskripsi layanan akan tampil di sini.' }}</p>
                        <a class="text-link" href="#" tabindex="-1">Pelajari layanan <span aria-hidden="true">→</span></a>
                    </article>
                </div>
                @break
            @case('portofolio')
                <div class="portfolio-grid">
                    <article class="portfolio-card" data-preview-public-portfolio>
                        <div class="portfolio-card-art" data-preview-public-art aria-hidden="true" @if ($previewImageUrl) hidden @endif>
                            <span data-preview-public-category>{{ $previewCategory ?: 'Kategori' }}</span>
                        </div>
                        <img
                            class="portfolio-card-image"
                            @if ($previewImageUrl) src="{{ $previewImageUrl }}" @else hidden @endif
                            alt=""
                            aria-hidden="true"
                            data-preview-public-image
                        >
                        <div class="portfolio-card-art portfolio-card-art-cover" aria-hidden="true" data-preview-public-image-art @if (! $previewImageUrl) hidden @endif>
                            <span data-preview-public-image-category>{{ $previewCategory ?: 'Kategori' }} / {{ $previewYear ?: 'Tahun' }}</span>
                        </div>
                        <div class="portfolio-card-content">
                            <p class="portfolio-card-meta" data-preview-public-plain-meta @if ($previewImageUrl) hidden @endif>
                                <span data-preview-public-location>{{ $previewLocation ?: 'Lokasi' }}</span>
                                <span aria-hidden="true"> · </span>
                                <span data-preview-public-year>{{ $previewYear ?: 'Tahun' }}</span>
                            </p>
                            <p class="portfolio-card-meta" data-preview-public-image-meta @if (! $previewImageUrl) hidden @endif>
                                <span class="portfolio-card-location">
                                    <img src="{{ asset('images/figma/icon-pin.svg') }}" alt="" aria-hidden="true">
                                    <span data-preview-public-image-location>{{ $previewLocation ?: 'Lokasi' }}</span>
                                </span>
                            </p>
                            <h3 data-preview-public-title>{{ $previewTitle ?: 'Nama portofolio' }}</h3>
                            <a class="text-link" href="#" tabindex="-1" data-preview-public-link>Lihat dokumentasi <span aria-hidden="true">→</span></a>
                        </div>
                    </article>
                </div>
                @break
            @case('klien')
                <div class="client-grid">
                    <div class="client-logo" role="img" aria-label="{{ $previewTitle ?: 'Nama klien' }}" data-preview-variant="beranda">
                        <span data-preview-public-title>{{ $previewTitle ?: 'Nama klien' }}</span>
                    </div>
                    <div class="about-client-strip hidden" data-preview-variant="tentang" hidden>
                        <span data-preview-public-title>{{ $previewTitle ?: 'Nama klien' }}</span>
                    </div>
                </div>
                @break
            @case('testimoni')
                <div class="testimonial-grid" data-preview-variant="beranda">
                    <figure class="testimonial-card">
                        <div class="testimonial-stars" aria-label="5 dari 5 bintang">
                            @for ($star = 0; $star < 5; $star++)
                                <svg viewBox="0 0 20 20" aria-hidden="true"><path d="m10 1.8 2.5 5.1 5.7.8-4.1 4 .97 5.7L10 14.7l-5.1 2.7.97-5.7-4.1-4 5.7-.8L10 1.8Z" fill="currentColor"/></svg>
                            @endfor
                        </div>
                        <blockquote data-preview-public-description>“{{ $previewDescription ?: 'Kutipan testimoni akan tampil di sini.' }}”</blockquote>
                        <figcaption data-preview-public-author>{{ $previewAuthor ?: 'Nama pemberi testimoni' }}</figcaption>
                    </figure>
                </div>
                <div class="about-testimonial-grid hidden" data-preview-variant="tentang" hidden>
                    <article>
                        <div class="about-stars" aria-label="5 dari 5 bintang">★★★★★</div>
                        <blockquote data-preview-public-description>{{ $previewDescription ?: 'Kutipan testimoni akan tampil di sini.' }}</blockquote>
                        <strong data-preview-public-author>{{ $previewAuthor ?: 'Nama pemberi testimoni' }}</strong>
                        <span data-preview-public-role>{{ $previewRole }}</span>
                    </article>
                </div>
                @break
            @case('faq')
                <div class="faq-list">
                    <details>
                        <summary data-preview-public-title>{{ $previewTitle ?: 'Pertanyaan yang sering diajukan' }}</summary>
                        <p data-preview-public-description>{{ $previewDescription ?: 'Jawaban akan tampil di sini.' }}</p>
                    </details>
                </div>
                @break
            @case('tim')
                <div class="about-team-grid">
                    <article>
                        <div class="about-team-photo" aria-hidden="true"></div>
                        <div>
                            <h3 data-preview-public-title>{{ $previewTitle ?: 'Nama tim' }}</h3>
                            <p data-preview-public-description>{{ $previewDescription ?: 'Deskripsi tim akan tampil di sini.' }}</p>
                        </div>
                    </article>
                </div>
                @break
        @endswitch
    </div>
</dialog>
