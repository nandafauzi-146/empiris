@extends('layouts.public')

@section('title', 'FAQ & Kontak — Empirise Jalan-Jalan')
@section('meta_description', 'Temukan jawaban pertanyaan umum atau hubungi tim Empirise Jalan-Jalan.')

@section('content')
    @php
        $faqs = [
            ['Layanan apa saja yang tersedia?', 'Kami melayani tour & wisata, event & gathering, outbound, rental armada, dan team building.'],
            ['Bisakah itinerary disesuaikan dengan kebutuhan kami?', 'Ya. Destinasi, kegiatan, durasi, dan kebutuhan rombongan dapat dibicarakan saat konsultasi.'],
            ['Bagaimana cara mengetahui biaya kegiatan?', 'Biaya disusun berdasarkan kebutuhan dan rincian kegiatan. Hubungi tim kami untuk membahas perkiraan yang sesuai.'],
            ['Kapan sebaiknya kami mulai merencanakan?', 'Hubungi kami lebih awal agar tersedia waktu untuk membahas pilihan tanggal, perjalanan, dan persiapan kegiatan.'],
        ];

        if (in_array('faq', $managedContentTypes, true)) {
            $faqs = $managedContent->get('faq', collect())
                ->map(fn ($content): array => [$content->judul, $content->deskripsi ?? ''])
                ->all();
        }
    @endphp

    <div class="public-page">
        <section class="page-intro page-shell">
            <p class="section-eyebrow">FAQ &amp; KONTAK</p>
            <h1>Ada yang ingin Anda tanyakan?</h1>
            <p>Hubungi tim kami untuk membahas layanan, rencana perjalanan, dan kebutuhan kegiatan Anda.</p>
        </section>

        <section class="content-section page-shell contact-grid">
            <div class="faq-list">
                <x-section-heading eyebrow="PERTANYAAN UMUM" title="Informasi sebelum merencanakan kegiatan" align="left" />
                @foreach ($faqs as [$question, $answer])
                    <details>
                        <summary>{{ $question }}</summary>
                        <p>{{ $answer }}</p>
                    </details>
                @endforeach
            </div>

            <aside class="contact-card">
                <h2>Hubungi Empirise</h2>
                <p><span>Email</span><a href="mailto:halo@empirise.id">halo@empirise.id</a></p>
                <p><span>WhatsApp</span><a href="https://wa.me/6281234567890">0812-3456-7890</a></p>
                <p><span>Legalitas</span><strong>NIB &amp; izin usaha pariwisata</strong></p>
                <x-consultation-button :href="route('consultation.create')" label="Kirim pertanyaan" :show-arrow="false" />
            </aside>
        </section>
    </div>
@endsection
