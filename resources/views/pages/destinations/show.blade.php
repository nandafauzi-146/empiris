@extends('layouts.public')
@php
    $destinationName = $destination['name'];
    $destinationRegion = $destination['region'];
    $destinationCategory = $destination['category'];
    $destinationDescription = $destination['description'];
@endphp

@section('title', $destinationName.' — Destinasi Empirise')
@section('meta_description', $destinationDescription)

@section('content')
    <div class="public-page">
        <section class="destination-detail-hero">
            <img src="{{ str_starts_with($destination['image'], 'http') ? $destination['image'] : asset($destination['image']) }}" alt="" fetchpriority="high">
            <div class="destination-detail-shade" aria-hidden="true"></div>
            <div class="page-shell destination-detail-copy">
                <p class="section-eyebrow">{{ $destinationRegion }} <span aria-hidden="true">/</span> {{ $destinationCategory }}</p>
                <h1>{{ $destinationName }}</h1>
                <p>{{ $destinationDescription }}</p>
                <x-consultation-button :href="route('consultation.create')" />
            </div>
        </section>

        <section class="content-section page-shell">
            <x-section-heading eyebrow="RENCANAKAN PERJALANAN" title="Sesuaikan perjalanan dengan kebutuhan rombongan Anda" />
            <p class="page-callout">Durasi, pilihan kegiatan, dan anggaran dapat dibicarakan bersama tim Empirise saat konsultasi.</p>
            <div class="section-actions">
                <x-consultation-button :href="route('consultation.create')" :show-arrow="false" />
                <a class="button-secondary button-secondary-dark" href="{{ route('destinations.index') }}">Lihat destinasi lainnya</a>
            </div>
        </section>
    </div>
@endsection
