@extends('layouts.admin')

@section('title', 'Pusat laporan — Empirise Jalan-Jalan')
@section('content')
    <header class="mb-8">
        <p class="text-xs font-bold tracking-[0.18em] text-brand">ANALITIK</p>
        <h1 class="mt-2 text-3xl font-bold sm:text-4xl">Pusat laporan</h1>
        <p class="mt-2 text-copy">Pratinjau dan ekspor rekap konsultasi, proyek, serta pembayaran.</p>
    </header>
    <form class="mb-8 flex flex-wrap items-end gap-3 rounded-3xl bg-gray-50 p-5" action="{{ route('admin.reports.index') }}" method="GET">
        <label class="text-sm font-semibold">Dari<input class="mt-1 block rounded-xl border-line" type="date" name="from" value="{{ $from }}"></label>
        <label class="text-sm font-semibold">Sampai<input class="mt-1 block rounded-xl border-line" type="date" name="to" value="{{ $to }}"></label>
        <button class="rounded-full bg-ink px-5 py-2.5 text-sm font-bold text-white" type="submit">Terapkan</button>
    </form>

    @foreach (['konsultasi' => 'Rekap konsultasi', 'proyek' => 'Rekap proyek', 'pembayaran' => 'Rekap pembayaran'] as $key => $title)
        @php $rows = $reports[$key]; @endphp
        <section class="mb-6 rounded-3xl border border-line p-5" aria-labelledby="report-{{ $key }}">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div><h2 id="report-{{ $key }}" class="text-xl font-bold">{{ $title }}</h2><p class="mt-1 text-sm text-copy">{{ count($rows) - 1 }} baris · {{ $from }} sampai {{ $to }}</p></div>
                <div class="flex gap-2">
                    @foreach (['csv' => 'CSV', 'xlsx' => 'Excel'] as $format => $label)
                        <a class="rounded-full {{ $format === 'xlsx' ? 'bg-ink text-white' : 'border border-line' }} px-4 py-2 text-sm font-semibold" href="{{ route('admin.reports.export', ['report' => $key, 'format' => $format, 'from' => $from, 'to' => $to]) }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-copy"><tr>@foreach ($rows[0] as $heading)<th class="px-4 py-3">{{ $heading }}</th>@endforeach</tr></thead>
                    <tbody class="divide-y divide-line">
                        @forelse (array_slice($rows, 1, 5) as $row)
                            <tr>@foreach ($row as $cell)<td class="px-4 py-3">{{ is_int($cell) && $key !== 'konsultasi' ? number_format($cell, 0, ',', '.') : $cell }}</td>@endforeach</tr>
                        @empty
                            <tr><td class="px-4 py-5 text-copy" colspan="{{ count($rows[0]) }}">Tidak ada data pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endforeach
@endsection
