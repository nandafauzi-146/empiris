<div class="admin-sidebar-content flex h-full flex-col gap-3">
    <div class="admin-sidebar-identity flex min-w-0 items-center gap-3">
        <span class="grid size-12 shrink-0 place-items-center rounded-full bg-ink font-bold text-white" aria-hidden="true">{{ collect(explode(' ', auth()->user()->name))->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->join('') }}</span>
        <div class="admin-sidebar-identity-copy min-w-0">
            <p class="truncate text-[15px] font-semibold">{{ auth()->user()->name }}</p>
            <p class="truncate text-[13px] text-copy-muted">{{ auth()->user()->email }}</p>
        </div>
    </div>
    <p class="admin-sidebar-label">Panel Admin</p>
    <nav class="admin-sidebar-navigation flex flex-1 flex-col gap-1" aria-label="Navigasi panel admin">
        <a
            href="{{ route('admin.dashboard') }}"
            @class([
                'admin-nav-link flex min-h-12 items-center gap-3 rounded-full px-4 py-3 text-[15px] font-medium transition-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand',
                'bg-white text-ink shadow-[var(--shadow-nav-active)] font-semibold' => request()->routeIs('admin.dashboard'),
                'text-neutral-600 hover:bg-white/70' => ! request()->routeIs('admin.dashboard'),
            ])
            title="Dashboard"
            @if (request()->routeIs('admin.dashboard')) aria-current="page" @endif
        >
            <svg class="size-5 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-brand-500' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            <span class="admin-nav-label">Dashboard</span>
        </a>
        <a
            href="{{ route('admin.consultations.index') }}"
            @class([
                'admin-nav-link flex min-h-12 items-center gap-3 rounded-full px-4 py-3 text-[15px] font-medium transition-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand',
                'bg-white text-ink shadow-[var(--shadow-nav-active)] font-semibold' => request()->routeIs('admin.consultations.*'),
                'text-neutral-600 hover:bg-white/70' => ! request()->routeIs('admin.consultations.*'),
            ])
            title="Konsultasi"
            @if (request()->routeIs('admin.consultations.*')) aria-current="page" @endif
        >
            <svg class="size-5 shrink-0 {{ request()->routeIs('admin.consultations.*') ? 'text-brand-500' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z"/><path d="M8 9h8M8 13h5"/></svg>
            <span class="admin-nav-label">Konsultasi</span>
        </a>
        @foreach ([
            ['admin.projects.*', 'admin.projects.index', 'Proyek & Keuangan', 'wallet'],
            ['admin.calendar.*', 'admin.calendar.index', 'Kalender', 'calendar'],
            ['admin.content.*', 'admin.content.index', 'Konten Web', 'file'],
            ['admin.reports.*', 'admin.reports.index', 'Laporan', 'chart'],
            ['admin.users.*', 'admin.users.index', 'Pengguna', 'users'],
            ['admin.chatbot.*', 'admin.chatbot.index', 'Chatbot AI', 'bot'],
        ] as [$pattern, $route, $label, $icon])
            <a href="{{ route($route) }}" @class([
                'admin-nav-link flex min-h-12 items-center gap-3 rounded-full px-4 py-3 text-[15px] font-medium transition-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand',
                'bg-white text-ink shadow-[var(--shadow-nav-active)] font-semibold' => request()->routeIs($pattern),
                'text-neutral-600 hover:bg-white/70' => ! request()->routeIs($pattern),
            ]) title="{{ $label }}" @if (request()->routeIs($pattern)) aria-current="page" @endif>
                <svg class="size-5 shrink-0 {{ request()->routeIs($pattern) ? 'text-brand-500' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    @if ($icon === 'wallet') <path d="M20 7H5a3 3 0 0 0 0 6h15v6H5a3 3 0 0 1-3-3V7a4 4 0 0 1 4-4h14Z"/><path d="M16 13v3"/>
                    @elseif ($icon === 'calendar') <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>
                    @elseif ($icon === 'file') <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6M8 13h8M8 17h6"/>
                    @elseif ($icon === 'chart') <path d="M3 3v18h18"/><path d="M7 16v-5M12 16V7M17 16v-3"/>
                    @elseif ($icon === 'users') <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    @else <rect x="4" y="6" width="16" height="12" rx="3"/><path d="M9 10h.01M15 10h.01M9 14h6M12 6V3"/>
                    @endif
                </svg>
                <span class="admin-nav-label">{{ $label }}</span>
            </a>
        @endforeach
    </nav>
    <div class="admin-sidebar-footer mt-auto border-t border-line pt-4">
        <div class="admin-sidebar-actions flex items-center gap-2">
            <button
                class="admin-sidebar-collapse-button grid size-11 shrink-0 place-items-center rounded-2xl shadow-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand"
                type="button"
                data-admin-sidebar-collapse
                aria-label="Ciutkan sidebar"
                aria-expanded="true"
                title="Ciutkan sidebar"
            >
                <svg class="size-5" data-collapse-icon viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M9 4v16M15 12h3"/></svg>
                <svg class="size-5 hidden" data-expand-icon viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M9 4v16M12 12h6M15 9l3 3-3 3"/></svg>
                <span class="sr-only">Ciutkan atau perluas sidebar</span>
            </button>
            <form class="admin-sidebar-logout-form min-w-0 flex-1" action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="admin-sidebar-logout flex min-h-11 w-full items-center justify-center gap-3 rounded-2xl px-4 py-3 text-base font-medium shadow-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand" type="submit" title="Keluar">
                    <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 17l5-5-5-5M15 12H3"/><path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/></svg>
                    <span class="admin-sidebar-footer-label">Keluar</span>
                </button>
            </form>
        </div>
    </div>
</div>
