@extends('layouts.public')

@section('title', 'Kata sandi baru — Empirise Jalan-Jalan')
@section('meta_description', 'Atur kata sandi baru untuk akun Empirise Anda.')

@section('content')
    <div class="public-page">
        <section class="page-intro page-shell">
            <p class="section-eyebrow">BANTUAN AKUN</p>
            <h1>Buat kata sandi baru.</h1>
            <p>Pilih kata sandi minimal 8 karakter untuk akun Empirise Anda.</p>
        </section>

        <section class="content-section content-section-tint">
            <div class="page-shell account-page-shell">
                <form class="account-form" action="{{ route('password.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="form-field">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email', request('email')) }}" required autofocus aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}">
                        @error('email')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-field">
                        <label for="password">Kata sandi baru</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}">
                        @error('password')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-field">
                        <label for="password_confirmation">Ulangi kata sandi</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required>
                    </div>
                    <button class="button-primary account-submit" type="submit">Simpan kata sandi baru</button>
                </form>
            </div>
        </section>
    </div>
@endsection
