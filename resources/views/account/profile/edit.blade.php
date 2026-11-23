@extends('layouts.account')

@section('title', 'Profil saya — Empirise Jalan-Jalan')
@section('meta_description', 'Perbarui nama, email, dan nomor telepon akun Empirise Anda.')

@section('account-content')
    <header class="account-panel-header">
        <h1>Profil saya</h1>
    </header>

    @if (session('status'))
        <p class="account-status" role="status">{{ session('status') }}</p>
    @endif

    <form class="account-form account-profile-form" action="{{ route('account.profile.update') }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="form-field">
            <label for="profile-name">Nama lengkap</label>
            <input
                id="profile-name"
                name="name"
                type="text"
                autocomplete="name"
                value="{{ old('name', $user->name) }}"
                maxlength="120"
                required
                aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                @if ($errors->has('name')) aria-describedby="profile-name-error" @endif
            >
            @error('name')
                <p class="form-error" id="profile-name-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="profile-email">Email</label>
            <input
                id="profile-email"
                name="email"
                type="email"
                autocomplete="email"
                value="{{ old('email', $user->email) }}"
                maxlength="255"
                required
                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                @if ($errors->has('email')) aria-describedby="profile-email-error" @endif
            >
            @error('email')
                <p class="form-error" id="profile-email-error">{{ $message }}</p>
            @enderror
            <p class="form-note">Jika email diubah, Anda perlu memverifikasi alamat email yang baru.</p>
        </div>

        <div class="form-field">
            <label for="profile-phone">Nomor telepon</label>
            <input
                id="profile-phone"
                name="phone"
                type="tel"
                autocomplete="tel"
                value="{{ old('phone', $user->phone) }}"
                maxlength="30"
                required
                aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}"
                @if ($errors->has('phone')) aria-describedby="profile-phone-error" @endif
            >
            @error('phone')
                <p class="form-error" id="profile-phone-error">{{ $message }}</p>
            @enderror
        </div>

        <button class="account-primary-action account-profile-submit" type="submit">Simpan perubahan</button>
    </form>
@endsection
