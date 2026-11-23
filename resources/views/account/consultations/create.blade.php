@extends('layouts.account')

@section('title', 'Konsultasi baru — Empirise Jalan-Jalan')
@section('meta_description', 'Ceritakan kebutuhan kegiatan Anda. Tim Empirise akan menghubungi melalui email.')

@section('account-content')
    <header class="account-panel-header">
        <div>
            <p class="account-page-eyebrow">
                <a href="{{ route('account.consultations.index') }}" class="account-breadcrumb-link">
                    <span aria-hidden="true">←</span> Konsultasi saya
                </a>
            </p>
            <h1>Konsultasi baru</h1>
        </div>
    </header>

    <div class="account-consultation-create-layout">
        {{-- Info identitas klien --}}
        <div class="account-consultation-identity">
            <p class="account-consultation-identity-label">Dikirim sebagai</p>
            <p class="account-consultation-identity-name">{{ auth()->user()->name }}</p>
            <p class="account-consultation-identity-email">{{ auth()->user()->email }}</p>
            <p class="account-consultation-identity-note">
                Data kontak diambil dari akun Anda. Untuk mengubahnya, pergi ke
                <a href="{{ route('account.profile.edit') }}">profil</a>.
            </p>
        </div>

        <form class="consultation-form account-consultation-form"
              action="{{ route('account.consultations.store') }}"
              method="POST"
              data-consultation-form>
            @csrf

            {{-- Nama, email, telepon: hidden — diisi dari profil akun (R1) --}}
            <input type="hidden" name="nama" value="{{ auth()->user()->name }}">
            <input type="hidden" name="email" value="{{ auth()->user()->email }}">
            <input type="hidden" name="telepon" value="{{ auth()->user()->telepon ?? '—' }}">

            @if ($errors->any())
                <div class="account-form-error-summary" role="alert">
                    <p><strong>Terdapat kesalahan pada formulir:</strong></p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-field">
                <label for="ac-service">Kebutuhan</label>
                <select id="ac-service" name="layanan" required
                        aria-invalid="{{ $errors->has('layanan') ? 'true' : 'false' }}">
                    <option value="">Pilih kebutuhan</option>
                    <option value="Tour & Wisata"
                        {{ old('layanan', $selectedService) === 'Tour & Wisata' ? 'selected' : '' }}>
                        Tour &amp; Wisata</option>
                    <option value="Event & Gathering"
                        {{ old('layanan', $selectedService) === 'Event & Gathering' ? 'selected' : '' }}>
                        Event &amp; Gathering</option>
                    <option value="Outbound"
                        {{ old('layanan', $selectedService) === 'Outbound' ? 'selected' : '' }}>
                        Outbound</option>
                    <option value="Rental Armada"
                        {{ old('layanan', $selectedService) === 'Rental Armada' ? 'selected' : '' }}>
                        Rental Armada</option>
                    <option value="Team Building"
                        {{ old('layanan', $selectedService) === 'Team Building' ? 'selected' : '' }}>
                        Team Building</option>
                </select>
                @error('layanan')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-field">
                <label for="ac-organization">Instansi / perusahaan (opsional)</label>
                <input id="ac-organization" name="instansi" type="text"
                       autocomplete="organization" value="{{ old('instansi') }}" maxlength="255">
            </div>

            {{-- Bagian Rental Armada --}}
            <fieldset class="rental-details-fieldset" data-rental-details
                      {{ old('layanan', $selectedService) !== 'Rental Armada' ? 'hidden' : '' }}>
                <legend>Detail kendaraan</legend>
                <p class="rental-disclaimer">
                    Permintaan ini belum mengunci kendaraan atau tanggal.
                    Ketersediaan akan dikonfirmasi oleh admin.
                </p>
                <div class="form-field">
                    <label for="ac-vehicle-type">Jenis kendaraan</label>
                    <select id="ac-vehicle-type" name="jenis_kendaraan"
                            aria-invalid="{{ $errors->has('jenis_kendaraan') ? 'true' : 'false' }}">
                        <option value="">Pilih jenis kendaraan</option>
                        <option value="Mobil"
                            {{ old('jenis_kendaraan') === 'Mobil' ? 'selected' : '' }}>Mobil</option>
                        <option value="Minibus"
                            {{ old('jenis_kendaraan') === 'Minibus' ? 'selected' : '' }}>Minibus</option>
                        <option value="Bus"
                            {{ old('jenis_kendaraan') === 'Bus' ? 'selected' : '' }}>Bus</option>
                        <option value="Belum menentukan"
                            {{ old('jenis_kendaraan') === 'Belum menentukan' ? 'selected' : '' }}>
                            Belum menentukan</option>
                    </select>
                    @error('jenis_kendaraan')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-field">
                    <label for="ac-pickup-location">Lokasi penjemputan</label>
                    <input id="ac-pickup-location" name="lokasi_jemput" type="text"
                           value="{{ old('lokasi_jemput') }}" maxlength="255"
                           aria-invalid="{{ $errors->has('lokasi_jemput') ? 'true' : 'false' }}">
                    @error('lokasi_jemput')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-field">
                    <label for="ac-pickup-time">Perkiraan jam penjemputan (opsional)</label>
                    <input id="ac-pickup-time" name="jam_jemput" type="time"
                           value="{{ old('jam_jemput') }}">
                    @error('jam_jemput')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-field">
                    <label for="ac-return-date">Tanggal kembali (opsional)</label>
                    <input id="ac-return-date" name="tanggal_kembali" type="date"
                           value="{{ old('tanggal_kembali') }}">
                    @error('tanggal_kembali')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </fieldset>

            <div class="form-field">
                <label for="ac-destination">Destinasi atau rute (opsional)</label>
                <input id="ac-destination" name="tujuan" type="text"
                       value="{{ old('tujuan') }}"
                       placeholder="Contoh: Bandung – Lembang – Bandung" maxlength="255">
            </div>

            <div class="form-row">
                <div class="form-field form-field-half">
                    <label for="ac-date">Tanggal rencana / keberangkatan</label>
                    <input id="ac-date" name="tanggal_rencana" type="date"
                           value="{{ old('tanggal_rencana') }}">
                    @error('tanggal_rencana')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-field form-field-half">
                    <label for="ac-participants">Jumlah peserta</label>
                    <input id="ac-participants" name="jumlah_peserta" type="number"
                           min="1" max="100000" inputmode="numeric"
                           value="{{ old('jumlah_peserta') }}">
                    @error('jumlah_peserta')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-field form-field-half">
                    <label for="ac-duration">Durasi (hari, opsional)</label>
                    <input id="ac-duration" name="durasi_hari" type="number"
                           min="1" max="365" inputmode="numeric"
                           value="{{ old('durasi_hari') }}">
                    @error('durasi_hari')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-field">
                <label for="ac-interests">Minat atau aktivitas yang disukai</label>
                <textarea id="ac-interests" name="minat" rows="3"
                          maxlength="1000">{{ old('minat') }}</textarea>
            </div>

            <div class="form-field">
                <label for="ac-notes">Cerita kebutuhan</label>
                <textarea id="ac-notes" name="cerita_kebutuhan" rows="4" maxlength="1500"
                          placeholder="Ceritakan gambaran kegiatan yang Anda rencanakan.">{{ old('cerita_kebutuhan') }}</textarea>
                @error('cerita_kebutuhan')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="account-consultation-form-actions">
                <button class="button-primary" type="submit">
                    Kirim konsultasi <span aria-hidden="true">→</span>
                </button>
                <p class="form-note">
                    Informasi disimpan untuk menindaklanjuti konsultasi. Tim kami akan
                    meninjau dan menghubungi Anda melalui email dalam waktu dekat.
                </p>
            </div>
        </form>
    </div>
@endsection
