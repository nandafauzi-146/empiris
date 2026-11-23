@extends('layouts.public')

@section('title', 'Portofolio — Empirise Jalan-Jalan')
@section('meta_description', 'Lihat contoh kegiatan dan perjalanan yang didampingi oleh Empirise Jalan-Jalan.')

@section('content')
    @php
        $portfolios = [
            ['Rapat Kerja Nasional Instansi', 'Bandung', 'Instansi', '2025', 'rapat-kerja-nasional-instansi', null],
            ['Gathering Tahunan 400 Karyawan', 'Yogyakarta', 'Korporat', '2024', 'gathering-tahunan-400-karyawan', null],
            ['Studi Lapangan Mahasiswa Arsitektur', 'Bali', 'Kampus', '2024', 'studi-lapangan-mahasiswa-arsitektur', null],
            ['Outbound Pegawai Dinas', 'Bogor', 'Instansi', '2023', 'outbound-pegawai-dinas', null],
            ['Team Building Startup Teknologi', 'Labuan Bajo', 'Korporat', '2025', 'team-building-startup-teknologi', null],
            ['Wisuda & Wisata Angkatan', 'Malang', 'Kampus', '2025', 'wisuda-wisata-angkatan', null],
        ];

        if (in_array('portofolio', $managedContentTypes, true)) {
            $portfolios = $managedContent->get('portofolio', collect())
                ->take(6)
                ->map(fn ($content): array => [
                    $content->judul,
                    $content->metadata['lokasi'] ?? $content->metadata['location'] ?? '',
                    $content->metadata['kategori'] ?? $content->metadata['category'] ?? '',
                    $content->metadata['tahun'] ?? $content->metadata['year'] ?? '',
                    $content->slug ?? '',
                    $content->metadata['image'] ?? $content->metadata['gambar'] ?? null,
                ])->all();
        }
    @endphp

    <div class="public-page">
        <section class="page-intro page-shell">
            <p class="section-eyebrow">PORTOFOLIO PILIHAN</p>
            <h1>Kegiatan yang telah kami wujudkan.</h1>
            <p>Contoh kegiatan perjalanan, gathering, dan team building untuk beragam kelompok.</p>
        </section>

        <section class="content-section page-shell">
            <div class="listing-controls" aria-label="Filter portofolio">
                <label class="listing-filter" for="portfolio-category">
                    Kategori
                    <select id="portfolio-category" data-portfolio-category>
                        <option value="">Semua kategori</option>
                        <option>Instansi</option>
                        <option>Korporat</option>
                        <option>Kampus</option>
                    </select>
                </label>
                <label class="listing-filter" for="portfolio-year">
                    Tahun
                    <select id="portfolio-year" data-portfolio-year>
                        <option value="">Semua tahun</option>
                        <option>2025</option>
                        <option>2024</option>
                        <option>2023</option>
                    </select>
                </label>
            </div>
            <div class="portfolio-grid">
                @foreach ($portfolios as [$title, $location, $category, $year, $slug, $image])
                    <div data-portfolio-listing data-category="{{ $category }}" data-year="{{ $year }}">
                        <x-portfolio-card
                            :title="$title"
                            :location="$location"
                            :category="$category"
                            :year="$year"
                            :image="$image ? (str_starts_with($image, 'http') ? $image : asset($image)) : null"
                            :href="in_array($slug, ['rapat-kerja-nasional-instansi', 'gathering-tahunan-400-karyawan', 'studi-lapangan-mahasiswa-arsitektur', 'outbound-pegawai-dinas', 'team-building-startup-teknologi', 'wisuda-wisata-angkatan'], true) ? route('portfolios.show', $slug) : route('portfolios.index')"
                        />
                    </div>
                @endforeach
            </div>
            <p class="listing-empty hidden" data-portfolio-empty>Tidak ada portofolio yang cocok dengan pilihan Anda.</p>
        </section>
    </div>
@endsection
