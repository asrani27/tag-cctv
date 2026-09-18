<section class="relative overflow-hidden bg-gradient-to-b from-slate-900 via-slate-900 to-slate-800 text-white py-14 sm:py-20 lg:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Portal Smart City Kota Banjarmasin
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-tight">
                    Sistem Informasi Survei <span class="text-emerald-400">CCTV & WiFi</span> Kota Banjarmasin
                </h1>
                <p class="text-base sm:text-lg text-slate-300 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                    Platform informasi dan pemetaan hasil survei lokasi untuk mendukung perencanaan infrastruktur CCTV dan WiFi di Kota Banjarmasin.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3 pt-2">
                    <a href="{{ route('map') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-semibold text-sm bg-emerald-500 text-slate-950 hover:bg-emerald-400 shadow-lg shadow-emerald-500/25 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                        Lihat Peta Survei
                    </a>
                    <a href="{{ route('public.surveys.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-semibold text-sm bg-slate-800 text-white hover:bg-slate-700 border border-slate-700 transition">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                        Jelajahi Data
                    </a>
                </div>
            </div>
            <div class="lg:col-span-5">
                <div class="bg-slate-800/80 border border-slate-700 rounded-2xl p-6 shadow-2xl space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-700 text-xs text-slate-400">
                        <span class="font-semibold text-white">Ringkasan Sistem Lapangan</span>
                        <span class="text-emerald-400 font-medium">Banjarmasin Smart City</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3.5 rounded-xl bg-slate-900/60 border border-slate-700">
                            <div class="text-xs text-slate-400">Total Titik</div>
                            <div class="text-2xl font-bold text-white">{{ number_format($stats['total_survey']) }}</div>
                            <div class="text-[11px] text-emerald-400">Titik Survei Terdata</div>
                        </div>
                        <div class="p-3.5 rounded-xl bg-slate-900/60 border border-slate-700">
                            <div class="text-xs text-slate-400">Kamera CCTV</div>
                            <div class="text-2xl font-bold text-white">{{ number_format($stats['total_cctv']) }}</div>
                            <div class="text-[11px] text-teal-400">Rencana Terpasang</div>
                        </div>
                    </div>
                    <div class="text-xs text-slate-400 flex items-center justify-between pt-2">
                        <span>Cakupan Wilayah:</span>
                        <span class="text-white font-medium">5 Kecamatan &bull; Kota Banjarmasin</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
