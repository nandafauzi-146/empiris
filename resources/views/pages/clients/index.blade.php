@extends('layouts.public')

@section('title', 'Klien — Empirise Jalan-Jalan')
@section('meta_description', 'Kenali instansi dan perusahaan yang mempercayakan kegiatan bersama Empirise.')

@section('content')
    @php
        $clients = [
            'SMP Insan Rabbani', 'UTY', 'UAD', 'UNY', 'KPU', "Mu'allimin", 'BAZNAS', 'The Rain',
            'PN Wates', 'Ngatmombilung', 'Pendheza', 'Lembaga Yogya', 'Kemendikdasmen', 'KAI',
            'Pertamina', 'ADHI', 'IALF', 'Samsung', 'KONE', 'Rocket Chicken',
            'Muhammadiyah Wirobrajan', 'Poltek YKPN', 'Kemenag Lembaga',
        ];

        if (in_array('klien', $managedContentTypes, true)) {
            $clients = $managedContent->get('klien', collect())
                ->filter(fn ($content): bool => ($content->metadata['bagian'] ?? 'beranda') === 'beranda')
                ->take(24)
                ->pluck('judul')
                ->all();
        }
    @endphp

    <div class="public-page">
        <section class="page-intro page-shell">
            <p class="section-eyebrow">KLIEN KAMI</p>
            <h1>Dipercaya berbagai instansi dan perusahaan.</h1>
            <p>Terima kasih telah mempercayakan perjalanan dan kegiatan Anda bersama Empirise.</p>
        </section>

        <section class="content-section page-shell">
            <div class="client-grid">
                @foreach ($clients as $client)
                    <x-client-logo :name="$client" />
                @endforeach
            </div>
        </section>
    </div>
@endsection
