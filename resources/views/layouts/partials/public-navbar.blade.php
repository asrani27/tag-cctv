<nav class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-20">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-700 flex items-center justify-center text-white shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <div class="text-base font-bold text-slate-900 leading-tight group-hover:text-emerald-600">Survei CCTV & WiFi</div>
                    <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Kota Banjarmasin</div>
                </div>
            </a>
            <div class="hidden md:flex items-center gap-2">
                <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('home') ? 'text-emerald-700 bg-emerald-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Beranda</a>
                <a href="{{ route('map') }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('map') ? 'text-emerald-700 bg-emerald-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Peta Survei</a>
                <a href="{{ route('public.surveys.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('public.surveys.*') ? 'text-emerald-700 bg-emerald-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Data Survei</a>
                <a href="{{ route('home') }}#fitur" class="px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100">Tentang</a>
            </div>
            <div class="hidden md:flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800">
                        Dashboard Admin
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:text-emerald-700 border border-slate-300">
                        Login Admin
                    </a>
                @endauth
            </div>
            <div class="flex items-center md:hidden">
                <button type="button" @click="open = !open" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
            </div>
        </div>
    </div>
    <div class="md:hidden border-b border-slate-200 bg-white px-4 pt-2 pb-4 space-y-1" x-show="open" x-cloak @click.away="open = false">
        <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('home') ? 'text-emerald-700 bg-emerald-50' : 'text-slate-700 hover:bg-slate-100' }}">Beranda</a>
        <a href="{{ route('map') }}" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('map') ? 'text-emerald-700 bg-emerald-50' : 'text-slate-700 hover:bg-slate-100' }}">Peta Survei</a>
        <a href="{{ route('public.surveys.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('public.surveys.*') ? 'text-emerald-700 bg-emerald-50' : 'text-slate-700 hover:bg-slate-100' }}">Data Survei</a>
        <a href="{{ route('home') }}#fitur" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-100">Tentang</a>
        <div class="pt-3 border-t border-slate-100">
            @auth
                <a href="{{ route('dashboard') }}" class="flex items-center justify-center w-full px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-slate-900">Dashboard Admin</a>
            @else
                <a href="{{ route('login') }}" class="flex items-center justify-center w-full px-4 py-2.5 rounded-lg text-sm font-semibold text-slate-700 bg-slate-100">Login Admin</a>
            @endauth
        </div>
    </div>
</nav>
