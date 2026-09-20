<aside class="hidden lg:flex lg:flex-col w-64 bg-white border-r border-slate-200 shrink-0">
    <div class="flex-1 flex flex-col justify-between p-4 overflow-y-auto">
        <div class="space-y-6">
            <div>
                <div class="px-3 mb-2 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                    Menu Utama
                </div>
                <nav class="space-y-1">
                    <a
                        href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-700 font-semibold border-l-4 border-emerald-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
                    >
                        <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a
                        href="{{ route('surveys.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('surveys.*') ? 'bg-emerald-50 text-emerald-700 font-semibold border-l-4 border-emerald-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
                    >
                        <svg class="w-5 h-5 {{ request()->routeIs('surveys.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <span>{{ auth()->user()?->isSuperAdmin() ? 'Data Survey' : 'Survey Saya' }}</span>
                    </a>

                    <a
                        href="{{ route('map') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('map') ? 'bg-emerald-50 text-emerald-700 font-semibold border-l-4 border-emerald-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
                    >
                        <svg class="w-5 h-5 {{ request()->routeIs('map') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        <span>Peta Survey</span>
                    </a>

                    @if (auth()->user()?->isSuperAdmin())
                        <a
                            href="{{ route('users.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('users.*') ? 'bg-emerald-50 text-emerald-700 font-semibold border-l-4 border-emerald-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
                        >
                            <svg class="w-5 h-5 {{ request()->routeIs('users.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span>Manajemen Pengguna</span>
                        </a>
                    @endif
                </nav>
            </div>

            <div>
                <div class="px-3 mb-2 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                    Aksi Cepat
                </div>
                <div class="px-2">
                    <a
                        href="{{ route('surveys.create') }}"
                        class="w-full flex items-center justify-center gap-2 px-3 py-2 text-xs font-semibold rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition shadow-xs"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Survey
                    </a>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-200 pt-4 mt-6">
            <div class="p-3 bg-slate-50 rounded-xl mb-3 border border-slate-200/60">
                <div class="flex items-center justify-between gap-1 mb-1">
                    <p class="text-xs font-semibold text-slate-800 truncate">{{ auth()->user()->name ?? 'Pengguna' }}</p>
                    @if (auth()->user()?->isSuperAdmin())
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800 shrink-0">Superadmin</span>
                    @else
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-slate-200 text-slate-700 shrink-0">Surveyor</span>
                    @endif
                </div>
                <p class="text-[10px] text-slate-500 truncate">{{ auth()->user()->email ?? '' }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-rose-600 hover:bg-rose-50 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>
</aside>
