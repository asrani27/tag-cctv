<x-card title="Informasi Lokasi & Peta" subtitle="Koordinat geografis dan visualisasi titik lokasi">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="space-y-4 text-xs">
            <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-2">
                <div class="flex justify-between">
                    <span class="text-slate-500">Latitude:</span>
                    <span class="font-mono font-semibold text-slate-800">{{ $survey->latitude ? number_format($survey->latitude, 7) : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Longitude:</span>
                    <span class="font-mono font-semibold text-slate-800">{{ $survey->longitude ? number_format($survey->longitude, 7) : '-' }}</span>
                </div>
            </div>

            <div>
                <h4 class="font-semibold text-slate-700 uppercase tracking-wider text-[11px] mb-1">Alamat</h4>
                <p class="text-slate-800 bg-white p-3 rounded-lg border border-slate-200">{{ $survey->alamat ?? '-' }}</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                    <span class="text-slate-400 block text-[11px]">Kecamatan</span>
                    <span class="font-semibold text-slate-800 mt-0.5 block">{{ $survey->kecamatan ?? '-' }}</span>
                </div>
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                    <span class="text-slate-400 block text-[11px]">Kelurahan</span>
                    <span class="font-semibold text-slate-800 mt-0.5 block">{{ $survey->kelurahan ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            @if ($survey->hasCoordinates())
                <div id="mini-map" class="h-64 sm:h-72 w-full rounded-xl border border-slate-200 overflow-hidden shadow-inner"></div>
            @else
                <div class="h-64 sm:h-72 w-full rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 flex flex-col items-center justify-center text-center p-6">
                    <p class="text-sm font-semibold text-slate-600">Lokasi belum memiliki koordinat.</p>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm">Tambahkan koordinat Latitude dan Longitude agar titik dapat dipetakan.</p>
                </div>
            @endif
        </div>
    </div>
</x-card>
