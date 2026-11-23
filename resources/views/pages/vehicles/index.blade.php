@extends('layouts.public')

@section('title', 'Armada — Empirise Jalan-Jalan')
@section('meta_description', 'Pilihan layanan sewa armada bus dan minibus untuk perjalanan kelompok.')

@section('content')
    <div class="public-page">
        <section class="page-intro page-shell">
            <p class="section-eyebrow">ARMADA</p>
            <h1>Perjalanan kelompok, lebih nyaman bersama.</h1>
            <p>Ajukan kebutuhan kendaraan, rute, dan jumlah peserta. Tim kami akan mengonfirmasi ketersediaan sebelum mengirim penawaran.</p>
        </section>

        <section class="content-section page-shell">
            <div class="vehicle-grid">
                <article class="vehicle-card"><span class="vehicle-number">01</span><h2>Bus pariwisata</h2><p>Untuk perjalanan rombongan dengan pilihan kapasitas dan fasilitas yang dapat dikonsultasikan.</p></article>
                <article class="vehicle-card"><span class="vehicle-number">02</span><h2>Minibus</h2><p>Alternatif kendaraan untuk kelompok yang lebih kecil dan perjalanan dengan rute fleksibel.</p></article>
                <article class="vehicle-card"><span class="vehicle-number">03</span><h2>Kendaraan pendukung</h2><p>Diskusikan kebutuhan kendaraan tambahan untuk mendukung susunan kegiatan Anda.</p></article>
            </div>
            <div class="section-actions">
                <x-consultation-button :href="route('consultation.create', ['layanan' => 'Rental Armada'])" label="Konsultasikan armada" :show-arrow="false" />
            </div>
        </section>
    </div>
@endsection
