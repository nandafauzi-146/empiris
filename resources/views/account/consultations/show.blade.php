@extends('layouts.account')

@section('title', 'Detail konsultasi — Empirise Jalan-Jalan')
@section('meta_description', 'Detail konsultasi Anda bersama Empirise.')

@section('account-content')
    <header class="account-panel-header account-detail-header">
        <a class="account-back-link" href="{{ route('account.consultations.index') }}">← Kembali ke konsultasi</a>
        <x-consultation-status-badge :status="$consultation->status" />
    </header>

    @if (session('status'))
        <p class="account-status" role="status">{{ session('status') }}</p>
    @endif

    <div class="account-detail-content">
        <div class="account-detail-title">
            <p class="account-consultation-reference">{{ $consultation->nomor_konsultasi }}</p>
            <h1>{{ $consultation->layanan }}</h1>
        </div>

        @if ($consultation->status->canBeCancelled())
            <form action="{{ route('account.consultations.cancel', $consultation) }}" method="POST">
                @csrf
                <button class="account-cancel-action" type="submit">Batalkan konsultasi</button>
            </form>
        @endif

        <article class="account-detail-card">
            <h2>Ringkasan kebutuhan</h2>
            <dl class="account-detail-list">
                <div><dt>Nama</dt><dd>{{ $consultation->nama }}</dd></div>
                <div><dt>Email</dt><dd>{{ $consultation->email }}</dd></div>
                <div><dt>Telepon</dt><dd>{{ $consultation->telepon }}</dd></div>
                @if ($consultation->instansi)
                    <div><dt>Instansi</dt><dd>{{ $consultation->instansi }}</dd></div>
                @endif
                <div><dt>Destinasi</dt><dd>{{ $consultation->tujuan ?: 'Belum ditentukan' }}</dd></div>
                <div><dt>Jumlah peserta</dt><dd>{{ $consultation->jumlah_peserta ?: 'Belum ditentukan' }}</dd></div>
                <div><dt>Durasi</dt><dd>{{ $consultation->durasi_hari ? $consultation->durasi_hari.' hari' : 'Belum ditentukan' }}</dd></div>
                <div><dt>Tanggal rencana</dt><dd>{{ $consultation->tanggal_rencana?->format('d/m/Y') ?: 'Belum ditentukan' }}</dd></div>
                @if ($consultation->layanan === 'Rental Armada')
                    <div><dt>Jenis kendaraan</dt><dd>{{ $consultation->jenis_kendaraan ?: 'Belum ditentukan' }}</dd></div>
                    <div><dt>Lokasi penjemputan</dt><dd>{{ $consultation->lokasi_jemput ?: 'Belum ditentukan' }}</dd></div>
                    <div><dt>Jam penjemputan</dt><dd>{{ $consultation->jam_jemput ? substr($consultation->jam_jemput, 0, 5) : 'Belum ditentukan' }}</dd></div>
                    <div><dt>Tanggal kembali</dt><dd>{{ $consultation->tanggal_kembali?->format('d/m/Y') ?: 'Belum ditentukan' }}</dd></div>
                    <div><dt>Konfirmasi armada</dt><dd>Menunggu konfirmasi ketersediaan dari tim Empirise</dd></div>
                @endif
                @if ($consultation->minat)
                    <div><dt>Minat</dt><dd>{{ $consultation->minat }}</dd></div>
                @endif
                @if ($consultation->cerita_kebutuhan)
                    <div><dt>Catatan kebutuhan</dt><dd>{{ $consultation->cerita_kebutuhan }}</dd></div>
                @endif
            </dl>
        </article>

        @foreach ($offers as $offer)
            <article class="account-detail-card" aria-labelledby="offer-{{ $offer->id_penawaran }}">
                <h2 id="offer-{{ $offer->id_penawaran }}">Penawaran versi {{ $offer->versi }}</h2>
                <p class="text-copy">Status: {{ $offer->status->label() }}</p>
                @if ($offer->catatan_admin)
                    <p class="mt-3 text-copy">{{ $offer->catatan_admin }}</p>
                @endif
                <dl class="account-detail-list mt-4">
                    @foreach ($offer->details as $detail)
                        <div>
                            <dt>{{ $detail->nama_item }}</dt>
                            <dd>
                                {{ number_format((float) $detail->jumlah, 0, ',', '.') }} {{ $detail->satuan }}
                                @if ($detail->harga_satuan !== null)
                                    · Rp {{ number_format((int) $detail->harga_satuan, 0, ',', '.') }}
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
                @if ($offer->status === \App\Enums\PenawaranStatus::Dikirim)
                    <p class="mt-4 font-bold">Total: Rp {{ number_format($offer->total, 0, ',', '.') }}</p>
                    <div class="mt-4 grid gap-3 md:grid-cols-3">
                        <form action="{{ route('api.offers.respond', $offer) }}" method="POST" data-offer-response>
                            @csrf
                            <input type="hidden" name="aksi" value="setuju">
                            <button class="account-primary-action w-full" type="submit">Setuju</button>
                        </form>
                        <form action="{{ route('api.offers.respond', $offer) }}" method="POST" data-offer-response>
                            @csrf
                            <input type="hidden" name="aksi" value="revisi">
                            <label class="sr-only" for="revision-note-{{ $offer->id_penawaran }}">Catatan revisi</label>
                            <textarea id="revision-note-{{ $offer->id_penawaran }}" name="catatan" maxlength="2000" placeholder="Catatan revisi wajib diisi" required></textarea>
                            <button class="account-cancel-action mt-2 w-full" type="submit">Minta revisi</button>
                        </form>
                        <form action="{{ route('api.offers.respond', $offer) }}" method="POST" data-offer-response>
                            @csrf
                            <input type="hidden" name="aksi" value="tolak">
                            <button class="account-cancel-action w-full" type="submit">Tolak</button>
                        </form>
                    </div>
                @endif
            </article>
        @endforeach

        <section class="account-detail-card" aria-labelledby="consultation-replies-title">
            <h2 id="consultation-replies-title">Percakapan</h2>
            @forelse ($replies as $reply)
                <article class="consultation-reply">
                    <header>
                        <strong>{{ $reply->sender?->role === \App\Enums\UserRole::Admin ? 'Tim Empirise' : ($reply->sender?->name ?? 'Pengguna') }}</strong>
                        <time datetime="{{ $reply->created_at->toISOString() }}">{{ $reply->created_at->format('d/m/Y H:i') }}</time>
                    </header>
                    <p>{{ $reply->pesan }}</p>
                </article>
            @empty
                <p>Belum ada pesan dalam percakapan ini. Kirim pesan jika ada informasi tambahan.</p>
            @endforelse
            {{ $replies->links() }}

            <form class="reply-form" action="{{ route('account.consultations.replies.store', $consultation) }}" method="POST">
                @csrf
                <div class="form-field">
                    <label for="consultation-reply">Tulis pesan</label>
                    <textarea id="consultation-reply" name="pesan" rows="4" maxlength="2000" required aria-invalid="{{ $errors->has('pesan') ? 'true' : 'false' }}">{{ old('pesan') }}</textarea>
                    @error('pesan')
                        <p class="form-error" id="consultation-reply-error">{{ $message }}</p>
                    @enderror
                </div>
                <button class="account-primary-action" type="submit">Kirim pesan</button>
            </form>
        </section>
    </div>
@endsection
