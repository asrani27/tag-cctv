<footer class="bg-slate-900 text-slate-300 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 lg:gap-12">
            <div class="md:col-span-2 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-white tracking-tight">Survei CCTV & WiFi</div>
                        <div class="text-xs font-medium text-emerald-400 uppercase tracking-wider">Pemerintah Kota Banjarmasin</div>
                    </div>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed max-w-md">
                    Portal sistem informasi hasil survei geospasial titik lokasi CCTV dan access point WiFi untuk mendukung integrasi infrastruktur digital dan Smart City Kota Banjarmasin.
                </p>
                <div class="text-xs text-slate-400 space-y-1">
                    <p>Dinas Komunikasi, Informatika dan Statistik Kota Banjarmasin</p>
                    <p>Jl. RE Martadinata No. 1, Kota Banjarmasin, Kalimantan Selatan</p>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-white tracking-wider uppercase mb-4">Navigasi Publik</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('home') }}" class="text-slate-400 hover:text-emerald-400 transition-colors">Beranda</a></li>
                    <li><a href="{{ route('map') }}" class="text-slate-400 hover:text-emerald-400 transition-colors">Peta Sebaran Interaktif</a></li>
                    <li><a href="{{ route('public.surveys.index') }}" class="text-slate-400 hover:text-emerald-400 transition-colors">Katalog Data Survei</a></li>
                    <li><a href="{{ route('home') }}#fitur" class="text-slate-400 hover:text-emerald-400 transition-colors">Tentang Sistem</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-white tracking-wider uppercase mb-4">Akses Pengelola</h4>
                <p class="text-xs text-slate-400 mb-3">Area terbatas untuk petugas survei dan tim teknis pengelola infrastruktur.</p>
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold text-slate-900 bg-emerald-400 hover:bg-emerald-300 transition-colors">
                        Buka Dashboard Admin
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold text-slate-200 bg-slate-800 hover:bg-slate-700 border border-slate-700 transition-colors">
                        Masuk Petugas (Login)
                    </a>
                @endauth
            </div>
        </div>

        <div class="mt-12 pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
            <p>&copy; {{ date('Y') }} Pemerintah Kota Banjarmasin. Hak Cipta Dilindungi Undang-Undang.</p>
            <p class="flex items-center gap-2">
                <span>Infrastruktur Teknologi & Informasi Publik</span>
            </p>
        </div>
    </div>
</footer>
