@extends('layouts.admin')

@section('title', 'Kalender kapasitas — Empirise Jalan-Jalan')
@section('content')
    <header class="mb-8">
        <p class="text-xs font-bold tracking-[0.18em] text-brand">OPERASIONAL</p>
        <h1 class="mt-2 text-3xl font-bold sm:text-4xl">Kalender</h1>
        <p class="mt-2 text-copy">Kelola jadwal proyek dan penanda kapasitas tim atau armada.</p>
    </header>

    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <a class="rounded-full border border-line px-4 py-2 text-sm font-semibold" href="{{ route('admin.calendar.index', ['month' => $month->subMonth()->format('Y-m')]) }}" aria-label="Bulan sebelumnya">←</a>
        <h2 class="text-xl font-bold">{{ $month->translatedFormat('F Y') }}</h2>
        <div class="flex gap-2">
            <a class="rounded-full border border-line px-4 py-2 text-sm font-semibold" href="{{ route('admin.calendar.index', ['month' => now()->format('Y-m')]) }}">Hari ini</a>
            <a class="rounded-full border border-line px-4 py-2 text-sm font-semibold" href="{{ route('admin.calendar.index', ['month' => $month->addMonth()->format('Y-m')]) }}" aria-label="Bulan berikutnya">→</a>
        </div>
    </div>

    <div class="admin-calendar-scroll overflow-x-auto pb-2">
    <div class="grid min-w-[680px] grid-cols-7 gap-1 sm:gap-2">
        @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $weekday)
            <div class="py-2 text-center text-xs font-bold text-copy">{{ $weekday }}</div>
        @endforeach
        @for ($blank = 0; $blank < $offset; $blank++)
            <div aria-hidden="true"></div>
        @endfor
        @for ($day = 1; $day <= $days; $day++)
            @php
                $dateKey = $month->addDays($day - 1)->toDateString();
                $status = $statuses[$dateKey] ?? 'tersedia';
                $dayProjects = $projects->filter(fn ($project) => $project->tgl_mulai->toDateString() <= $dateKey && $project->tgl_selesai->toDateString() >= $dateKey);
                $dayMarkers = $markers->get($dateKey, collect());
            @endphp
            <details class="min-h-24 rounded-2xl border border-line bg-white p-2 sm:min-h-32 sm:p-3">
                <summary class="cursor-pointer list-none">
                    <div class="flex items-center justify-between gap-1">
                        <span class="font-semibold">{{ $day }}</span>
                        <span class="size-2 rounded-full {{ ['penuh' => 'bg-red-500', 'terbatas' => 'bg-amber-400', 'tersedia' => 'bg-green-500'][$status] }}" aria-label="{{ ucfirst($status) }}"></span>
                    </div>
                    <p class="mt-1 text-[10px] font-semibold text-copy sm:text-xs">{{ ucfirst($status) }}</p>
                    @if ($dayProjects->isNotEmpty())
                        <p class="mt-2 hidden truncate text-xs text-copy sm:block">{{ $dayProjects->first()->nama }}{{ $dayProjects->count() > 1 ? ' +' . ($dayProjects->count() - 1) : '' }}</p>
                    @endif
                </summary>
                <div class="mt-3 space-y-2 border-t border-line pt-2">
                    @foreach ($dayProjects as $project)
                        <p class="text-[10px] font-medium leading-tight sm:text-xs">{{ $project->nama }} <span class="block text-copy">{{ $project->client?->name }}</span></p>
                    @endforeach
                    @foreach (['tim' => 'Tim penuh', 'armada' => 'Armada penuh'] as $kind => $label)
                        <form action="{{ route('admin.calendar.markers.toggle') }}" method="POST">
                            @csrf
                            <input type="hidden" name="tanggal" value="{{ $dateKey }}">
                            <input type="hidden" name="jenis" value="{{ $kind }}">
                            <input type="hidden" name="month" value="{{ $month->format('Y-m') }}">
                            <button class="w-full rounded-lg px-2 py-1 text-left text-[10px] {{ $dayMarkers->contains('jenis', $kind) ? 'bg-red-100 font-bold text-red-800' : 'bg-gray-50 text-copy' }}" type="submit" aria-pressed="{{ $dayMarkers->contains('jenis', $kind) ? 'true' : 'false' }}">{{ $dayMarkers->contains('jenis', $kind) ? '✓ ' : '+ ' }}{{ $label }}</button>
                        </form>
                    @endforeach
                </div>
            </details>
        @endfor
    </div>
    </div>
    <div class="mt-5 flex flex-wrap gap-4 text-xs text-copy">
        <span><span class="mr-1 inline-block size-2 rounded-full bg-green-500"></span>Tersedia</span>
        <span><span class="mr-1 inline-block size-2 rounded-full bg-amber-400"></span>Terbatas (ada proyek)</span>
        <span><span class="mr-1 inline-block size-2 rounded-full bg-red-500"></span>Penuh (penanda kapasitas)</span>
    </div>
@endsection
