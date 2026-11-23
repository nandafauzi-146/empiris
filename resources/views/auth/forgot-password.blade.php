@extends('layouts.public')

@section('title', 'Lupa kata sandi — Empirise Jalan-Jalan')
@section('meta_description', 'Minta tautan untuk mengatur ulang kata sandi akun Empirise.')

@section('content')
    <div class="public-page">
        <section class="page-intro page-shell">
            <p class="section-eyebrow">BANTUAN AKUN</p>
            <h1>Atur ulang kata sandi.</h1>
            <p>Masukkan email akun Anda. Jika terdaftar, kami akan mengirim tautan reset.</p>
        </section>

        <section class="content-section content-section-tint">
            <div class="page-shell account-page-shell">
                <form class="account-form" action="{{ route('password.email') }}" method="POST">
                    @csrf
                    @if (session('status'))
                        <p class="account-status" role="status">{{ session('status') }}</p>
                    @endif
                    <div class="form-field">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required autofocus aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}">
                        @error('email')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <button class="button-primary account-submit" type="submit">Kirim tautan reset</button>
                    <p class="account-form-links"><a href="{{ route('login') }}">Kembali ke halaman masuk</a></p>
                </form>
            </div>
        </section>
    </div>
@endsection
