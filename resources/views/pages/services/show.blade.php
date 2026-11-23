@extends('layouts.public')

@php
    $services = [
        'tour-wisata' => ['Tour & Wisata', 'Perjalanan wisata domestik dan internasional yang direncanakan sesuai minat, durasi, dan kebutuhan rombongan.'],
        'event-gathering' => ['Event & Gathering', 'Pendampingan acara perusahaan, gathering, dan perayaan mulai dari konsep, susunan kegiatan, hingga pelaksanaan.'],
        'outbound' => ['Outbound', 'Kegiatan luar ruang dan fun games yang dirancang untuk meningkatkan kebersamaan dengan fasilitator berpengalaman.'],
        'rental-armada' => ['Rental Armada', 'Pilihan bus dan minibus untuk membantu perjalanan kelompok dengan nyaman bersama pengemudi profesional.'],
        'team-building' => ['Team Building', 'Program pengembangan tim dengan aktivitas kolaboratif, sesi refleksi, dan tujuan yang disepakati bersama.'],
    ];
    [$serviceTitle, $serviceDescription] = $services[$slug];
    $serviceImage = null;

    if (in_array('layanan', $managedContentTypes, true)) {
        $managedService = $managedContent->get('layanan', collect())->firstWhere('slug', $slug);
        abort_unless($managedService, 404);

        $serviceTitle = $managedService->judul;
        $serviceDescription = $managedService->deskripsi ?? '';
        $serviceImage = $managedService->metadata['image'] ?? $managedService->metadata['gambar'] ?? null;
    }
@endphp

@section('title', $serviceTitle.' — Empirise Jalan-Jalan')
@section('meta_description', $serviceDescription)

@section('content')
    <div class="public-page">
        <section class="page-intro page-shell">
            <p class="section-eyebrow">LAYANAN EMPIRISE</p>
            <h1>{{ $serviceTitle }}</h1>
            <p>{{ $serviceDescription }}</p>
            @if (is_string($serviceImage) && $serviceImage !== '')
                <img class="mt-6 max-h-96 w-full rounded-3xl object-cover" src="{{ str_starts_with($serviceImage, 'http') ? $serviceImage : asset($serviceImage) }}" alt="{{ $serviceTitle }}" loading="lazy">
            @endif
            <x-consultation-button :href="route('consultation.create')" />
        </section>

        <section class="content-section content-section-tint">
            <div class="page-shell">
                <x-section-heading eyebrow="ALUR KERJA" title="Dari rencana hingga kegiatan selesai" />
                <ol class="process-list">
                    <li><span>01</span><div><h2>Konsultasi kebutuhan</h2><p>Bagikan tujuan, jumlah peserta, waktu, dan preferensi kegiatan.</p></div></li>
                    <li><span>02</span><div><h2>Rencana kegiatan</h2><p>Tim kami membantu menyusun rancangan perjalanan atau kegiatan untuk dibahas bersama.</p></div></li>
                    <li><span>03</span><div><h2>Persiapan dan pelaksanaan</h2><p>Setelah rencana disepakati, kami membantu koordinasi menuju hari kegiatan.</p></div></li>
                </ol>
            </div>
        </section>
    </div>
@endsection
