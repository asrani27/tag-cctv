<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Survey CCTV & WiFi Kota Banjarmasin</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full antialiased font-sans bg-slate-100 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center mb-6">
            <div class="inline-flex h-12 w-12 rounded-xl bg-emerald-600 items-center justify-center text-white shadow-md shadow-emerald-600/20 mb-3">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Survey CCTV & WiFi</h1>
            <p class="text-xs text-slate-500 mt-1">Pemerintah Kota Banjarmasin</p>
        </div>

        <div class="bg-white py-8 px-6 sm:px-8 shadow-sm rounded-2xl border border-slate-200" x-data="{ showPassword: false }">
            <div class="mb-5">
                <h2 class="text-base font-semibold text-slate-800">Masuk ke Akun Admin</h2>
                <p class="text-xs text-slate-500">Silakan masukkan email dan kata sandi Anda</p>
            </div>

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        placeholder="admin@banjarmasinkota.go.id"
                        class="block w-full rounded-lg border text-sm px-3.5 py-2.5 transition @error('email') border-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-300 text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200/60 @enderror"
                    />
                    @error('email')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            id="password"
                            name="password"
                            required
                            placeholder="••••••••"
                            class="block w-full rounded-lg border text-sm px-3.5 py-2.5 pr-10 transition @error('password') border-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-300 text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200/60 @enderror"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer"
                        >
                            <span class="text-xs font-medium" x-text="showPassword ? 'Sembunyi' : 'Lihat'"></span>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center pt-1">
                    <input
                        id="remember"
                        name="remember"
                        type="checkbox"
                        value="1"
                        class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer"
                    />
                    <label for="remember" class="ml-2 block text-xs text-slate-600 select-none cursor-pointer">
                        Ingat saya di perangkat ini
                    </label>
                </div>

                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-xs text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-hidden focus:ring-2 focus:ring-emerald-500 transition cursor-pointer"
                    >
                        Masuk
                    </button>
                </div>
            </form>
        </div>

        <p class="text-center text-xs text-slate-400 mt-6">
            &copy; {{ date('Y') }} Diskominfotik Pemerintah Kota Banjarmasin
        </p>
    </div>
</body>
</html>
