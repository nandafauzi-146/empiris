@extends('layouts.public')

@section('title', 'Layanan — Empirise Jalan-Jalan')
@section('meta_description', 'Lihat layanan tour, event, outbound, rental armada, dan team building dari Empirise.')

@section('content')
    @php
        $services = [
            ['title' => 'Tour & Wisata', 'description' => 'Perjalanan wisata domestik dan internasional dengan itinerary yang disesuaikan untuk kelompok Anda.', 'slug' => 'tour-wisata', 'icon' => 'map'],
            ['title' => 'Event & Gathering', 'description' => 'Perencanaan acara perusahaan, gathering, dan perayaan dari konsep hingga pelaksanaan.', 'slug' => 'event-gathering', 'icon' => 'calendar'],
            ['title' => 'Outbound', 'description' => 'Program outbound dan fun games dengan fasilitator berpengalaman untuk membangun kebersamaan.', 'slug' => 'outbound', 'icon' => 'gamepad'],
            ['title' => 'Rental Armada', 'description' => 'Sewa bus dan minibus dengan pengemudi profesional untuk perjalanan kelompok.', 'slug' => 'rental-armada', 'icon' => 'bus'],
            ['title' => 'Team Building', 'description' => 'Modul pengembangan tim dengan sesi refleksi dan kegiatan yang disesuaikan dengan tujuan.', 'slug' => 'team-building', 'icon' => 'users'],
        ];

        if (in_array('layanan', $managedContentTypes, true)) {
            $services = $managedContent->get('layanan', collect())
                ->take(5)
                ->map(fn ($content): array => [
                    'title' => $content->judul,
                    'description' => $content->deskripsi ?? '',
                    'slug' => $content->slug ?? '',
                    'icon' => $content->metadata['icon'] ?? 'map',
                ])->all();
        }
    @endphp

    <div class="public-page">
        <section class="page-intro page-shell">
            <p class="section-eyebrow">LAYANAN</p>
            <h1>Satu mitra untuk seluruh kebutuhan kegiatan Anda.</h1>
            <p>Dari memilih destinasi hingga pelaksanaan acara, kami membantu setiap tahap berjalan lebih mudah.</p>
        </section>

        <section class="content-section page-shell">
            <div class="service-grid">
                @foreach ($services as $index => $service)
                    <x-service-card
                        :title="$service['title']"
                        :description="$service['description']"
                        :number="$index + 1"
                        :icon="$service['icon']"
                        :href="in_array($service['slug'], ['tour-wisata', 'event-gathering', 'outbound', 'rental-armada', 'team-building'], true) ? route('services.show', $service['slug']) : route('services.index')"
                    />
                @endforeach
            </div>
        </section>
    </div>

    <section class="consultation-section">
        <div class="page-shell consultation-content">
            <h2>Ceritakan kebutuhan kegiatan Anda kepada tim kami.</h2>
            <x-consultation-button :href="route('consultation.create')" :show-arrow="false" />
        </div>
    </section>
@endsection
