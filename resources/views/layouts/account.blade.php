<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="@yield('meta_description', 'Kelola konsultasi Anda dengan Empirise Jalan-Jalan.')">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Akun klien — Empirise Jalan-Jalan')</title>

        <script>
            (() => {
                const preference = sessionStorage.getItem('empirise-account-sidebar-collapsed');
                const isTablet = window.matchMedia('(min-width: 48rem) and (max-width: 63.999rem)').matches;
                const isCollapsed = preference === 'true' || (preference === null && isTablet);

                if (isCollapsed && window.matchMedia('(min-width: 48rem)').matches) {
                    document.documentElement.dataset.accountSidebarInitiallyCollapsed = 'true';
                }
            })();
        </script>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="account-layout antialiased">
        <main id="konten" class="account-workspace">
            <div class="account-shell" data-account-shell>
                @include('account.partials.sidebar')

                <section class="account-main-panel" aria-label="Area akun klien">
                    @yield('account-content')
                </section>
            </div>
        </main>

        <x-assistant-widget />
    </body>
</html>
