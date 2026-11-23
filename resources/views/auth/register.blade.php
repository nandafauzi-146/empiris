@extends('layouts.public')

@section('title', 'Daftar akun — Empirise Jalan-Jalan')
@section('meta_description', 'Buat akun klien Empirise untuk menyimpan dan memantau konsultasi.')

@section('content')
    <div class="auth-main auth-main-register">
        <section class="auth-stage page-shell" aria-label="Daftar akun Empirise">
            <div class="auth-card auth-card-register">
                <aside class="auth-visual auth-visual-register">
                    <span class="auth-visual-star" aria-hidden="true">*</span>
                    <div class="auth-visual-copy">
                        <p>Mulai perjalanan bersama kami</p>
                        <h1>Satu akun untuk rencana kegiatan yang lebih mudah</h1>
                    </div>
                </aside>

                <div class="auth-panel">
                    <span class="auth-panel-mark" aria-hidden="true">*</span>
                    <h2>Buat akun klien</h2>
                    <p class="auth-description">Daftar untuk menyimpan konsultasi dan mengikuti kabar terbaru dari tim Empirise.</p>
                    <form class="auth-form" action="{{ route('register.store') }}" method="POST">
                        @csrf
                    <div class="form-field">
                        <label for="name">Nama lengkap</label>
                            <input id="name" name="name" type="text" autocomplete="name" placeholder="Nama lengkap" value="{{ old('name') }}" maxlength="255" required autofocus aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}">
                        @error('name')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-field">
                        <label for="email">Email</label>
                            <input id="email" name="email" type="email" autocomplete="email" placeholder="nama@email.com" value="{{ old('email') }}" maxlength="255" required aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}">
                        @error('email')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-field">
                        <label for="phone">Nomor telepon</label>
                            <input id="phone" name="phone" type="tel" autocomplete="tel" placeholder="08xxxxxxxxxx" value="{{ old('phone') }}" maxlength="30" required aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}">
                        @error('phone')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-field">
                        <label for="password">Kata sandi</label>
                            <div class="auth-password-field">
                                <input id="password" name="password" type="password" autocomplete="new-password" placeholder="Minimal 8 karakter" minlength="8" required aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}">
                                <button class="auth-password-toggle" type="button" aria-label="Tampilkan kata sandi" aria-pressed="false" data-password-toggle="password">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                                </button>
                            </div>
                        @error('password')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-field">
                        <label for="password_confirmation">Ulangi kata sandi</label>
                            <div class="auth-password-field">
                                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" placeholder="Ulangi kata sandi" minlength="8" required>
                                <button class="auth-password-toggle" type="button" aria-label="Tampilkan kata sandi" aria-pressed="false" data-password-toggle="password_confirmation">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                                </button>
                            </div>
                        @error('password_confirmation')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                        <p class="auth-verification-note">Tautan verifikasi akan dikirim ke email Anda.</p>
                        <button class="button-primary auth-submit" type="submit">Daftar</button>
                        <p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}" data-auth-transition="login">Masuk</a></p>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
