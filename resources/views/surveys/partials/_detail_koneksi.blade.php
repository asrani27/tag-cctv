<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <x-card title="Karakteristik WiFi" subtitle="Cakupan dan estimasi user">
        <dl class="space-y-3 text-xs">
            <div><dt class="text-slate-400">Luas Area</dt><dd class="font-medium text-slate-800">{{ $survey->luas_area ? $survey->luas_area . ' m²' : '-' }}</dd></div>
            <div><dt class="text-slate-400">Prakiraan Pengguna Max</dt><dd class="font-medium text-slate-800">{{ $survey->prakiraan_pengguna_max ? $survey->prakiraan_pengguna_max . ' orang' : '-' }}</dd></div>
            <div><dt class="text-slate-400">Titik Pasang AP</dt><dd class="font-mono text-[11px] text-slate-700">{{ $survey->tempat_ap_latitude && $survey->tempat_ap_longitude ? $survey->tempat_ap_latitude . ', ' . $survey->tempat_ap_longitude : '-' }}</dd></div>
            <div><dt class="text-slate-400">Potensi Interferensi</dt><dd class="font-medium text-slate-800">{{ $survey->potensi_interferensi ?? '-' }}</dd></div>
        </dl>
    </x-card>

    <x-card title="Ketersediaan Listrik" subtitle="Catu daya & proteksi lonjakan">
        <dl class="space-y-3 text-xs">
            <div><dt class="text-slate-400">Sumber Listrik</dt><dd class="font-medium text-slate-800">{{ $survey->sumber_listrik ?? '-' }}</dd></div>
            <div><dt class="text-slate-400">Daya Tersedia</dt><dd class="font-medium text-slate-800">{{ $survey->daya_tersedia ? $survey->daya_tersedia . ' VA' : '-' }}</dd></div>
            <div><dt class="text-slate-400">Posisi Panel</dt><dd class="font-mono text-[11px] text-slate-700">{{ $survey->posisi_panel_latitude && $survey->posisi_panel_longitude ? $survey->posisi_panel_latitude . ', ' . $survey->posisi_panel_longitude : '-' }}</dd></div>
            <div><dt class="text-slate-400">Proteksi Petir</dt><dd class="font-medium text-slate-800">{{ $survey->proteksi_petir ?? '-' }}</dd></div>
        </dl>
    </x-card>

    <x-card title="Jalur Komunikasi" subtitle="Konektivitas dan ISP">
        <dl class="space-y-3 text-xs">
            <div><dt class="text-slate-400">Fiber Optik</dt><dd class="font-medium {{ $survey->tersedia_fiber_optik ? 'text-emerald-700 font-semibold' : 'text-slate-500' }}">{{ $survey->tersedia_fiber_optik ? 'Tersedia' : 'Tidak' }}</dd></div>
            <div><dt class="text-slate-400">Sinyal 4G/5G</dt><dd class="font-medium {{ $survey->tersedia_4g_5g ? 'text-emerald-700 font-semibold' : 'text-slate-500' }}">{{ $survey->tersedia_4g_5g ? 'Tersedia' : 'Tidak' }}</dd></div>
            <div><dt class="text-slate-400">Link P2P</dt><dd class="font-medium {{ $survey->tersedia_link_p2p ? 'text-emerald-700 font-semibold' : 'text-slate-500' }}">{{ $survey->tersedia_link_p2p ? 'Tersedia' : 'Tidak' }}</dd></div>
            <div><dt class="text-slate-400">Jumlah Provider</dt><dd class="font-medium text-slate-800">{{ $survey->jumlah_provider ?? 0 }} Operator/ISP</dd></div>
        </dl>
    </x-card>
</div>
