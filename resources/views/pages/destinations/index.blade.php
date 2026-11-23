@extends('layouts.public')

@section('title', 'Destinasi — Empirise Jalan-Jalan')
@section('meta_description', 'Jelajahi pilihan destinasi perjalanan di Indonesia bersama Empirise Jalan-Jalan.')

@section('content')
    <div class="public-page">
        <section class="page-intro page-shell">
            <p class="section-eyebrow">DESTINASI</p>
            <h1>Temukan tempat untuk cerita perjalanan berikutnya.</h1>
            <p>Pilihan kota budaya, alam, dan pesisir untuk perjalanan bersama tim, keluarga, atau komunitas.</p>
        </section>

        <section class="content-section page-shell">
            <div class="listing-controls" aria-label="Filter destinasi">
                <label class="listing-search" for="destination-search">
                    Cari destinasi
                    <input id="destination-search" type="search" placeholder="Contoh: Solo" data-destination-search>
                </label>
                <label class="listing-filter" for="destination-region">
                    Wilayah
                    <select id="destination-region" data-destination-region>
                        <option value="">Semua wilayah</option>
                        <option>Jawa Tengah &amp; DIY</option>
                        <option>Jawa Timur</option>
                        <option>Bali</option>
                        <option>Nusa Tenggara</option>
                        <option>Jawa Barat</option>
                        <option>DKI Jakarta</option>
                    </select>
                </label>
                <label class="listing-filter" for="destination-category">
                    Kategori
                    <select id="destination-category" data-destination-category>
                        <option value="">Semua kategori</option>
                        <option>Budaya</option>
                        <option>Alam</option>
                        <option>Kota</option>
                    </select>
                </label>
            </div>
            <div class="destination-grid">
                @foreach ($destinations as $destination)
                    <a
                        class="destination-listing-card"
                        href="{{ route('destinations.show', $destination['slug']) }}"
                        data-destination-listing
                        data-name="{{ $destination['name'] }}"
                        data-region="{{ $destination['region'] }}"
                        data-category="{{ $destination['category'] }}"
                    >
                        <img src="{{ str_starts_with($destination['image'], 'http') ? $destination['image'] : asset($destination['image']) }}" alt="" loading="lazy">
                        <div>
                            <p>{{ $destination['region'] }} <span aria-hidden="true">·</span> {{ $destination['category'] }}</p>
                            <h2>{{ $destination['name'] }}</h2>
                        </div>
                    </a>
                @endforeach
            </div>
            <p class="listing-empty hidden" data-destination-empty>Tidak ada destinasi yang cocok dengan pilihan Anda.</p>
        </section>
    </div>
@endsection
