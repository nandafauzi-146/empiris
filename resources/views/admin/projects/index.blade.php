@extends('layouts.admin')

@section('title', 'Proyek & Keuangan — Empirise Jalan-Jalan')
@section('content')
    <header class="mb-8">
        <p class="text-xs font-bold tracking-[0.18em] text-brand">OPERASIONAL</p>
        <h1 class="mt-2 text-3xl font-bold sm:text-4xl">Proyek &amp; Keuangan</h1>
        <p class="mt-2 text-copy">Buat proyek dari konsultasi klien dan catat pembayaran termin.</p>
    </header>

    <details class="mb-8 rounded-3xl border border-line p-5" open>
        <summary class="cursor-pointer text-lg font-bold">Buat proyek dan termin pembayaran</summary>
        <form class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3" action="{{ route('admin.projects.store') }}" method="POST">
            @csrf
            <label class="text-sm font-semibold">Konsultasi klien
                <select class="mt-2 w-full rounded-xl border-line" name="id_konsultasi" required>
                    <option value="">Pilih konsultasi</option>
                    @foreach ($consultations as $consultation)
                        <option value="{{ $consultation->id_konsultasi }}" @selected(old('id_konsultasi') == $consultation->id_konsultasi)>
                            {{ $consultation->nomor_konsultasi }} — {{ $consultation->nama }} ({{ $consultation->layanan }})
                        </option>
                    @endforeach
                </select>
            </label>
            <label class="text-sm font-semibold">Nama proyek
                <input class="mt-2 w-full rounded-xl border-line" name="nama" maxlength="190" value="{{ old('nama') }}" required>
            </label>
            <label class="text-sm font-semibold">Total nilai proyek (Rp)
                <input class="mt-2 w-full rounded-xl border-line" type="number" min="1" step="1" name="total_nilai" value="{{ old('total_nilai') }}" required>
            </label>
            <label class="text-sm font-semibold">Tanggal mulai
                <input class="mt-2 w-full rounded-xl border-line" type="date" name="tgl_mulai" value="{{ old('tgl_mulai') }}" required>
            </label>
            <label class="text-sm font-semibold">Tanggal selesai
                <input class="mt-2 w-full rounded-xl border-line" type="date" name="tgl_selesai" value="{{ old('tgl_selesai') }}" required>
            </label>
            <label class="text-sm font-semibold">Catatan
                <input class="mt-2 w-full rounded-xl border-line" name="catatan" maxlength="5000" value="{{ old('catatan') }}">
            </label>
            @foreach (['Termin 1 · 30%', 'Termin 2 · 40%', 'Termin 3 · 30%'] as $index => $label)
                <label class="text-sm font-semibold">{{ $label }} · Jatuh tempo
                    <input class="mt-2 w-full rounded-xl border-line" type="date" name="jatuh_tempo[{{ $index }}]" value="{{ old("jatuh_tempo.$index") }}">
                </label>
            @endforeach
            <div class="sm:col-span-2 xl:col-span-3">
                <button class="rounded-full bg-brand px-6 py-3 text-sm font-bold text-white hover:bg-red-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand" type="submit">Buat proyek</button>
            </div>
        </form>
        <p class="mt-3 text-xs text-copy">Termin dibuat otomatis dengan komposisi 30% / 40% / 30%; nominal terakhir menutup sisa pembulatan.</p>
    </details>

    <section class="space-y-5" aria-label="Daftar proyek">
        @forelse ($projects as $project)
            <article class="rounded-3xl border border-line p-5 sm:p-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                    <div>
                        <p class="text-xs font-bold tracking-wide text-copy">{{ $project->nomor_proyek }}</p>
                        <h2 class="mt-1 text-xl font-bold">{{ $project->nama }}</h2>
                        <p class="mt-1 text-sm text-copy">{{ $project->client?->name }} · {{ $project->tgl_mulai->translatedFormat('d M Y') }} – {{ $project->tgl_selesai->translatedFormat('d M Y') }}</p>
                        <p class="mt-2 font-semibold">Rp {{ number_format($project->total_nilai, 0, ',', '.') }}</p>
                    </div>
                    <form class="flex items-center gap-2" action="{{ route('admin.projects.status', $project) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <label class="sr-only" for="status-proyek-{{ $project->id_proyek }}">Status proyek</label>
                        <select class="rounded-full border-line text-sm" id="status-proyek-{{ $project->id_proyek }}" name="status">
                            @foreach (['terjadwal' => 'Terjadwal', 'berjalan' => 'Berjalan', 'selesai' => 'Selesai', 'batal' => 'Batal'] as $value => $label)
                                <option value="{{ $value }}" @selected($project->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="rounded-full border border-line px-4 py-2 text-sm font-semibold hover:border-brand hover:text-brand" type="submit">Simpan</button>
                    </form>
                </div>
                <div class="mt-5 grid gap-3 lg:grid-cols-3">
                    @foreach ($project->terms as $term)
                        <div class="rounded-2xl bg-gray-50 p-4">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="font-semibold">{{ $term->nama }}</p>
                                    <p class="mt-1 text-sm">Rp {{ number_format($term->nominal, 0, ',', '.') }}</p>
                                    <p class="mt-1 text-xs text-copy">Jatuh tempo {{ $term->jatuh_tempo->translatedFormat('d M Y') }}</p>
                                </div>
                                <span class="rounded-full {{ $term->status === 'lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} px-3 py-1 text-xs font-semibold">{{ $term->status === 'lunas' ? 'Lunas' : 'Belum bayar' }}</span>
                            </div>
                            @if ($term->status !== 'lunas')
                                <form class="mt-4 flex flex-wrap items-end gap-2" action="{{ route('admin.projects.payments.store', $term) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <label class="min-w-36 flex-1 text-xs font-semibold">Tanggal bayar
                                        <input class="mt-1 w-full rounded-lg border-line text-sm" type="date" name="tgl_bayar" required>
                                    </label>
                                    <label class="min-w-36 flex-1 text-xs font-semibold">Bukti (opsional)
                                        <input class="mt-1 block w-full text-xs" type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf">
                                    </label>
                                    <button class="rounded-full bg-ink px-4 py-2 text-xs font-semibold text-white hover:bg-brand" type="submit">Catat lunas</button>
                                </form>
                            @else
                                <p class="mt-3 text-xs text-copy">Dibayar {{ $term->tgl_bayar?->translatedFormat('d M Y') }} @if($term->bukti_path) · Bukti tersimpan @endif</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </article>
        @empty
            <div class="rounded-3xl border border-dashed border-line p-10 text-center">
                <h2 class="text-xl font-bold">Belum ada proyek</h2>
                <p class="mt-2 text-sm text-copy">Proyek akan muncul setelah admin membuatnya dari konsultasi klien.</p>
            </div>
        @endforelse
        {{ $projects->links() }}
    </section>
@endsection
