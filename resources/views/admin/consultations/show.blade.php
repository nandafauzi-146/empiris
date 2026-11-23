@extends('layouts.admin')

@section('title', 'Detail konsultasi — Admin Empirise')
@section('meta_description', 'Tinjau dan balas konsultasi klien Empirise.')

@section('content')
    <div class="public-page">
        <section class="page-intro page-shell">
            <p class="section-eyebrow">ADMIN · {{ $consultation->nomor_konsultasi }}</p>
            <h1>{{ $consultation->nama }}</h1>
            <p class="flex flex-wrap items-center gap-2">
                {{ $consultation->layanan }} · Status <x-consultation-status-badge :status="$consultation->status" />
            </p>
        </section>

        <section class="content-section content-section-tint">
            <div class="page-shell account-content">
                @if (session('status'))
                    <p class="account-status" role="status">{{ session('status') }}</p>
                @endif
                <article class="account-detail-card">
                    <h2>Ringkasan kebutuhan</h2>
                    <dl class="account-detail-list">
                        <div><dt>Email</dt><dd>{{ $consultation->email }}</dd></div>
                        <div><dt>Telepon</dt><dd>{{ $consultation->telepon }}</dd></div>
                        <div><dt>Instansi</dt><dd>{{ $consultation->instansi ?: 'Tidak dicantumkan' }}</dd></div>
                        <div><dt>Destinasi</dt><dd>{{ $consultation->tujuan ?: 'Belum ditentukan' }}</dd></div>
                        <div><dt>Jumlah peserta</dt><dd>{{ $consultation->jumlah_peserta ?: 'Belum ditentukan' }}</dd></div>
                        <div><dt>Durasi</dt><dd>{{ $consultation->durasi_hari ? $consultation->durasi_hari.' hari' : 'Belum ditentukan' }}</dd></div>
                        <div><dt>Tanggal rencana</dt><dd>{{ $consultation->tanggal_rencana?->format('d/m/Y') ?: 'Belum ditentukan' }}</dd></div>
                        @if ($consultation->layanan === 'Rental Armada')
                            <div><dt>Jenis kendaraan yang diminta</dt><dd>{{ $consultation->jenis_kendaraan ?: 'Belum ditentukan' }}</dd></div>
                            <div><dt>Lokasi penjemputan</dt><dd>{{ $consultation->lokasi_jemput ?: 'Belum ditentukan' }}</dd></div>
                            <div><dt>Jam penjemputan</dt><dd>{{ $consultation->jam_jemput ? substr($consultation->jam_jemput, 0, 5) : 'Belum ditentukan' }}</dd></div>
                            <div><dt>Tanggal kembali</dt><dd>{{ $consultation->tanggal_kembali?->format('d/m/Y') ?: 'Belum ditentukan' }}</dd></div>
                            <div><dt>Status ketersediaan</dt><dd>Perlu dikonfirmasi admin; permintaan ini bukan reservasi terkonfirmasi.</dd></div>
                        @endif
                        <div><dt>Minat</dt><dd>{{ $consultation->minat ?: 'Tidak dicantumkan' }}</dd></div>
                        <div><dt>Catatan kebutuhan</dt><dd>{{ $consultation->cerita_kebutuhan ?: 'Tidak dicantumkan' }}</dd></div>
                    </dl>
                </article>

                <section class="account-detail-card" aria-labelledby="admin-replies-title">
                    <h2 id="admin-replies-title">Percakapan</h2>
                    @forelse ($replies as $reply)
                        <article class="consultation-reply">
                            <header>
                                <strong>{{ $reply->sender?->role === \App\Enums\UserRole::Admin ? 'Tim Empirise' : ($reply->sender?->name ?? 'Pengguna') }}</strong>
                                <time datetime="{{ $reply->created_at->toISOString() }}">{{ $reply->created_at->format('d/m/Y H:i') }}</time>
                            </header>
                            <p>{{ $reply->pesan }}</p>
                        </article>
                    @empty
                        <p>Belum ada pesan dalam percakapan ini.</p>
                    @endforelse
                    {{ $replies->links() }}

                    <form class="reply-form" action="{{ route('admin.consultations.replies.store', $consultation) }}" method="POST">
                        @csrf
                        <div class="form-field">
                            <label for="consultation-reply">Balas klien</label>
                            <textarea id="consultation-reply" name="pesan" rows="4" maxlength="2000" required aria-invalid="{{ $errors->has('pesan') ? 'true' : 'false' }}">{{ old('pesan') }}</textarea>
                            @error('pesan')
                                <p class="form-error" id="consultation-reply-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <button class="button-primary" type="submit">Kirim balasan</button>
                    </form>
                </section>

                <section class="account-detail-card" aria-labelledby="admin-offers-title">
                    <h2 id="admin-offers-title">Penawaran</h2>

                    @foreach ($offers as $offer)
                        <article class="mb-5 rounded-2xl border border-line p-4">
                            <header class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <strong>Versi {{ $offer->versi }}</strong>
                                    <span class="ml-2 text-sm text-copy">{{ $offer->status->label() }}</span>
                                </div>
                                @if ($offer->status === \App\Enums\PenawaranStatus::Draft)
                                    <form action="{{ route('admin.consultations.offers.send', [$consultation, $offer]) }}" method="POST">
                                        @csrf
                                        <button class="button-primary" type="submit">Kirim penawaran</button>
                                    </form>
                                @endif
                            </header>
                            <dl class="account-detail-list mt-4">
                                @foreach ($offer->details as $detail)
                                    <div>
                                        <dt>{{ $detail->nama_item }}</dt>
                                        <dd>
                                            {{ number_format((float) $detail->jumlah, 0, ',', '.') }} {{ $detail->satuan }}
                                            @if ($detail->harga_satuan !== null)
                                                · Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}
                                            @else
                                                · Harga belum diisi
                                            @endif
                                        </dd>
                                    </div>
                                @endforeach
                            </dl>
                        </article>
                    @endforeach

                    @if ($consultation->id_klien)
                        <form class="reply-form" action="{{ route('admin.consultations.offers.store', $consultation) }}" method="POST">
                            @csrf
                            <div class="form-field">
                                <label for="offer-note">Catatan admin</label>
                                <textarea id="offer-note" name="catatan_admin" rows="3" maxlength="5000">{{ old('catatan_admin') }}</textarea>
                            </div>
                            <div class="grid gap-3 md:grid-cols-4">
                                <div class="form-field md:col-span-2">
                                    <label for="offer-item-name">Nama komponen</label>
                                    <input id="offer-item-name" name="items[0][nama_item]" value="{{ old('items.0.nama_item') }}" maxlength="190" required>
                                </div>
                                <div class="form-field">
                                    <label for="offer-item-quantity">Jumlah</label>
                                    <input id="offer-item-quantity" name="items[0][jumlah]" type="number" min="0.01" step="0.01" value="{{ old('items.0.jumlah', 1) }}" required>
                                </div>
                                <div class="form-field">
                                    <label for="offer-item-unit">Satuan</label>
                                    <input id="offer-item-unit" name="items[0][satuan]" value="{{ old('items.0.satuan', 'paket') }}" maxlength="30" required>
                                </div>
                            </div>
                            <div class="form-field">
                                <label for="offer-item-price">Harga satuan (Rp)</label>
                                <input id="offer-item-price" name="items[0][harga_satuan]" type="number" min="0" value="{{ old('items.0.harga_satuan') }}" placeholder="Kosongkan bila belum disepakati">
                            </div>
                            <button class="button-primary" type="submit">Buat draf penawaran</button>
                        </form>
                    @else
                        <p class="text-copy">Tautkan konsultasi ke akun klien sebelum membuat penawaran.</p>
                    @endif
                </section>

                <div>
                    <a class="account-detail-link" href="{{ route('admin.consultations.index') }}">← Kembali ke daftar konsultasi</a>
                </div>
            </div>
        </section>
    </div>
@endsection
