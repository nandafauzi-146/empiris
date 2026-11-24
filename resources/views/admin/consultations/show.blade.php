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
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('admin.consultations.offers.preview', [$consultation, $offer]) }}" class="button-secondary text-xs" target="_blank">
                                        Pratinjau Proposal
                                    </a>
                                    <a href="{{ route('admin.consultations.offers.pdf', [$consultation, $offer]) }}" class="button-secondary text-xs" target="_blank">
                                        Unduh PDF
                                    </a>
                                    @if (in_array($offer->status, [\App\Enums\PenawaranStatus::Revisi, \App\Enums\PenawaranStatus::Dikirim], true))
                                        <form action="{{ route('admin.consultations.offers.new-version', [$consultation, $offer]) }}" method="POST">
                                            @csrf
                                            <button class="button-secondary text-xs" type="submit">Buat Versi Baru</button>
                                        </form>
                                    @endif
                                    @if ($offer->status === \App\Enums\PenawaranStatus::Draft)
                                        <form action="{{ route('admin.consultations.offers.send', [$consultation, $offer]) }}" method="POST">
                                            @csrf
                                            <button class="button-primary text-xs" type="submit">Kirim penawaran</button>
                                        </form>
                                    @endif
                                </div>
                            </header>

                            @if ($offer->catatan_klien)
                                <div class="mt-3 bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-900">
                                    <strong>Catatan Klien (Permintaan Revisi / Negosiasi):</strong>
                                    <p class="mt-1">{{ $offer->catatan_klien }}</p>
                                </div>
                            @endif

                            @if ($offer->pemahaman)
                                <div class="mt-3 text-xs text-copy">
                                    <strong>Pemahaman Kebutuhan:</strong> {{ $offer->pemahaman }}
                                </div>
                            @endif

                            @if ($offer->days->isNotEmpty())
                                <div class="mt-3 text-xs">
                                    <strong>Rencana Hari ({{ $offer->days->count() }} Hari):</strong>
                                    <ul class="list-disc list-inside mt-1 space-y-1 text-copy">
                                        @foreach ($offer->days as $day)
                                            <li><strong>{{ $day->judul }}:</strong> {{ $day->isi }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
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
                            <h3 class="font-bold text-base mb-3">Buat Draf Proposal / Penawaran Lengkap</h3>

                            <div class="form-field">
                                <label for="offer-title">Judul Proposal</label>
                                <input id="offer-title" name="judul" value="{{ old('judul', 'Proposal ' . $consultation->layanan . ($consultation->instansi ? ' - ' . $consultation->instansi : '')) }}" maxlength="190" required>
                            </div>

                            <div class="form-field">
                                <label for="offer-understanding">Pemahaman Kebutuhan Klien (2-3 Kalimat)</label>
                                <textarea id="offer-understanding" name="pemahaman" rows="3" maxlength="5000">{{ old('pemahaman', 'Kami memahami pihak ' . ($consultation->instansi ?: $consultation->nama) . ' merencanakan agenda ' . $consultation->layanan . ' untuk ' . ($consultation->jumlah_peserta ?: 'rombongan') . ' peserta dengan fokus pada kebersamaan, kenyamanan, dan keselamatan.') }}</textarea>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2 mb-4">
                                <div class="form-field">
                                    <label for="offer-included">Sudah Termasuk (Inclusions)</label>
                                    <textarea id="offer-included" name="cakupan_termasuk" rows="3" maxlength="5000">{{ old('cakupan_termasuk', config('empirise.proposal.cakupan_termasuk')) }}</textarea>
                                </div>
                                <div class="form-field">
                                    <label for="offer-excluded">Belum Termasuk (Exclusions)</label>
                                    <textarea id="offer-excluded" name="cakupan_tidak" rows="3" maxlength="5000">{{ old('cakupan_tidak', config('empirise.proposal.cakupan_tidak')) }}</textarea>
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2 mb-4">
                                <div class="form-field">
                                    <label for="offer-safety">Keselamatan & Operasional (CHG-09)</label>
                                    <textarea id="offer-safety" name="keselamatan" rows="3" maxlength="5000">{{ old('keselamatan', config('empirise.proposal.keselamatan')) }}</textarea>
                                </div>
                                <div class="form-field">
                                    <label for="offer-terms">Syarat & Ketentuan</label>
                                    <textarea id="offer-terms" name="syarat" rows="3" maxlength="5000">{{ old('syarat', config('empirise.proposal.syarat')) }}</textarea>
                                </div>
                            </div>

                            <div class="form-row mb-4">
                                <div class="form-field form-field-half">
                                    <label for="offer-validity">Masa Berlaku Proposal (Hari)</label>
                                    <input id="offer-validity" name="berlaku_hari" type="number" min="1" max="365" value="{{ old('berlaku_hari', 14) }}" required>
                                </div>
                                <div class="form-field form-field-half">
                                    <label for="offer-note">Catatan Tambahan Admin</label>
                                    <input id="offer-note" name="catatan_admin" value="{{ old('catatan_admin') }}" maxlength="5000" placeholder="Opsional">
                                </div>
                            </div>

                            <!-- Rencana Hari Kegiatan -->
                            <div class="mb-4 p-4 border border-line rounded-xl bg-gray-50/50">
                                <h4 class="font-bold text-sm mb-2">Rencana Hari Kegiatan (Itinerary)</h4>
                                <div class="space-y-3">
                                    @php $durasi = max(1, min(7, (int) ($consultation->durasi_hari ?: 1))); @endphp
                                    @for ($d = 1; $d <= $durasi; $d++)
                                        <div class="grid gap-2 md:grid-cols-3">
                                            <div class="form-field">
                                                <label for="day-title-{{ $d }}">Judul Hari {{ $d }}</label>
                                                <input id="day-title-{{ $d }}" name="days[{{ $d - 1 }}][judul]" value="{{ old('days.' . ($d - 1) . '.judul', 'Hari ' . $d . ': ' . ($d === 1 ? 'Kedatangan & Pembukaan' : ($d === $durasi ? 'Penutupan & Kepulangan' : 'Kegiatan Utama'))) }}" maxlength="60">
                                            </div>
                                            <div class="form-field md:col-span-2">
                                                <label for="day-body-{{ $d }}">Uraian Kegiatan Hari {{ $d }}</label>
                                                <input id="day-body-{{ $d }}" name="days[{{ $d - 1 }}][isi]" value="{{ old('days.' . ($d - 1) . '.isi', 'Aktivitas terkoordinasi dan kunjungan program sesuai itinerary.') }}" maxlength="5000">
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <!-- Rincian Biaya -->
                            <div class="mb-4">
                                <h4 class="font-bold text-sm mb-2">Rincian Komponen Biaya</h4>
                                <div class="grid gap-3 md:grid-cols-4">
                                    <div class="form-field md:col-span-2">
                                        <label for="offer-item-name">Nama komponen</label>
                                        <input id="offer-item-name" name="items[0][nama_item]" value="{{ old('items.0.nama_item', 'Paket ' . $consultation->layanan) }}" maxlength="190" required>
                                    </div>
                                    <div class="form-field">
                                        <label for="offer-item-quantity">Jumlah</label>
                                        <input id="offer-item-quantity" name="items[0][jumlah]" type="number" min="0.01" step="0.01" value="{{ old('items.0.jumlah', $consultation->jumlah_peserta ?: 1) }}" required>
                                    </div>
                                    <div class="form-field">
                                        <label for="offer-item-unit">Satuan</label>
                                        <input id="offer-item-unit" name="items[0][satuan]" value="{{ old('items.0.satuan', $consultation->jumlah_peserta ? 'orang' : 'paket') }}" maxlength="30" required>
                                    </div>
                                </div>
                                <div class="form-field mt-2">
                                    <label for="offer-item-price">Harga satuan (Rp)</label>
                                    <input id="offer-item-price" name="items[0][harga_satuan]" type="number" min="0" value="{{ old('items.0.harga_satuan') }}" placeholder="Contoh: 350000 (Kosongkan bila belum disepakati)">
                                </div>
                            </div>

                            <button class="button-primary" type="submit">Buat Draf Proposal</button>
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
