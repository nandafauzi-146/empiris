@extends('layouts.public')

@section('title', 'Mulai Konsultasi — Empirise Jalan-Jalan')
@section('meta_description', 'Ceritakan kebutuhan kegiatan Anda kepada tim Empirise.')

@section('content')
    <div class="public-page">
        <section class="page-intro page-shell">
            <p class="section-eyebrow">MULAI KONSULTASI</p>
            <h1>Mari rencanakan kegiatan berikutnya.</h1>
            <p>Bagikan kebutuhan kegiatan Anda. Tim Empirise akan meninjau dan menghubungi Anda melalui email.</p>
        </section>

        <section class="content-section content-section-tint">
            <div class="page-shell consultation-form-layout">

                {{-- Panel utama: sukses atau form --}}
                <div class="consultation-form-main">
                    @if (session('consultation_number'))
                        <div class="consultation-success" role="status" aria-live="polite">
                            <div class="consultation-success-icon" aria-hidden="true">✓</div>
                            <h2>Konsultasi Anda sudah tercatat.</h2>
                            <p>Nomor konsultasi: <strong>{{ session('consultation_number') }}</strong></p>
                            <p>Konfirmasi akan dikirim ke email yang Anda cantumkan. Untuk rental armada, admin akan mengonfirmasi ketersediaan sebelum mengirim penawaran.</p>
                            @if (session('consultation_id'))
                                <a class="account-detail-link" href="{{ route('account.consultations.show', session('consultation_id')) }}">Lihat konsultasi dan percakapan <span aria-hidden="true">→</span></a>
                            @elseif (auth()->check() && auth()->user()->role === \App\Enums\UserRole::Klien && ! auth()->user()->hasVerifiedEmail())
                                <p>Verifikasi email Anda untuk membuka konsultasi dan membalas pesan tim kami.</p>
                                <a class="account-detail-link" href="{{ route('verification.notice') }}">Verifikasi email <span aria-hidden="true">→</span></a>
                            @elseif (! auth()->check())
                                <p>Untuk melihat riwayat dan membalas pesan, daftar menggunakan email yang sama lalu verifikasi alamat email Anda.</p>
                                <a class="account-detail-link" href="{{ route('register') }}">Buat akun klien <span aria-hidden="true">→</span></a>
                            @endif
                        </div>
                    @endif

                    <form class="consultation-form" action="{{ route('consultation.store') }}" method="POST" data-consultation-form>
                        @csrf

                        @if (! auth()->check())
                            {{-- Pesan untuk tamu: dorong untuk login --}}
                            <div class="consultation-login-nudge">
                                <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk</a> agar konsultasi tersimpan di dashboard Anda.</p>
                            </div>
                        @endif

                        <div class="form-row">
                            <div class="form-field form-field-half">
                                <label for="consultation-name">Nama</label>
                                <input id="consultation-name" name="nama" type="text"
                                       autocomplete="name" value="{{ old('nama') }}"
                                       maxlength="255" required
                                       aria-invalid="{{ $errors->has('nama') ? 'true' : 'false' }}">
                                @error('nama')
                                    <p class="form-error" id="consultation-name-error">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-field form-field-half">
                                <label for="consultation-email">Email</label>
                                <input id="consultation-email" name="email" type="email"
                                       autocomplete="email" value="{{ old('email') }}"
                                       maxlength="255" required
                                       aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}">
                                @error('email')
                                    <p class="form-error" id="consultation-email-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-field form-field-half">
                                <label for="consultation-phone">Nomor telepon</label>
                                <input id="consultation-phone" name="telepon" type="tel"
                                       autocomplete="tel" value="{{ old('telepon') }}"
                                       maxlength="30" required
                                       aria-invalid="{{ $errors->has('telepon') ? 'true' : 'false' }}">
                                @error('telepon')
                                    <p class="form-error" id="consultation-phone-error">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-field form-field-half">
                                <label for="consultation-organization">Instansi / perusahaan</label>
                                <input id="consultation-organization" name="instansi" type="text"
                                       autocomplete="organization" value="{{ old('instansi') }}"
                                       maxlength="255">
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="consultation-service">Kebutuhan</label>
                            <select id="consultation-service" name="layanan" required
                                    aria-invalid="{{ $errors->has('layanan') ? 'true' : 'false' }}">
                                <option value="">Pilih kebutuhan</option>
                                <option value="Tour & Wisata" @selected(old('layanan') === 'Tour & Wisata')>Tour &amp; Wisata</option>
                                <option value="Event & Gathering" @selected(old('layanan') === 'Event & Gathering')>Event &amp; Gathering</option>
                                <option value="Outbound" @selected(old('layanan') === 'Outbound')>Outbound</option>
                                <option value="Rental Armada" @selected(old('layanan', $selectedService) === 'Rental Armada')>Rental Armada</option>
                                <option value="Team Building" @selected(old('layanan') === 'Team Building')>Team Building</option>
                            </select>
                            @error('layanan')
                                <p class="form-error" id="consultation-service-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <fieldset
                            class="rental-details-fieldset"
                            data-rental-details
                            @if (old('layanan', $selectedService) !== 'Rental Armada') hidden disabled @endif
                        >
                            <legend class="rental-details-legend">Rincian rental armada</legend>
                            <p class="form-note">
                                Permintaan ini belum mengunci kendaraan atau tanggal. Tim kami akan mengonfirmasi ketersediaan dan menyusun penawaran.
                            </p>
                            <div class="form-row">
                                <div class="form-field form-field-half">
                                    <label for="consultation-vehicle">Jenis kendaraan</label>
                                    <select
                                        id="consultation-vehicle"
                                        name="jenis_kendaraan"
                                        data-rental-required
                                        aria-invalid="{{ $errors->has('jenis_kendaraan') ? 'true' : 'false' }}"
                                        aria-describedby="{{ $errors->has('jenis_kendaraan') ? 'consultation-vehicle-error' : '' }}"
                                    >
                                        <option value="">Pilih jenis kendaraan</option>
                                        @foreach (['Mobil', 'Minibus', 'Bus', 'Belum menentukan'] as $vehicleType)
                                            <option value="{{ $vehicleType }}" @selected(old('jenis_kendaraan') === $vehicleType)>{{ $vehicleType }}</option>
                                        @endforeach
                                    </select>
                                    @error('jenis_kendaraan')
                                        <p class="form-error" id="consultation-vehicle-error">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-field form-field-half">
                                    <label for="consultation-pickup-location">Lokasi penjemputan</label>
                                    <input
                                        id="consultation-pickup-location"
                                        name="lokasi_jemput"
                                        type="text"
                                        maxlength="255"
                                        value="{{ old('lokasi_jemput') }}"
                                        placeholder="Contoh: Stasiun Bandung"
                                        data-rental-required
                                        aria-invalid="{{ $errors->has('lokasi_jemput') ? 'true' : 'false' }}"
                                        aria-describedby="{{ $errors->has('lokasi_jemput') ? 'consultation-pickup-location-error' : '' }}"
                                    >
                                    @error('lokasi_jemput')
                                        <p class="form-error" id="consultation-pickup-location-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-field form-field-half">
                                    <label for="consultation-pickup-time">Perkiraan jam penjemputan (opsional)</label>
                                    <input id="consultation-pickup-time" name="jam_jemput" type="time" value="{{ old('jam_jemput') }}">
                                    @error('jam_jemput')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-field form-field-half">
                                    <label for="consultation-return-date">Tanggal kembali (opsional)</label>
                                    <input id="consultation-return-date" name="tanggal_kembali" type="date" value="{{ old('tanggal_kembali') }}">
                                    @error('tanggal_kembali')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </fieldset>

                        <div class="form-field">
                            <label for="consultation-destination">Destinasi atau rute (opsional)</label>
                            <input id="consultation-destination" name="tujuan" type="text"
                                   value="{{ old('tujuan') }}"
                                   placeholder="Contoh: Bandung – Lembang – Bandung" maxlength="255">
                        </div>

                        <div class="form-row">
                            <div class="form-field form-field-half">
                                <label for="consultation-date">Tanggal rencana / keberangkatan</label>
                                <input id="consultation-date" name="tanggal_rencana" type="date" value="{{ old('tanggal_rencana') }}">
                                @error('tanggal_rencana')
                                    <p class="form-error" id="consultation-date-error">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-field form-field-half">
                                <label for="consultation-participants">Jumlah peserta</label>
                                <input id="consultation-participants" name="jumlah_peserta" type="number"
                                       min="1" max="100000" inputmode="numeric" value="{{ old('jumlah_peserta') }}">
                                @error('jumlah_peserta')
                                    <p class="form-error" id="consultation-participants-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="form-field form-field-half">
                            <label for="consultation-duration">Durasi (hari, opsional)</label>
                            <input id="consultation-duration" name="durasi_hari" type="number"
                                   min="1" max="365" inputmode="numeric" value="{{ old('durasi_hari') }}">
                            @error('durasi_hari')
                                <p class="form-error" id="consultation-duration-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="consultation-interests">Minat atau aktivitas yang disukai</label>
                            <textarea id="consultation-interests" name="minat" rows="3" maxlength="1000">{{ old('minat') }}</textarea>
                        </div>

                        <div class="form-field">
                            <label for="consultation-notes">Catatan kebutuhan</label>
                            <textarea id="consultation-notes" name="cerita_kebutuhan" rows="4" maxlength="1500"
                                      placeholder="Ceritakan gambaran kegiatan yang Anda rencanakan.">{{ old('cerita_kebutuhan') }}</textarea>
                            @error('cerita_kebutuhan')
                                <p class="form-error" id="consultation-notes-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <button class="button-primary consultation-submit" type="submit">
                            Kirim konsultasi <span aria-hidden="true">→</span>
                        </button>
                        <p class="form-note">Informasi Anda disimpan untuk menindaklanjuti konsultasi. Jangan cantumkan informasi sensitif yang tidak diperlukan.</p>
                    </form>
                </div>

                {{-- Aside: info tim + ajakan buat akun --}}
                <aside class="consultation-aside">
                    <div class="consultation-aside-block">
                        <h2>Tim kami siap membantu.</h2>
                        <p>Setelah menerima konsultasi, tim kami akan meninjau kebutuhan Anda dan menghubungi melalui email.</p>
                        <ul class="consultation-aside-steps">
                            <li>
                                <span class="consultation-step-number" aria-hidden="true">1</span>
                                <span>Isi formulir konsultasi</span>
                            </li>
                            <li>
                                <span class="consultation-step-number" aria-hidden="true">2</span>
                                <span>Tim kami meninjau kebutuhan Anda</span>
                            </li>
                            <li>
                                <span class="consultation-step-number" aria-hidden="true">3</span>
                                <span>Penawaran dikirim melalui email</span>
                            </li>
                            <li>
                                <span class="consultation-step-number" aria-hidden="true">4</span>
                                <span>Anda menyetujui dan kegiatan dimulai</span>
                            </li>
                        </ul>
                    </div>

                    @guest
                        <div class="consultation-aside-cta">
                            <p><strong>Punya akun klien?</strong></p>
                            <p>Masuk terlebih dahulu agar konsultasi tersimpan di dashboard Anda dan Anda dapat membalas pesan tim kami.</p>
                            <a class="button-secondary-outline consultation-aside-login" href="{{ route('login') }}">
                                Masuk ke akun
                            </a>
                            <p class="form-note">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
                        </div>
                    @endguest
                </aside>
            </div>
        </section>
    </div>
@endsection
