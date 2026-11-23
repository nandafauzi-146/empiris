@extends('layouts.public')

@section('title', 'Verifikasi email — Empirise Jalan-Jalan')
@section('meta_description', 'Verifikasi alamat email untuk mengamankan akun Empirise Anda.')

@section('content')
    <div class="public-page">
        <section class="page-intro page-shell">
            <p class="section-eyebrow">AMANAN AKUN</p>
            <h1>Verifikasi alamat email.</h1>
            <p>Kami mengirim tautan verifikasi ke {{ auth()->user()->email }}. Verifikasi diperlukan sebelum konsultasi dapat ditautkan ke akun Anda.</p>
        </section>

        <section class="content-section content-section-tint">
            <div class="page-shell account-page-shell">
                <div class="account-form">
                    @if (session('status'))
                        <p class="account-status" role="status">{{ session('status') }}</p>
                    @endif
                    <p class="form-note">Buka email Anda dan klik tautan verifikasi. Jika email belum diterima, Anda dapat meminta tautan baru.</p>
                    <form action="{{ route('verification.send') }}" method="POST">
                        @csrf
                        <button class="button-primary account-submit" type="submit">Kirim ulang tautan verifikasi</button>
                    </form>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="account-detail-link account-link-button" type="submit">Keluar</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
