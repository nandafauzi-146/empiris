<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="@yield('meta_description', 'Panel admin Empirise Jalan-Jalan.')">
        <title>@yield('title', 'Admin Empirise Jalan-Jalan')</title>

        <script>
            (() => {
                const isCollapsed = window.sessionStorage.getItem('empirise-admin-sidebar-collapsed') === 'true';

                if (isCollapsed && window.matchMedia('(min-width: 48rem)').matches) {
                    document.documentElement.dataset.adminSidebarInitiallyCollapsed = 'true';
                }
            })();
        </script>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="admin-layout min-h-screen font-sans antialiased text-ink">
        <div class="admin-shell mx-0 flex min-h-screen w-full max-w-[1600px] gap-4 p-3 sm:p-5 sm:pl-7">
            <div
                class="fixed inset-0 z-30 hidden bg-black/40 md:hidden"
                data-admin-sidebar-overlay
                aria-hidden="true"
            ></div>
            <aside
                class="fixed inset-y-3 left-3 z-40 hidden w-[280px] rounded-[2rem] p-4 shadow-card md:sticky md:top-5 md:flex md:w-[280px] md:shrink-0 md:self-start"
                style="background: var(--sidebar-bg)"
                data-admin-sidebar
                aria-hidden="false"
                aria-label="Navigasi admin"
            >
                <button class="absolute right-5 top-5 grid size-10 place-items-center rounded-full text-neutral-600 hover:bg-white md:hidden" type="button" data-admin-sidebar-close aria-label="Tutup navigasi">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m18 6-12 12M6 6l12 12"/></svg>
                </button>
                @include('admin.partials.sidebar')
            </aside>
            <main id="konten" class="min-w-0 flex-1 rounded-[2rem] bg-white p-5 shadow-sm sm:p-8 lg:p-10">
                <div class="mb-5 flex items-center justify-between md:hidden">
                    <span class="font-semibold">Panel Admin</span>
                    <button class="rounded-full border border-line px-4 py-2 text-sm" type="button" data-admin-sidebar-open aria-label="Buka navigasi" aria-expanded="false">Menu</button>
                </div>
                @if (session('status'))
                    <div class="mb-6 rounded-2xl bg-green-50 px-5 py-3 text-sm font-semibold text-green-800" role="status">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="mb-6 rounded-2xl bg-red-50 px-5 py-3 text-sm text-red-800" role="alert">
                        <ul class="list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </body>
</html>