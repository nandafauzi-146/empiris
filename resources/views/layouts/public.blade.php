<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="@yield('meta_description', 'Jelajahi layanan, destinasi, dan kegiatan Empirise Jalan-Jalan.')">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Empirise Jalan-Jalan')</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body
        @class(['font-sans antialiased public-layout', 'auth-layout' => request()->routeIs('login', 'register')])
        @if (request()->routeIs('login', 'register')) data-auth-page="{{ request()->routeIs('login') ? 'login' : 'register' }}" @endif
    >
        <x-site-header :transparent="false" :animate-scroll="! request()->routeIs('login', 'register')" />

        <main id="konten">
            @yield('content')
        </main>

        <x-site-footer />
        <x-assistant-widget />
    </body>
</html>
