@extends('layouts.public')

@section('title', 'Tentang Empirise — Empirise Jalan-Jalan')
@section('meta_description', 'Kenali Empirise, mitra perjalanan, event, gathering, outbound, dan team building Anda.')

@section('content')
    @php
        $teamMembers = [
            ['Management Team', 'Juru pikir dan juru lambak.'],
            ['Operations Team', 'Penganut Faham “Oke, siap!”.'],
            ['Support Team', 'Penganut Faham “Oke, bisa!”.'],
        ];
        $aboutTestimonials = [
            ['Ust. Agus Mianta, S.Si., M.Pd.', 'Direktur, Madrasah Muallimin Yogyakarta', 'Tim kami sangat terbantu dengan Empirise atas perencanaan perjalanan, respons tim, dan koordinasi kegiatan.'],
            ['Bpk Donny', 'Ketua Pandu Employee Gathering KONE Glitchai', 'Tim kami sangat terbantu dengan Empirise atas perencanaan perjalanan, respons tim, dan koordinasi kegiatan.'],
            ['Bpk Budi Silaban', 'Managing Director PT. KONE Indonesia', 'Tim kami sangat terbantu dengan Empirise atas perencanaan perjalanan, respons tim, dan koordinasi kegiatan.'],
        ];
        $aboutClients = ['Kementerian', 'HUT RI', 'KONE', 'MunclinCorp', 'IAF', 'AMCOA', 'BBM UAD', 'UTY', 'Mualimin'];

        if (in_array('tim', $managedContentTypes, true)) {
            $teamMembers = $managedContent->get('tim', collect())
                ->take(8)
                ->map(fn ($content): array => [$content->judul, $content->deskripsi ?? ''])
                ->all();
        }

        if (in_array('testimoni', $managedContentTypes, true)) {
            $aboutTestimonials = $managedContent->get('testimoni', collect())
                ->filter(fn ($content): bool => ($content->metadata['bagian'] ?? '') === 'tentang')
                ->take(3)
                ->map(fn ($content): array => [
                    $content->metadata['penulis'] ?? $content->judul,
                    $content->metadata['jabatan'] ?? '',
                    $content->deskripsi ?? '',
                ])->all();
        }

        if (in_array('klien', $managedContentTypes, true)) {
            $aboutClients = $managedContent->get('klien', collect())
                ->filter(fn ($content): bool => ($content->metadata['bagian'] ?? '') === 'tentang')
                ->take(24)
                ->pluck('judul')
                ->all();
        }
    @endphp

    <div class="public-page">
        <section class="about-hero">
            <div class="page-shell about-hero-content">
                <p class="section-eyebrow">#TENTANGKAMI</p>
                <h1>PT Empirise Karya Hutama</h1>
                <p class="about-hero-tagline">Your Trip. Your Event. Your Story.</p>
                <p class="about-hero-motto"><em>Lets Go. Lets Celebrate!</em></p>
                <div class="about-hero-actions">
                    <x-consultation-button :href="route('consultation.create')" label="Rencanakan perjalanan" />
                    <a class="button-secondary-dark" href="#hubungi-kami">Hubungi kami</a>
                </div>
            </div>
        </section>

        <section class="content-section page-shell">
            <div class="about-story-grid">
                <div class="about-story-copy">
                    <p class="section-eyebrow">SIAPA KAMI</p>
                    <h2>Partner perjalanan dan event sejak 2016</h2>
                    <p>PT Empirise Karya Hutama berdiri sejak 2016 sebagai perusahaan penyedia jasa travel dan event package yang berlokasi di Kota Yogyakarta. Kami menawarkan kunjungan wisata, wisata religi/ziarah, antar jemput, kunjungan kerja, city tour, dan layanan event capacity building, company meeting, serta grand opening.</p>
                    <p>Kami bekerja sama dengan instansi perusahaan, lembaga pendidikan, lembaga pemerintahan, dan perorangan dalam menyediakan jasa transportasi di wilayah Indonesia. Keamanan, kenyamanan, dan kepuasan klien menjadi hal yang kami kedepankan.</p>
                </div>
                <div class="about-stat-grid" aria-label="Ringkasan Empirise">
                    <article><strong>2016</strong><span>Berdiri</span></article>
                    <article><strong>2023</strong><span>Resmi PT</span></article>
                    <article><strong>4</strong><span>Lini layanan</span></article>
                    <article><strong>Jawa-Bali+</strong><span>Area armada</span></article>
                </div>
            </div>
        </section>

        <section class="content-section page-shell">
            <p class="section-eyebrow">#HISTORY</p>
            <h2 class="about-section-title">Perjalanan Empirise</h2>
            <div class="about-history" aria-label="Riwayat Empirise">
                <article><time>2010</time><p>The journey begins here. Empirise memulai dengan bergerak di bidang kreatif.</p></article>
                <article><time>2016</time><p>Empirise melegalkan diri dengan legalitas CV, tempat perjalanan ini dimulai.</p></article>
                <article><time>2020an</time><p>Berkembang menjadi event and tour planner sekaligus penyedia sewa kendaraan.</p></article>
                <article><time>2023</time><p>Resmi menjadi PT (Perseroan Terbatas).</p></article>
                <article><time>2024 - sekarang</time><p>Melabarkan sayap bersama praktisi ahli untuk membenahi layanan: Event Organizer, Jalan-Jalan (Tour Planner), Outbound/MICE, serta Rental Car and Driver.</p></article>
            </div>
        </section>

        <section class="content-section page-shell">
            <p class="section-eyebrow">#ARAHKAMI</p>
            <h2 class="about-section-title">Visi dan misi</h2>
            <div class="about-vision-grid">
                <article class="about-info-card">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="5" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="1.5" fill="currentColor"/></svg>
                    <h3>Visi</h3>
                    <p>Menjadi partner perjalanan dan penyelenggara event terpercaya yang mengedepankan pelayanan profesional dan cerita berkesan di setiap perjalanan maupun acara.</p>
                </article>
                <article class="about-info-card">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <h3>Misi</h3>
                    <ol>
                        <li>Memberikan layanan perjalanan berkualitas dengan kebutuhan, kenyamanan, dan efisiensi.</li>
                        <li>Menyelenggarakan event kreatif, profesional, dan terorganisir.</li>
                        <li>Menjadi mitra transportasi yang responsif dan aman.</li>
                        <li>Memahami kebutuhan pelanggan secara personal.</li>
                        <li>Membangun hubungan jangka panjang dengan pelanggan.</li>
                        <li>Menjaga pengalaman perjalanan yang bermakna.</li>
                    </ol>
                </article>
            </div>
        </section>

        <section class="content-section page-shell">
            <p class="section-eyebrow">#LAYANANKAMI</p>
            <h2 class="about-section-title">Empat lini layanan</h2>
            <div class="about-service-grid">
                <article><span class="about-service-number">01</span><h3>EVENT ORGANIZER</h3><ul><li>Meeting, insentif, konferensi, exhibition</li><li>Grand opening</li><li>Karnaval</li><li>Gala dinner</li><li>Show dan hiburan</li></ul></article>
                <article><span class="about-service-number">02</span><h3>OUTBOUND</h3><ul><li>Team building</li><li>Fun outing</li><li>Ice breaking</li><li>Kegiatan komunitas</li></ul></article>
                <article><span class="about-service-number">03</span><h3>TOUR PLANNER</h3><ul><li>Family gathering</li><li>Employee gathering</li><li>Kunjungan industri</li><li>Study tour</li></ul></article>
                <article><span class="about-service-number">04</span><h3>RENTAL CAR AND DRIVER</h3><ul><li>Rangkaian VIP / VVIP</li><li>Kunjungan kerja</li><li>Wisata keluarga</li><li>Armada Jawa-Bali</li></ul></article>
            </div>
        </section>

        <section class="content-section page-shell">
            <p class="section-eyebrow">#LEGAL &amp; PERIJINAN</p>
            <h2 class="about-section-title">Legalitas dan izin usaha</h2>
            <div class="about-license-grid">
                @foreach (['Akta Pendirian PT', 'SK Kemenkumham', 'NPWP Perusahaan', 'Rekening Aktif PT', 'NIB', 'Biro Wisata Perjalanan'] as $license)
                    <article><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 2h9l4 4v16H6V2Z M15 2v5h5 M9 12h7M9 16h7" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg><div><strong>{{ $license }}</strong><span>Tersedia dan dapat diverifikasi.</span></div></article>
                @endforeach
            </div>
        </section>

        <section class="content-section page-shell">
            <p class="section-eyebrow">#KENAPA MEMILIH KAMI</p>
            <h2 class="about-section-title">Tujuh alasan memilih Empirise</h2>
            <div class="about-reason-grid">
                @foreach ([
                    'One Stop Solution',
                    'Bukan Sekadar Menjual Paket',
                    'Perjalanan yang Terencana',
                    'Service dengan Rasa “Aman”',
                    'Professional Team',
                    'Experience yang Bisa Dicontohkan',
                    'Flexible &amp; Customized',
                ] as $index => $reason)
                    <article><span>{{ $index + 1 }}</span><h3>{{ $reason }}</h3><p>{{ [
                        'Travel, event, dan transport dalam satu partner.',
                        'Kami memahami kebutuhan sebelum menjual produk.',
                        'Detail kecil diperhatikan, dari transportasi hingga koordinasi.',
                        'Komunikasi jelas, koordinasi rapi, dan respons tepat.',
                        'Tim siap dengan pengalaman lintas kebutuhan.',
                        'Perjalanan berkesan menjadi cerita yang mudah dikenang.',
                        'Solusi disesuaikan dengan kebutuhan, tujuan, dan anggaran.',
                    ][ $index ] }}</p></article>
                @endforeach
            </div>
        </section>

        <section class="content-section page-shell">
            <p class="section-eyebrow">#OUR TEAM</p>
            <h2 class="about-section-title">Tim di balik setiap acara</h2>
            <div class="about-team-grid">
                @foreach ($teamMembers as [$title, $description])
                    <article><div class="about-team-photo" aria-hidden="true"></div><div><h3>{{ $title }}</h3><p>{{ $description }}</p></div></article>
                @endforeach
            </div>
            <div class="about-team-services">
                @foreach (['Portal registrasi', 'Runner and medias', 'Layanan bagasi', 'Unggah video cepat'] as $item)
                    <span>{{ $item }}</span>
                @endforeach
            </div>
        </section>

        <section class="content-section page-shell">
            <p class="section-eyebrow">#CLIENT TESTIMONI</p>
            <h2 class="about-section-title">Dipercaya klien kami</h2>
            <div class="about-testimonial-grid">
                @foreach ($aboutTestimonials as [$name, $role, $quote])
                    <article><div class="about-stars" aria-label="5 dari 5 bintang">★★★★★</div><blockquote>{{ $quote }}</blockquote><strong>{{ $name }}</strong><span>{{ $role }}</span></article>
                @endforeach
            </div>
            <div class="about-client-strip" aria-label="Klien Empirise">
                @foreach ($aboutClients as $client)
                    <span>{{ $client }}</span>
                @endforeach
            </div>
        </section>

        <section class="content-section page-shell" id="hubungi-kami">
            <p class="section-eyebrow">#HUBUNGI KAMI</p>
            <h2 class="about-section-title">Hubungi kami</h2>
            <div class="about-contact-grid">
                <div class="about-contact-card">
                    <p><strong>PT. EMPIRISE KARYA HUTAMA</strong><br>Alamat Kantor Jl. Kyai Ahmad Dahlan No.59, Ngupasan, Gondomanan, Yogyakarta, D.I. Yogyakarta</p>
                    <p>Office: Jl. Puri Sewon Asri 7 Blok G, Sewon</p>
                    <p>0856 4306 2158 (Pram / WhatsApp &amp; telepon)</p>
                    <p>Instagram: @empirisejalanjalan · www.empirisejalanan.com</p>
                </div>
                <div class="about-contact-card about-contact-cta">
                    <p class="section-eyebrow">#TERIMAKASIH</p>
                    <h3>Anda tidak perlu memikirkan semuanya sendiri.</h3>
                    <p>Ceritakan tujuan, kebutuhan, dan harapan Anda. Kami membantu menyusun rencana perjalanan dan event yang tepat.</p>
                    <a class="button-primary about-whatsapp-button" href="https://wa.me/6285643062158" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M20.5 11.7A8.5 8.5 0 0 1 8 18.9L3.5 20l1.2-4.4A8.5 8.5 0 1 1 20.5 11.7Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8.8 8.2c.2-.5.5-.5.8-.5h.4c.2 0 .4.1.5.4l.7 1.7c.1.3 0 .5-.2.7l-.6.7c.5 1 1.3 1.8 2.3 2.3l.7-.6c.2-.2.5-.3.7-.2l1.7.7c.3.1.4.3.4.5v.4c0 .3-.1.6-.5.8-.5.3-1.2.5-1.9.3a7 7 0 0 1-3.2-1.8 7.2 7.2 0 0 1-1.8-3.2c-.2-.7 0-1.4.3-1.9Z" fill="currentColor"/>
                        </svg>
                        <span>Chat WhatsApp</span>
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
