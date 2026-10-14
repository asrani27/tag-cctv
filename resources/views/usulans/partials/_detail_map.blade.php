<x-card title="Informasi Lokasi & Peta" subtitle="Koordinat geografis dan visualisasi titik usulan pada peta">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="space-y-4 text-xs">
            <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-2">
                <div class="flex justify-between">
                    <span class="text-slate-500">Latitude:</span>
                    <span class="font-mono font-semibold text-slate-800">{{ $usulan->latitude ? number_format($usulan->latitude, 7) : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Longitude:</span>
                    <span class="font-mono font-semibold text-slate-800">{{ $usulan->longitude ? number_format($usulan->longitude, 7) : '-' }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                    <span class="text-slate-400 block text-[11px]">Kelurahan</span>
                    <span class="font-semibold text-slate-800 mt-0.5 block">{{ $usulan->kelurahan ?? '-' }}</span>
                </div>
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                    <span class="text-slate-400 block text-[11px]">Kecamatan</span>
                    <span class="font-semibold text-slate-800 mt-0.5 block">{{ $usulan->kecamatan ?? '-' }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                    <span class="text-slate-400 block text-[11px]">RT</span>
                    <span class="font-bold text-slate-800 mt-0.5 block">RT {{ $usulan->rt ?? '-' }}</span>
                </div>
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                    <span class="text-slate-400 block text-[11px]">Ketua RT</span>
                    <span class="font-semibold text-slate-800 mt-0.5 block">{{ $usulan->nama_ketua_rt ?? '-' }}</span>
                </div>
            </div>

            @if ($usulan->no_hp)
                <div class="p-3 rounded-lg bg-emerald-50/50 border border-emerald-100 flex items-center justify-between">
                    <div>
                        <span class="text-slate-500 block text-[11px]">No HP</span>
                        <span class="font-mono font-bold text-emerald-800">{{ $usulan->no_hp }}</span>
                    </div>
                    <a
                        href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $usulan->no_hp)) }}"
                        target="_blank"
                        class="px-2 py-1 text-[11px] font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded transition"
                    >
                        WA
                    </a>
                </div>
            @endif
        </div>

        <div class="lg:col-span-2">
            @if ($usulan->hasCoordinates())
                <div id="mini-map" class="h-64 sm:h-72 w-full rounded-xl border border-slate-200 overflow-hidden shadow-inner"></div>
            @else
                <div class="h-64 sm:h-72 w-full rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 flex flex-col items-center justify-center text-center p-6">
                    <p class="text-sm font-semibold text-slate-600">Usulan belum memiliki koordinat.</p>
                    <p class="text-xs text-slate-400 mt-1">Tambahkan koordinat Latitude dan Longitude agar titik dapat dipetakan.</p>
                </div>
            @endif
        </div>
    </div>
</x-card>
