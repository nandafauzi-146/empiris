@extends('layouts.account')

@section('title', 'Proyek & Pembayaran — Empirise Jalan-Jalan')
@section('meta_description', 'Lihat informasi proyek dan pembayaran akun Empirise Anda.')

@section('account-content')
    <header class="account-panel-header">
        <h1>Proyek &amp; Pembayaran</h1>
    </header>

    @forelse ($projects as $project)
        <section class="account-detail-card" aria-labelledby="project-{{ $project->id_proyek }}">
            <div class="account-consultation-card-header">
                <div>
                    <p class="section-eyebrow">{{ $project->nomor_proyek }}</p>
                    <h2 id="project-{{ $project->id_proyek }}">{{ $project->nama }}</h2>
                </div>
                <span class="rounded-full bg-brand-soft px-3 py-1 text-sm font-semibold text-brand">{{ ucfirst($project->status) }}</span>
            </div>
            <p class="mt-3">{{ $project->tgl_mulai->translatedFormat('d MMMM yyyy') }} – {{ $project->tgl_selesai->translatedFormat('d MMMM yyyy') }}</p>
            <p class="mt-1 font-semibold">Total proyek: Rp {{ number_format($project->total_nilai, 0, ',', '.') }}</p>
            <div class="mt-5 grid gap-3 md:grid-cols-3">
                @foreach ($project->terms as $term)
                    <article class="rounded-2xl bg-gray-50 p-4">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-semibold">{{ $term->nama }}</h3>
                            <span class="text-xs font-bold {{ $term->status === 'lunas' ? 'text-green-700' : 'text-red-700' }}">{{ $term->status === 'lunas' ? 'Lunas' : 'Belum bayar' }}</span>
                        </div>
                        <p class="mt-2">Rp {{ number_format($term->nominal, 0, ',', '.') }}</p>
                        <p class="mt-1 text-sm text-copy">Jatuh tempo {{ $term->jatuh_tempo->translatedFormat('d MMM yyyy') }}</p>
                        @if ($term->status === 'lunas' && $term->tgl_bayar)
                            <p class="mt-1 text-sm text-copy">Dibayar {{ $term->tgl_bayar->translatedFormat('d MMM yyyy') }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @empty
        <div class="account-empty-state">
            <h2>Belum ada proyek atau pembayaran.</h2>
            <p>Belum ada data proyek dan pembayaran yang tercatat untuk akun Anda.</p>
            <a class="account-primary-action" href="{{ route('contact') }}">Hubungi Empirise</a>
        </div>
    @endforelse
@endsection
