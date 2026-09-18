<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') | Survey CCTV & WiFi Kota Banjarmasin</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="h-full antialiased font-sans text-slate-800 selection:bg-emerald-500 selection:text-white" x-data="{ sidebarOpen: false }">
    <div class="min-h-full flex flex-col">
        <!-- Top Navbar -->
        @include('layouts.partials.navbar')

        <div class="flex-1 flex overflow-hidden">
            <!-- Sidebar Desktop -->
            @include('layouts.partials.sidebar')

            <!-- Sidebar Mobile -->
            @include('layouts.partials.mobile-sidebar')

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto bg-slate-50/70 py-6 sm:py-8 px-4 sm:px-6 lg:px-8">
                <div class="max-w-7xl mx-auto space-y-6">
                    @if (session('success'))
                        <x-alert type="success" :message="session('success')" />
                    @endif

                    @if (session('error'))
                        <x-alert type="danger" :message="session('error')" />
                    @endif

                    @if (session('warning'))
                        <x-alert type="warning" :message="session('warning')" />
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
