@php
    $accountInitials = collect(preg_split('/\s+/', trim(auth()->user()->name)))
        ->filter()
        ->take(2)
        ->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
@endphp

<aside class="account-sidebar" data-account-sidebar aria-label="Navigasi akun">
    <div class="account-sidebar-identity">
        <span class="account-avatar" aria-hidden="true">{{ $accountInitials }}</span>
        <div class="account-identity-copy">
            <strong>{{ auth()->user()->name }}</strong>
            <span>{{ auth()->user()->email }}</span>
        </div>
    </div>

    <p class="account-sidebar-label">Akun Klien</p>

    <nav class="account-navigation" aria-label="Navigasi akun klien">
        <a
            @class([
                'account-navigation-link',
                'account-navigation-link-active' => request()->routeIs('account.consultations.*'),
            ])
            href="{{ route('account.consultations.index') }}"
            title="Konsultasi"
            @if (request()->routeIs('account.consultations.*')) aria-current="page" @endif
        >
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M5 5.75h14a1.25 1.25 0 0 1 1.25 1.25v9A1.25 1.25 0 0 1 19 17.25h-8l-4.75 3v-3H5A1.25 1.25 0 0 1 3.75 16V7A1.25 1.25 0 0 1 5 5.75Z" />
            </svg>
            <span>Konsultasi</span>
        </a>

        <a
            @class([
                'account-navigation-link',
                'account-navigation-link-active' => request()->routeIs('account.projects.*'),
            ])
            href="{{ route('account.projects.index') }}"
            title="Proyek & Pembayaran"
            @if (request()->routeIs('account.projects.*')) aria-current="page" @endif
        >
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect x="4" y="6.5" width="16" height="13" rx="1.5" />
                <path d="M8 6.5V4.75h8V6.5M8 11h3m-3 3h8" />
            </svg>
            <span>Proyek &amp; Pembayaran</span>
        </a>

        <a
            @class([
                'account-navigation-link',
                'account-navigation-link-active' => request()->routeIs('account.profile.*'),
            ])
            href="{{ route('account.profile.edit') }}"
            title="Profil"
            @if (request()->routeIs('account.profile.*')) aria-current="page" @endif
        >
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle cx="12" cy="8" r="3.25" />
                <path d="M5.5 19.25a6.5 6.5 0 0 1 13 0" />
            </svg>
            <span>Profil</span>
        </a>
    </nav>

    <div class="account-sidebar-footer">
        <div class="account-sidebar-actions">
            <button
                class="account-sidebar-collapse-button"
                type="button"
                data-account-sidebar-collapse
                aria-label="Ciutkan navigasi"
                aria-expanded="true"
                title="Ciutkan navigasi"
            >
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="4" y="4" width="16" height="16" rx="1.5" />
                    <path d="M10 4v16m7-12-4 4 4 4" />
                </svg>
            </button>
            <form action="{{ route('logout') }}" method="POST" class="account-logout-form">
                @csrf
                <button type="submit" class="account-logout-button" title="Keluar">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M10 5.5H6.75A1.25 1.25 0 0 0 5.5 6.75v10.5a1.25 1.25 0 0 0 1.25 1.25H10m4-3 4-4-4-4m4 4H9" />
                    </svg>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>
</aside>
