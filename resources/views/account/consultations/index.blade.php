@extends('layouts.account')

@section('title', 'Konsultasi saya — Empirise Jalan-Jalan')
@section('meta_description', 'Lihat status dan riwayat konsultasi Anda dengan Empirise.')

@section('account-content')
    <header class="account-panel-header">
        <div>
            <p class="account-page-eyebrow">Dashboard klien</p>
            <h1>Ringkasan akun Anda</h1>
        </div>
        <a class="account-primary-action" href="{{ route('account.consultations.create') }}">Konsultasi baru</a>
    </header>

    @if (session('status'))
        <p class="account-status" role="status">{{ session('status') }}</p>
    @endif

    <section class="account-dashboard-stats" aria-label="Ringkasan akun">
        <article class="account-stat-card">
            <p>Konsultasi aktif</p>
            <strong>{{ $activeConsultationCount }}</strong>
            <a href="#daftar-konsultasi">Lihat konsultasi <span aria-hidden="true">→</span></a>
        </article>
        <article class="account-stat-card">
            <p>Proyek aktif</p>
            <strong>{{ $activeProjectCount }}</strong>
            <a href="{{ route('account.projects.index') }}">Buka proyek <span aria-hidden="true">→</span></a>
        </article>
        <article class="account-stat-card">
            <p>Termin belum bayar</p>
            <strong>{{ $outstandingPaymentCount }}</strong>
            <a href="{{ route('account.projects.index') }}">Lihat pembayaran <span aria-hidden="true">→</span></a>
        </article>
    </section>

    @if ($consultations->isEmpty())
        <div class="account-empty-state">
            <h2>Belum ada konsultasi.</h2>
            <p>Mulai dengan menceritakan rencana kegiatan Anda kepada tim Empirise.</p>
            <a class="account-primary-action" href="{{ route('account.consultations.create') }}">Mulai konsultasi</a>
        </div>
    @else
        <section id="daftar-konsultasi" class="account-recent-section" aria-labelledby="consultation-list-title">
            <div class="account-section-heading">
                <h2 id="consultation-list-title">Konsultasi saya</h2>
            </div>
            <div class="account-consultation-list">
            @foreach ($consultations as $consultation)
                <a class="account-consultation-card" href="{{ route('account.consultations.show', $consultation) }}">
                    <div class="account-consultation-card-copy">
                        <p class="account-consultation-reference">
                            {{ $consultation->nomor_konsultasi }}
                            <span aria-hidden="true">/</span>
                            <time datetime="{{ $consultation->created_at->toDateString() }}">
                                {{ $consultation->created_at->locale('id')->translatedFormat('j M Y') }}
                            </time>
                        </p>
                        <h2>{{ $consultation->layanan }}</h2>
                        <p class="account-consultation-summary">
                            @if ($consultation->durasi_hari)
                                {{ $consultation->durasi_hari }} hari
                            @else
                                Durasi belum ditentukan
                            @endif
                            <span aria-hidden="true">,</span>
                            {{ $consultation->tujuan ?: 'Tujuan belum ditentukan' }}
                        </p>
                        @if ($consultation->layanan === 'Rental Armada')
                            <p class="account-consultation-summary">
                                {{ $consultation->jenis_kendaraan ?: 'Jenis kendaraan belum ditentukan' }}
                                <span aria-hidden="true">·</span>
                                Penjemputan: {{ $consultation->lokasi_jemput ?: 'Belum ditentukan' }}
                            </p>
                        @endif
                    </div>
                    <x-consultation-status-badge :status="$consultation->status" />
                </a>
            @endforeach
            </div>
        </section>

        {{ $consultations->links() }}
    @endif
@endsection
