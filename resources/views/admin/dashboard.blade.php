@extends('layouts.admin')

@section('title', 'Dashboard admin — Empirise Jalan-Jalan')
@section('meta_description', 'Ringkasan konsultasi, penawaran, proyek, dan pembayaran Empirise.')

@section('content')
    <header class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold tracking-[0.18em] text-brand-500">PANEL ADMIN</p>
            <h1 class="mt-2 text-[28px] font-semibold leading-[1.2] text-ink">Dashboard</h1>
            <p class="mt-2 text-[14px] leading-6 text-copy-muted">Ringkasan pekerjaan dan aktivitas Empirise.</p>
        </div>
        <time class="hidden text-sm text-copy-muted sm:block" datetime="{{ today()->toDateString() }}">{{ today()->translatedFormat('d MMMM yyyy') }}</time>
    </header>

    <section class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5" aria-label="Statistik">
        @foreach ([
            ['Konsultasi baru', $newConsultationCount],
            ['Penawaran menunggu', $pendingOfferCount],
            ['Perlu ditanggapi', $needsResponseCount],
            ['Proyek berjalan', $runningProjectCount],
            ['Pembayaran jatuh tempo', $overduePaymentCount],
        ] as $index => [$label, $count])
            <article @class([
                'admin-stat-card',
                'admin-stat-card-attention' => $index === 2,
            ])>
                <p>{{ $label }}</p>
                <strong>{{ $count }}</strong>
            </article>
        @endforeach
    </section>

    <div class="mt-8 grid gap-6 xl:grid-cols-2">
        <section class="rounded-3xl border border-line p-6" aria-labelledby="tasks-title">
            <div class="flex items-center justify-between gap-4">
                <h2 id="tasks-title" class="text-xl font-bold">Tugas hari ini</h2>
                <a class="text-sm font-semibold text-brand hover:underline" href="{{ route('admin.consultations.index', ['perlu_ditanggapi' => 1]) }}">Semua tugas</a>
            </div>
            <div class="mt-4 divide-y divide-line">
                @forelse ($todayTasks as $task)
                    <a class="admin-list-row" href="{{ route('admin.consultations.show', $task) }}">
                        <span><span class="font-semibold">Balas {{ $task->nama }}</span><span class="mt-1 block text-xs text-copy-muted">{{ $task->nomor_konsultasi }}</span></span>
                        <span aria-hidden="true">→</span>
                    </a>
                @empty
                    <p class="py-5 text-sm text-copy">Tidak ada tugas hari ini.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-3xl border border-line p-6" aria-labelledby="projects-title">
            <div class="flex items-center justify-between gap-4">
                <h2 id="projects-title" class="text-xl font-bold">Proyek 7 hari ke depan</h2>
                <a class="text-sm font-semibold text-brand hover:underline" href="{{ route('admin.projects.index') }}">Semua proyek</a>
            </div>
            <div class="mt-4 divide-y divide-line">
                @forelse ($upcomingProjects as $project)
                    <div class="flex items-center justify-between gap-4 py-4">
                        <div>
                            <p class="font-semibold">{{ $project->nama }}</p>
                            <p class="mt-1 text-sm text-copy">{{ $project->tgl_mulai->translatedFormat('d M Y') }}</p>
                        </div>
                        @php
                            $projectStatusClass = match ($project->status) {
                                'terjadwal' => 'bg-status-info-bg text-status-info-fg',
                                'berjalan' => 'bg-status-warn-bg text-status-warn-fg',
                                'selesai' => 'bg-status-success-bg text-status-success-fg',
                                default => 'bg-status-neutral-bg text-status-neutral-fg',
                            };
                        @endphp
                        <span class="rounded-full px-3 py-1 text-xs font-medium {{ $projectStatusClass }}">{{ ucfirst($project->status) }}</span>
                    </div>
                @empty
                    <p class="py-5 text-sm text-copy">Belum ada proyek yang dimulai dalam 7 hari.</p>
                @endforelse
            </div>
        </section>
    </div>

    <section class="mt-6 rounded-3xl border border-line p-6" aria-labelledby="recent-consultations-title">
        <div class="flex items-center justify-between gap-4">
            <h2 id="recent-consultations-title" class="text-xl font-bold">Konsultasi terbaru</h2>
            <a class="text-sm font-semibold text-brand hover:underline" href="{{ route('admin.consultations.index') }}">Semua konsultasi</a>
        </div>
        <div class="mt-4 divide-y divide-line">
            @forelse ($recentConsultations as $consultation)
                <a class="flex flex-wrap items-center justify-between gap-3 py-4 hover:text-brand" href="{{ route('admin.consultations.show', $consultation) }}">
                    <span>
                        <span class="font-semibold">{{ $consultation->nama }}</span>
                        <span class="ml-2 text-sm text-copy">{{ $consultation->nomor_konsultasi }} · {{ $consultation->layanan }}</span>
                    </span>
                    <x-consultation-status-badge :status="$consultation->status" />
                </a>
            @empty
                <p class="py-5 text-sm text-copy">Belum ada konsultasi.</p>
            @endforelse
        </div>
    </section>
@endsection
