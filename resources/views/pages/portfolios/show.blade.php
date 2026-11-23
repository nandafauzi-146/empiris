@extends('layouts.public')

@php
    $portfolios = [
        'rapat-kerja-nasional-instansi' => ['Rapat Kerja Nasional Instansi', 'Bandung', 'Instansi', '2025'],
        'gathering-tahunan-400-karyawan' => ['Gathering Tahunan 400 Karyawan', 'Yogyakarta', 'Korporat', '2024'],
        'studi-lapangan-mahasiswa-arsitektur' => ['Studi Lapangan Mahasiswa Arsitektur', 'Bali', 'Kampus', '2024'],
        'outbound-pegawai-dinas' => ['Outbound Pegawai Dinas', 'Bogor', 'Instansi', '2023'],
        'team-building-startup-teknologi' => ['Team Building Startup Teknologi', 'Labuan Bajo', 'Korporat', '2025'],
        'wisuda-wisata-angkatan' => ['Wisuda & Wisata Angkatan', 'Malang', 'Kampus', '2025'],
    ];
    [$portfolioTitle, $portfolioLocation, $portfolioCategory, $portfolioYear] = $portfolios[$slug];
    $portfolioImage = null;
    $portfolioDescription = 'Rincian dokumentasi kegiatan ini belum tersedia di situs. Hubungi tim kami untuk membahas kegiatan serupa.';

    if (in_array('portofolio', $managedContentTypes, true)) {
        $managedPortfolio = $managedContent->get('portofolio', collect())->firstWhere('slug', $slug);
        abort_unless($managedPortfolio, 404);

        $portfolioTitle = $managedPortfolio->judul;
        $portfolioLocation = $managedPortfolio->metadata['lokasi'] ?? $managedPortfolio->metadata['location'] ?? '';
        $portfolioCategory = $managedPortfolio->metadata['kategori'] ?? $managedPortfolio->metadata['category'] ?? '';
        $portfolioYear = $managedPortfolio->metadata['tahun'] ?? $managedPortfolio->metadata['year'] ?? '';
        $portfolioImage = $managedPortfolio->metadata['image'] ?? $managedPortfolio->metadata['gambar'] ?? null;
        $portfolioDescription = $managedPortfolio->deskripsi ?? $portfolioDescription;
    }
@endphp

@section('title', $portfolioTitle.' — Portofolio Empirise')
@section('meta_description', $portfolioDescription)

@section('content')
    <div class="public-page">
        <section class="page-intro page-shell">
            <p class="section-eyebrow">{{ $portfolioCategory }} <span aria-hidden="true">/</span> {{ $portfolioYear }}</p>
            <h1>{{ $portfolioTitle }}</h1>
            <p>{{ $portfolioLocation }} <span aria-hidden="true">·</span> Kegiatan bersama Empirise Jalan-Jalan</p>
            <p>{{ $portfolioDescription }}</p>
            @if (is_string($portfolioImage) && $portfolioImage !== '')
                <img class="mt-6 max-h-96 w-full rounded-3xl object-cover" src="{{ str_starts_with($portfolioImage, 'http') ? $portfolioImage : asset($portfolioImage) }}" alt="{{ $portfolioTitle }}" loading="lazy">
            @endif
            <x-consultation-button :href="route('consultation.create')" />
        </section>
    </div>
@endsection
