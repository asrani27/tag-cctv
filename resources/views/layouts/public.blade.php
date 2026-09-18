<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sistem Informasi Survei CCTV & WiFi Kota Banjarmasin')</title>
    <meta name="description" content="@yield('meta_description', 'Portal informasi dan pemetaan spasial hasil survei lokasi CCTV dan WiFi di Kota Banjarmasin untuk mendukung integrasi infrastruktur Smart City.')">

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('title', 'Sistem Informasi Survei CCTV & WiFi Kota Banjarmasin')">
    <meta property="og:description" content="@yield('meta_description', 'Portal informasi dan pemetaan spasial hasil survei lokasi CCTV dan WiFi di Kota Banjarmasin.')">
    <meta property="og:type" content="website">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="min-h-full flex flex-col antialiased font-sans text-slate-800 selection:bg-emerald-500 selection:text-white bg-slate-50">
    <!-- Public Navbar -->
    @include('layouts.partials.public-navbar')

    <!-- Main Public Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Public Footer -->
    @include('layouts.partials.public-footer')

    @stack('scripts')
</body>
</html>
