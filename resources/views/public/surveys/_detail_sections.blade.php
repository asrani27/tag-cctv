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

<div class="bg-white rounded-xl border border-slate-200 p-5 text-xs">
    <div class="flex items-center justify-between mb-3">
        <h2 class="font-bold text-slate-900 uppercase tracking-wider text-emerald-700">5. Foto Dokumentasi</h2>
        @if ($survey->photos && $survey->photos->isNotEmpty())
            <span class="text-[11px] font-medium text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full">
                {{ $survey->photos->count() }} Foto
            </span>
        @endif
    </div>

    @if ($survey->photos && $survey->photos->isNotEmpty())
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach ($survey->photos as $photo)
                <div class="group relative rounded-lg overflow-hidden border border-slate-200 bg-white shadow-xs hover:border-emerald-400 hover:shadow-sm transition">
                    <a href="{{ $photo->url }}" target="_blank" rel="noopener noreferrer" class="block aspect-video sm:aspect-square overflow-hidden bg-slate-100">
                        <img src="{{ $photo->url }}" alt="{{ $photo->original_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" loading="lazy" />
                    </a>
                    <div class="p-2.5 space-y-0.5">
                        <p class="text-[11px] text-slate-800 font-medium truncate" title="{{ $photo->original_name }}">{{ $photo->original_name }}</p>
                        <p class="text-[10px] text-slate-400">{{ $photo->formatted_size }} &bull; {{ $photo->created_at?->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="py-8 text-center bg-slate-50 rounded-lg border border-dashed border-slate-200">
            <svg class="w-8 h-8 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="text-slate-400 text-xs">Belum ada foto dokumentasi untuk titik survei ini.</p>
        </div>
    @endif
</div>
