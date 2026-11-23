@extends('layouts.admin')

@section('title', 'Konsultasi masuk — Empirise Jalan-Jalan')
@section('meta_description', 'Kelola konsultasi calon klien Empirise.')

@section('content')
    <header class="mb-8">
        <p class="text-xs font-bold tracking-[0.18em] text-brand">ADMIN EMPIRISE</p>
        <h1 class="mt-2 text-3xl font-bold sm:text-4xl">Konsultasi masuk</h1>
        <p class="mt-2 text-copy">Tinjau kebutuhan klien dan balas melalui percakapan konsultasi.</p>
    </header>

    <section aria-label="Daftar konsultasi" class="space-y-4">
                @forelse ($consultations as $consultation)
                    <article class="account-consultation-card">
                        <div class="account-consultation-card-header">
                            <div>
                                <p class="section-eyebrow">{{ $consultation->nomor_konsultasi }}</p>
                                <h2>{{ $consultation->nama }} — {{ $consultation->layanan }}</h2>
                            </div>
                            <x-consultation-status-badge :status="$consultation->status" />
                        </div>
                        <dl class="account-consultation-meta">
                            <div><dt>Email</dt><dd>{{ $consultation->email }}</dd></div>
                            @if ($consultation->layanan === 'Rental Armada')
                                <div><dt>Kendaraan</dt><dd>{{ $consultation->jenis_kendaraan ?: 'Belum ditentukan' }}</dd></div>
                                <div><dt>Penjemputan</dt><dd>{{ $consultation->lokasi_jemput ?: 'Belum ditentukan' }}</dd></div>
                                <div><dt>Rute</dt><dd>{{ $consultation->tujuan ?: 'Belum ditentukan' }}</dd></div>
                                <div><dt>Tanggal</dt><dd>{{ $consultation->tanggal_rencana?->format('d/m/Y') ?: 'Fleksibel / belum ditentukan' }}</dd></div>
                            @endif
                            <div><dt>Pesan</dt><dd>{{ $consultation->replies_count }}</dd></div>
                            <div><dt>Diperbarui</dt><dd>{{ $consultation->updated_at->format('d/m/Y H:i') }}</dd></div>
                        </dl>
                        <a class="account-detail-link" href="{{ route('admin.consultations.show', $consultation) }}">Buka konsultasi <span aria-hidden="true">→</span></a>
                    </article>
                @empty
                    <div class="account-empty-state">
                        <h2>Belum ada konsultasi.</h2>
                        <p>Konsultasi yang masuk akan muncul di halaman ini.</p>
                    </div>
                @endforelse
                {{ $consultations->links() }}
    </section>
@endsection
