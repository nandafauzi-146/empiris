@extends('layouts.admin')

@section('title', 'Konten Web — Empirise Jalan-Jalan')
@section('content')
    <header class="mb-8">
        <p class="text-xs font-bold tracking-[0.18em] text-brand">SITUS WEB</p>
        <h1 class="mt-2 text-3xl font-bold sm:text-4xl">Konten Web</h1>
        <p class="mt-2 text-copy">Kelola konten yang tampil di situs. Item yang dinonaktifkan tetap tersimpan.</p>
    </header>

    <p class="mb-6 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">
        Konten awal yang diberi label <strong>CONTOH</strong> adalah data tampilan awal. Periksa dan sesuaikan sebelum digunakan sebagai informasi resmi.
    </p>

    <nav class="mb-6 flex flex-wrap gap-2" aria-label="Kategori konten">
        @foreach ($typeLinks as $typeLink)
            <a class="rounded-full border px-4 py-2 text-sm font-semibold {{ $typeLink->className }}" href="{{ $typeLink->url }}" @if($typeLink->isCurrent) aria-current="page" @endif>{{ $typeLink->label }}</a>
        @endforeach
    </nav>

    <details class="mb-6 rounded-3xl border border-line p-5">
        <summary class="cursor-pointer font-bold">+ Tambah {{ $typeLabel }}</summary>
        @if (session('errors'))
            <p class="mt-3 text-sm font-semibold text-red-700">{{ $errors->first() }}</p>
        @endif
        <div class="mt-3 rounded-2xl bg-neutral-50 p-4 text-sm text-copy">
            @switch($type)
                @case('layanan')
                    Maksimal 5 layanan tampil. Gunakan slug yang sudah dikenal: <strong>tour-wisata</strong>, <strong>event-gathering</strong>, <strong>outbound</strong>, <strong>rental-armada</strong>, atau <strong>team-building</strong>.
                    @break
                @case('destinasi')
                    Tambahkan nama, wilayah, kategori, deskripsi, dan gambar. Maksimal 30 destinasi tampil agar daftar dan filter tetap teratur.
                    @break
                @case('portofolio')
                    Maksimal 6 portofolio tampil. Isi lokasi, kategori, tahun, dan slug halaman pada metadata.
                    @break
                @case('klien')
                    Maksimal 40 nama klien tampil di seluruh bagian situs. Metadata <code>bagian</code> membedakan <code>beranda</code> dan <code>tentang</code>.
                    @break
                @case('testimoni')
                    Maksimal 6 testimoni tampil di seluruh bagian situs. Deskripsi adalah kutipan; metadata <code>bagian</code> membedakan <code>beranda</code> dan <code>tentang</code>.
                    @break
                @default
                    Gunakan urutan dari angka terkecil untuk menentukan posisi konten.
            @endswitch
        </div>
        <form class="mt-4 grid gap-3 sm:grid-cols-2" action="{{ route('admin.content.store') }}" method="POST" enctype="multipart/form-data" data-content-preview-form>
            @csrf
            <input type="hidden" name="jenis" value="{{ $type }}">
            <label class="text-sm font-semibold">Judul
                <input class="mt-1 w-full rounded-xl border-line" name="judul" data-preview-title maxlength="190" required>
            </label>
            <label class="text-sm font-semibold">Slug (opsional)
                <input class="mt-1 w-full rounded-xl border-line" name="slug" maxlength="190">
            </label>
            <label class="text-sm font-semibold sm:col-span-2">Deskripsi
                <textarea class="mt-1 w-full rounded-xl border-line" name="deskripsi" data-preview-description rows="3" maxlength="10000"></textarea>
            </label>
            @if ($type === 'layanan')
                <label class="text-sm font-semibold sm:col-span-2">Ikon layanan
                    <select class="mt-1 w-full rounded-xl border-line" name="icon" data-preview-icon-input>
                        <option value="map">Peta</option>
                        <option value="calendar">Kalendar</option>
                        <option value="gamepad">Outbound / permainan</option>
                        <option value="bus">Armada</option>
                        <option value="users">Tim / team building</option>
                    </select>
                </label>
            @endif
            @if ($supportsImages)
                <label class="text-sm font-semibold sm:col-span-2">Gambar
                    <input class="mt-1 w-full rounded-xl border-line" type="file" name="gambar" accept="image/jpeg,image/png,image/webp"
                        data-preview-image-input>
                    <span class="mt-1 block text-xs font-normal text-copy">JPG, PNG, atau WebP; maksimal 4 MB.</span>
                </label>
            @endif
            <label class="text-sm font-semibold sm:col-span-2">Metadata JSON (opsional)
                @switch($type)
                    @case('destinasi')
                        <textarea class="mt-1 w-full rounded-xl border-line font-mono text-xs" name="metadata_json" data-preview-metadata rows="4" placeholder='{"wilayah":"Bali","kategori":"Alam"}'></textarea>
                        @break
                    @case('portofolio')
                        <textarea class="mt-1 w-full rounded-xl border-line font-mono text-xs" name="metadata_json" data-preview-metadata rows="4" placeholder='{"lokasi":"Bandung","kategori":"Instansi","tahun":"2026"}'></textarea>
                        @break
                    @case('testimoni')
                        <textarea class="mt-1 w-full rounded-xl border-line font-mono text-xs" name="metadata_json" data-preview-metadata rows="4" placeholder='{"penulis":"HR Manager"}'></textarea>
                        @break
                    @default
                        <textarea class="mt-1 w-full rounded-xl border-line font-mono text-xs" name="metadata_json" data-preview-metadata rows="4" placeholder='{"icon":"map"}'></textarea>
                @endswitch
            </label>
            <label class="text-sm font-semibold">Urutan
                <input class="mt-1 w-full rounded-xl border-line" type="number" name="urutan" min="0" value="0" required>
            </label>
            <label class="text-sm font-semibold">Status
                <select class="mt-1 w-full rounded-xl border-line" name="status"><option value="tampil">Tampil</option><option value="draf">Draf</option><option value="tersembunyi">Tersembunyi</option></select>
            </label>
            <label class="flex items-center gap-2 text-sm font-semibold"><input type="checkbox" name="aktif" value="1" checked> Aktif</label>
            @include('admin.content.preview', ['contentType' => $type, 'content' => null])
            <div class="sm:col-span-2"><button class="rounded-full bg-brand px-5 py-2.5 text-sm font-bold text-white" type="submit">Simpan konten</button></div>
        </form>
    </details>

    <div class="space-y-4">
        @forelse ($contents as $content)
            <article class="rounded-3xl border border-line p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex min-w-0 items-start gap-4">
                        @php($contentImage = $content->metadata['image'] ?? $content->metadata['gambar'] ?? null)
                        @if (is_string($contentImage) && $contentImage !== '')
                            <img class="size-20 shrink-0 rounded-xl object-cover" src="{{ str_starts_with($contentImage, 'http') ? $contentImage : asset($contentImage) }}" alt="Gambar {{ $content->judul }}" loading="lazy">
                        @endif
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="font-bold">{{ $content->judul }}</h2>
                                @if ($content->metadata['contoh'] ?? false)
                                    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-900">CONTOH</span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-copy">{{ $content->slug ?: 'Tanpa slug' }} · Urutan {{ $content->urutan }} · {{ $content->aktif ? 'Aktif' : 'Nonaktif' }} · {{ ucfirst($content->status) }}</p>
                            @foreach (['wilayah', 'kategori', 'lokasi', 'tahun', 'penulis', 'icon'] as $metadataKey)
                                @if (is_scalar(data_get($content->metadata, $metadataKey)))
                                    <p class="mt-1 text-xs text-copy">{{ ucfirst($metadataKey) }}: {{ data_get($content->metadata, $metadataKey) }}</p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <button class="rounded-full border border-line px-4 py-2 text-sm font-semibold" type="button" data-content-edit-toggle aria-expanded="false">Edit</button>
                </div>
                <p class="mt-3 whitespace-pre-line text-sm text-copy">{{ $content->deskripsi }}</p>
                <form class="mt-3" action="{{ route('admin.content.destroy', $content) }}" method="POST" onsubmit="return confirm('Nonaktifkan konten ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="text-sm font-semibold text-red-700 underline" type="submit">Nonaktifkan</button>
                </form>
                <div class="mt-4 hidden border-t border-line pt-4" data-content-edit-panel>
                <form class="grid gap-3 sm:grid-cols-2" action="{{ route('admin.content.update', $content) }}" method="POST" enctype="multipart/form-data" data-content-preview-form>
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="jenis" value="{{ $content->jenis }}">
                    <label class="text-sm font-semibold">Judul<input class="mt-1 w-full rounded-xl border-line" name="judul" data-preview-title value="{{ $content->judul }}" required></label>
                    <label class="text-sm font-semibold">Slug<input class="mt-1 w-full rounded-xl border-line" name="slug" value="{{ $content->slug }}"></label>
                    <label class="text-sm font-semibold sm:col-span-2">Deskripsi<textarea class="mt-1 w-full rounded-xl border-line" name="deskripsi" data-preview-description rows="3">{{ $content->deskripsi }}</textarea></label>
                    @if ($content->jenis === 'layanan')
                        <label class="text-sm font-semibold sm:col-span-2">Ikon layanan
                            <select class="mt-1 w-full rounded-xl border-line" name="icon" data-preview-icon-input>
                                <option value="map" @selected(($content->metadata['icon'] ?? 'map') === 'map')>Peta</option>
                                <option value="calendar" @selected(($content->metadata['icon'] ?? 'map') === 'calendar')>Kalendar</option>
                                <option value="gamepad" @selected(($content->metadata['icon'] ?? 'map') === 'gamepad')>Outbound / permainan</option>
                                <option value="bus" @selected(($content->metadata['icon'] ?? 'map') === 'bus')>Armada</option>
                                <option value="users" @selected(($content->metadata['icon'] ?? 'map') === 'users')>Tim / team building</option>
                            </select>
                        </label>
                    @endif
                    @if ($content->jenis === 'destinasi' || $content->jenis === 'portofolio' || $content->jenis === 'layanan')
                        <label class="text-sm font-semibold sm:col-span-2">Ganti gambar
                            <input class="mt-1 w-full rounded-xl border-line" type="file" name="gambar" accept="image/jpeg,image/png,image/webp"
                                data-preview-image-input>
                        </label>
                    @endif
                    <label class="text-sm font-semibold sm:col-span-2">Metadata JSON (opsional)<textarea class="mt-1 w-full rounded-xl border-line font-mono text-xs" name="metadata_json" data-preview-metadata rows="4">{{ json_encode($content->metadata ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</textarea></label>
                    <label class="text-sm font-semibold">Urutan<input class="mt-1 w-full rounded-xl border-line" type="number" name="urutan" min="0" value="{{ $content->urutan }}" required></label>
                    <label class="text-sm font-semibold">Status<select class="mt-1 w-full rounded-xl border-line" name="status"><option value="tampil" @selected($content->status === 'tampil')>Tampil</option><option value="draf" @selected($content->status === 'draf')>Draf</option><option value="tersembunyi" @selected($content->status === 'tersembunyi')>Tersembunyi</option></select></label>
                    <label class="flex items-center gap-2 text-sm font-semibold"><input type="checkbox" name="aktif" value="1" @checked($content->aktif)> Aktif</label>
                    @include('admin.content.preview', ['contentType' => $content->jenis, 'content' => $content, 'previewNumber' => $loop->iteration])
                    <div class="sm:col-span-2"><button class="rounded-full bg-ink px-5 py-2.5 text-sm font-bold text-white" type="submit">Simpan perubahan</button></div>
                </form>
                </div>
            </article>
        @empty
            <p class="rounded-3xl border border-dashed border-line p-8 text-center text-copy">Belum ada konten pada kategori {{ $typeLabel }}.</p>
        @endforelse
        {{ $contents->links() }}
    </div>
@endsection
