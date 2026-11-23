@extends('layouts.public')

@php
    $packages = [
        'jelajah-solo' => ['Jelajah Solo', '3 hari 2 malam', 'Kota batik, keraton, kuliner, dan suasana khas Solo.'],
        'pesona-dieng' => ['Pesona Dieng', '3 hari 2 malam', 'Udara pegunungan, telaga, dan lanskap alam dataran tinggi Dieng.'],
        'eksplorasi-bali' => ['Eksplorasi Bali', '4 hari 3 malam', 'Pengalaman budaya, pesisir, dan keindahan alam Bali.'],
    ];
    [$packageName, $packageDuration, $packageSummary] = $packages[$slug];
@endphp

@section('title', $packageName.' — Paket Empirise')
@section('meta_description', $packageSummary)

@section('content')
    <div class="public-page">
        <section class="page-intro page-shell">
            <p class="section-eyebrow">PAKET PERJALANAN</p>
            <h1>{{ $packageName }}</h1>
            <p>{{ $packageSummary }}</p>
            <p class="detail-meta">{{ $packageDuration }} <span aria-hidden="true">·</span> Rencana dapat disesuaikan</p>
            <x-consultation-button :href="route('consultation.create')" />
        </section>

        <section class="content-section content-section-tint">
            <div class="page-shell package-detail-grid">
                <div>
                    <x-section-heading eyebrow="RENCANA PERJALANAN" title="Gambaran kegiatan" align="left" />
                    <ol class="itinerary-list">
                        <li><span>Hari 1</span><p>Kedatangan, orientasi, dan waktu untuk memulai perjalanan.</p></li>
                        <li><span>Hari 2</span><p>Jelajahi destinasi utama bersama rombongan.</p></li>
                        <li><span>Hari 3</span><p>Waktu bebas, perjalanan pulang, dan penutupan kegiatan.</p></li>
                    </ol>
                </div>
                <aside class="package-note">
                    <h2>Rincian paket</h2>
                    <p>Harga dan fasilitas bergantung pada tanggal, jumlah peserta, dan kebutuhan perjalanan.</p>
                    <x-consultation-button :href="route('consultation.create')" label="Tanyakan paket ini" :show-arrow="false" />
                </aside>
            </div>
        </section>
    </div>
@endsection
