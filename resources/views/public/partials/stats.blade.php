<section class="relative -mt-10 z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200/80 p-6 sm:p-8">
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-6 lg:gap-4 divide-y sm:divide-y-0 sm:divide-x divide-slate-100">
            <div class="flex flex-col items-center text-center p-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Total Titik Survei</span>
                <span class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">{{ number_format($stats['total_survey']) }}</span>
                <span class="text-xs text-slate-400 mt-1">Lokasi observasi</span>
            </div>
            <div class="flex flex-col items-center text-center p-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600 mb-1">Total CCTV</span>
                <span class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">{{ number_format($stats['total_cctv']) }}</span>
                <span class="text-xs text-slate-400 mt-1">Unit kamera</span>
            </div>
            <div class="flex flex-col items-center text-center p-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-teal-600 mb-1">Access Point</span>
                <span class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">{{ number_format($stats['total_ap']) }}</span>
                <span class="text-xs text-slate-400 mt-1">Titik pancar WiFi</span>
            </div>
            <div class="flex flex-col items-center text-center p-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-indigo-600 mb-1">Kecamatan</span>
                <span class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">{{ number_format($stats['total_kecamatan']) }}</span>
                <span class="text-xs text-slate-400 mt-1">Wilayah kecamatan</span>
            </div>
            <div class="flex flex-col items-center text-center p-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-amber-600 mb-1">Kelurahan</span>
                <span class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">{{ number_format($stats['total_kelurahan']) }}</span>
                <span class="text-xs text-slate-400 mt-1">Kelurahan terjangkau</span>
            </div>
        </div>
    </div>
</section>
