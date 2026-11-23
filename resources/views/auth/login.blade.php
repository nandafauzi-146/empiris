@extends('layouts.public')

@section('title', 'Masuk — Empirise Jalan-Jalan')
@section('meta_description', 'Masuk ke akun Empirise untuk memantau konsultasi Anda.')

@section('content')
    <div class="auth-main">
        <section class="auth-stage page-shell" aria-label="Masuk ke akun Empirise">
            <div class="auth-card">
                <aside class="auth-visual auth-visual-login">
                    <span class="auth-visual-star" aria-hidden="true">*</span>
                    <div class="auth-visual-copy">
                        <p>Selamat datang kembali</p>
                        <h1>Pantau konsultasi dan proyekmu dalam satu tempat</h1>
                    </div>
                </aside>

                <div class="auth-panel">
                    <span class="auth-panel-mark" aria-hidden="true">*</span>
                    <h2>Masuk ke akun</h2>
                    <p class="auth-description">Akses konsultasi, proyek, dan pembayaranmu kapan saja.</p>
                    @if (session('status'))
                        <p class="account-status" role="status">{{ session('status') }}</p>
                    @endif
                    <form class="auth-form" action="{{ route('login.store') }}" method="POST">
                        @csrf
                    <div class="form-field">
                        <label for="email">Email</label>
                            <input id="email" name="email" type="email" autocomplete="email" placeholder="nama@email.com" value="{{ old('email') }}" required autofocus aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}">
                        @error('email')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-field">
                        <label for="password">Kata sandi</label>
                            <div class="auth-password-field">
                                <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Masukkan kata sandi" required>
                                <button class="auth-password-toggle" type="button" aria-label="Tampilkan kata sandi" aria-pressed="false" data-password-toggle="password">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                                </button>
                            </div>
                        @error('password')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                        <div class="auth-form-meta">
                            <label class="account-remember" for="remember">
                                <input id="remember" name="remember" type="checkbox" value="1" @checked(old('remember'))>
                                <span>Ingat saya</span>
                            </label>
                            <a href="{{ route('password.request') }}">Lupa kata sandi?</a>
                        </div>
                        <button class="button-primary auth-submit" type="submit">Masuk</button>
                        <p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}" data-auth-transition="register">Daftar</a></p>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
