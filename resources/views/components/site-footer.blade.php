<footer @class(['site-footer-wrap', 'auth-footer' => request()->routeIs('login', 'register')])>
    <div class="site-footer page-shell">
        <a class="brand-mark footer-brand" href="{{ route('home') }}" aria-label="Empirise, kembali ke beranda">
            <img src="{{ asset('images/figma/empirise-logo.png') }}" alt="Empirise Jalan-Jalan" width="140" height="44" loading="lazy">
        </a>
        <nav class="footer-links" aria-label="Navigasi footer">
            <a href="{{ route('about') }}">Tentang</a>
            <a href="{{ route('services.index') }}">Layanan</a>
            <a href="{{ route('destinations.index') }}">Destinasi</a>
            <a href="{{ route('portfolios.index') }}">Portofolio</a>
            <a href="{{ route('contact') }}">FAQ &amp; Kontak</a>
        </nav>
        <p>
            <a href="mailto:halo@empirise.id">halo@empirise.id</a>
            <span aria-hidden="true">/</span>
            <a href="https://wa.me/6281234567890">WhatsApp 0812-3456-7890</a>
        </p>
        <p>2025 Empirise. Berizin resmi.</p>
    </div>
</footer>
