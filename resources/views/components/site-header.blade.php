@props(['transparent' => true, 'animateScroll' => true])

<header
    @class(['site-header', 'site-header-solid' => ! $transparent])
    @if ($animateScroll) data-scroll-header @endif
>
    <nav class="page-shell site-nav" aria-label="Navigasi utama">
        <a class="brand-mark" href="{{ route('home') }}" aria-label="Empirise, beranda">
            <img src="{{ asset('images/figma/empirise-logo.png') }}" alt="Empirise Jalan-Jalan" width="140" height="44">
        </a>

        <button
            class="menu-toggle"
            type="button"
            aria-label="Buka navigasi"
            aria-expanded="false"
            aria-controls="mobile-menu"
            data-mobile-menu-button
        >
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="desktop-nav" aria-label="Menu halaman">
            <a @class(['nav-link', 'nav-link-active' => request()->routeIs('home')]) href="{{ route('home') }}" @if (request()->routeIs('home')) aria-current="page" @endif>Beranda</a>
            <a @class(['nav-link', 'nav-link-active' => request()->routeIs('about')]) href="{{ route('about') }}" @if (request()->routeIs('about')) aria-current="page" @endif>Tentang</a>
            <a @class(['nav-link', 'nav-link-active' => request()->routeIs('services.*')]) href="{{ route('services.index') }}" @if (request()->routeIs('services.*')) aria-current="page" @endif>Layanan</a>
            <a @class(['nav-link', 'nav-link-active' => request()->routeIs('destinations.*')]) href="{{ route('destinations.index') }}" @if (request()->routeIs('destinations.*')) aria-current="page" @endif>Destinasi</a>
            <a @class(['nav-link', 'nav-link-active' => request()->routeIs('calendar.public')]) href="{{ route('calendar.public') }}" @if (request()->routeIs('calendar.public')) aria-current="page" @endif>Kalender</a>
            <a @class(['nav-link', 'nav-link-active' => request()->routeIs('consultation.*')]) href="{{ route('consultation.create') }}" @if (request()->routeIs('consultation.*')) aria-current="page" @endif>Konsultasi</a>
            <a @class(['nav-link', 'nav-link-active' => request()->routeIs('contact')]) href="{{ route('contact') }}" @if (request()->routeIs('contact')) aria-current="page" @endif>FAQ &amp; Kontak</a>
        </div>

        <div class="nav-actions">
            @auth
                @if (auth()->user()->role === \App\Enums\UserRole::Klien)
                    <a class="nav-action-login" href="{{ route('account.consultations.index') }}">Akun saya</a>
                @elseif (auth()->user()->role === \App\Enums\UserRole::Admin)
                    <a class="nav-action-login" href="{{ route('admin.dashboard') }}">Panel admin</a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="nav-logout-form">
                    @csrf
                    <button class="nav-action-logout" type="submit">Keluar</button>
                </form>
            @else
                <a class="nav-action-login" href="{{ route('login') }}" data-auth-transition="login">Masuk</a>
                <a class="nav-action-register" href="{{ route('register') }}" data-auth-transition="register">Daftar</a>
            @endauth
        </div>
    </nav>

    <nav class="mobile-menu page-shell hidden" id="mobile-menu" aria-label="Navigasi seluler">
        <div class="mobile-menu-panel">
            <a class="mobile-nav-link" href="{{ route('home') }}">Beranda</a>
            <a class="mobile-nav-link" href="{{ route('about') }}">Tentang</a>
            <a class="mobile-nav-link" href="{{ route('services.index') }}">Layanan</a>
            <a class="mobile-nav-link" href="{{ route('destinations.index') }}">Destinasi</a>
            <a class="mobile-nav-link" href="{{ route('calendar.public') }}">Kalender agenda</a>
            <a class="mobile-nav-link" href="{{ route('portfolios.index') }}">Portofolio</a>
            <a class="mobile-nav-link" href="{{ route('vehicles.index') }}">Armada</a>
            <a class="mobile-nav-link" href="{{ route('consultation.create') }}">Konsultasi</a>
            <a class="mobile-nav-link" href="{{ route('contact') }}">FAQ &amp; Kontak</a>
            @auth
                @if (auth()->user()->role === \App\Enums\UserRole::Klien)
                    <a class="mobile-nav-link" href="{{ route('account.consultations.index') }}">Akun saya</a>
                @elseif (auth()->user()->role === \App\Enums\UserRole::Admin)
                    <a class="mobile-nav-link" href="{{ route('admin.dashboard') }}">Panel admin</a>
                @endif
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="mobile-nav-link mobile-nav-button" type="submit">Keluar</button>
                </form>
            @else
                <a class="mobile-nav-link" href="{{ route('login') }}" data-auth-transition="login">Masuk</a>
                <a class="mobile-nav-link" href="{{ route('register') }}" data-auth-transition="register">Daftar akun</a>
            @endauth
        </div>
    </nav>
</header>
