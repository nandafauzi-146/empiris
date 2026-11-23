@extends('layouts.public')

@section('title', 'Kalender Agenda — Empirise Jalan-Jalan')
@section('meta_description', 'Lihat status ketersediaan agenda kegiatan Empirise Jalan-Jalan.')

@section('content')
    <div class="public-page">
        <section class="page-intro page-shell">
            <p class="section-eyebrow">KALENDER AGENDA</p>
            <h1>Rencanakan tanggal kegiatan Anda.</h1>
            <p>Status kalender adalah gambaran awal dan bukan konfirmasi ketersediaan. Tim kami akan membantu mengecek kebutuhan Anda.</p>
        </section>

        <section class="content-section page-shell" aria-labelledby="calendar-month">
            <div class="mx-auto max-w-4xl rounded-3xl border border-line bg-white p-5 shadow-sm sm:p-8">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <a class="rounded-full border border-line px-4 py-2 text-sm font-semibold hover:border-brand hover:text-brand" href="{{ route('calendar.public', ['month' => $previousMonth]) }}" aria-label="Bulan sebelumnya">Sebelumnya</a>
                    <h2 id="calendar-month" class="text-center text-xl font-bold sm:text-2xl">{{ $month->translatedFormat('F Y') }}</h2>
                    <a class="rounded-full border border-line px-4 py-2 text-sm font-semibold hover:border-brand hover:text-brand" href="{{ route('calendar.public', ['month' => $nextMonth]) }}" aria-label="Bulan berikutnya">Berikutnya</a>
                </div>

                <div class="grid grid-cols-7 gap-2 text-center text-xs font-semibold text-copy sm:gap-3 sm:text-sm" aria-hidden="true">
                    @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $weekday)
                        <span class="py-2">{{ $weekday }}</span>
                    @endforeach
                </div>
                <div class="grid grid-cols-7 gap-2 sm:gap-3" role="list" aria-label="Status tanggal">
                    @for ($blank = 0; $blank < $offset; $blank++)
                        <span aria-hidden="true"></span>
                    @endfor
                    @foreach ($statuses as $date => $status)
                        @php
                            $day = (int) substr($date, 8, 2);
                            $statusLabel = match ($status) {
                                'penuh' => 'Penuh',
                                'terbatas' => 'Terbatas',
                                default => 'Tersedia',
                            };
                            $statusClass = match ($status) {
                                'penuh' => 'bg-red-100 text-red-800',
                                'terbatas' => 'bg-amber-100 text-amber-900',
                                default => 'bg-green-100 text-green-800',
                            };
                        @endphp
                        <div class="flex min-h-16 flex-col items-center justify-center rounded-xl {{ $statusClass }} sm:min-h-20" role="listitem" aria-label="{{ $day }} {{ $month->translatedFormat('F') }}: {{ $statusLabel }}">
                            <span class="font-bold">{{ $day }}</span>
                            <span class="mt-1 text-[10px] sm:text-xs">{{ $statusLabel }}</span>
                        </div>
                    @endforeach
                </div>

                <p class="mt-6 text-sm text-copy">Kalender tidak menampilkan nama klien. Silakan <a class="font-semibold text-brand underline" href="{{ route('consultation.create') }}">konsultasikan tanggal dan kebutuhan kegiatan</a> Anda.</p>
            </div>
        </section>
    </div>
@endsection
