<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-2 text-xs">
        <h2 class="font-bold text-slate-900 uppercase tracking-wider text-emerald-700">1. Lokasi</h2>
        <div class="flex justify-between py-1 border-b border-slate-100"><span>Alamat:</span><strong>{{ $survey->alamat ?? '-' }}</strong></div>
        <div class="flex justify-between py-1 border-b border-slate-100"><span>RT/RW:</span><strong>{{ $survey->rt ?? '-' }}/{{ $survey->rw ?? '-' }}</strong></div>
        <div class="flex justify-between py-1 border-b border-slate-100"><span>Kelurahan:</span><strong>{{ $survey->kelurahan ?? '-' }}</strong></div>
        <div class="flex justify-between py-1 border-b border-slate-100"><span>Kecamatan:</span><strong>{{ $survey->kecamatan ?? '-' }}</strong></div>
        <div class="flex justify-between py-1 border-b border-slate-100"><span>Koordinat:</span><strong class="font-mono">{{ $survey->hasCoordinates() ? "{$survey->latitude}, {$survey->longitude}" : 'Nihil' }}</strong></div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h2 class="font-bold text-slate-900 text-xs uppercase tracking-wider text-emerald-700 mb-2">Peta</h2>
        @if ($survey->hasCoordinates())
            <div id="detail-mini-map" class="w-full h-40 rounded-lg bg-slate-100"></div>
        @else
            <div class="w-full h-40 rounded-lg bg-slate-50 border border-dashed border-slate-300 flex items-center justify-center text-xs text-slate-400">Koordinat belum tercatat</div>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
    <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-2">
        <h2 class="font-bold text-slate-900 uppercase tracking-wider text-emerald-700">2. Infrastruktur</h2>
        <div class="flex justify-between py-1 border-b border-slate-100"><span>CCTV:</span><strong>{{ $survey->jumlah_cctv ?? 0 }} Unit</strong></div>
        <div class="flex justify-between py-1 border-b border-slate-100"><span>WiFi AP:</span><strong>{{ $survey->jumlah_ap ?? 0 }} Unit</strong></div>
        <div class="flex justify-between py-1 border-b border-slate-100"><span>Bandwidth:</span><strong>{{ $survey->bandwidth ?? '-' }}</strong></div>
        <div class="flex justify-between py-1 border-b border-slate-100"><span>Backhaul:</span><strong>{{ $survey->backhaul ?? '-' }}</strong></div>
        <div class="flex justify-between py-1 border-b border-slate-100"><span>Storage:</span><strong>{{ $survey->storage ?? '-' }}</strong></div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-2">
        <h2 class="font-bold text-slate-900 uppercase tracking-wider text-emerald-700">3. Koneksi & Daya</h2>
        <div class="flex justify-between py-1 border-b border-slate-100"><span>Fiber Optik:</span><strong>{{ $survey->tersedia_fiber_optik ? 'Ya' : 'Tidak' }}</strong></div>
        <div class="flex justify-between py-1 border-b border-slate-100"><span>4G/5G:</span><strong>{{ $survey->tersedia_4g_5g ? 'Ya' : 'Tidak' }}</strong></div>
        <div class="flex justify-between py-1 border-b border-slate-100"><span>Link P2P:</span><strong>{{ $survey->tersedia_link_p2p ? 'Ya' : 'Tidak' }}</strong></div>
        <div class="flex justify-between py-1 border-b border-slate-100"><span>Sumber Listrik:</span><strong>{{ $survey->sumber_listrik ?? '-' }}</strong></div>
        <div class="flex justify-between py-1 border-b border-slate-100"><span>Proteksi Petir:</span><strong>{{ $survey->proteksi_petir ?? '-' }}</strong></div>
    </div>
</div>

<div class="bg-white rounded-xl border border-slate-200 p-5 text-xs">
    <h2 class="font-bold text-slate-900 uppercase tracking-wider text-emerald-700 mb-2">4. Kondisi Fisik</h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-slate-50 p-2.5 rounded"><span class="text-slate-400 block text-[10px]">Jalan</span><strong>{{ $survey->jenis_jalan ?? '-' }}</strong></div>
        <div class="bg-slate-50 p-2.5 rounded"><span class="text-slate-400 block text-[10px]">Tiang</span><strong>{{ $survey->tiang ?? '-' }} {{ $survey->jenis_tiang ? "({$survey->jenis_tiang})" : '' }}</strong></div>
        <div class="bg-slate-50 p-2.5 rounded"><span class="text-slate-400 block text-[10px]">Penerangan</span><strong>{{ $survey->pencahayaan_malam ?? '-' }}</strong></div>
        <div class="bg-slate-50 p-2.5 rounded"><span class="text-slate-400 block text-[10px]">Penghalang</span><strong>{{ $survey->penghalang ?? '-' }}</strong></div>
    </div>
</div>
