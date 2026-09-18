@extends('layouts.public')

@section('title', 'Peta Persebaran Titik Survei CCTV & WiFi Kota Banjarmasin')
@section('meta_description', 'Peta interaktif persebaran titik survei lokasi CCTV dan WiFi di Kota Banjarmasin.')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpush

@section('content')
<div class="bg-slate-100 py-6 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ route('home') }}" class="hover:text-emerald-600">Beranda</a>
                    <span>/</span>
                    <span class="text-slate-700 font-medium">Peta Survei</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Peta Sebaran Titik Survei</h1>
                <p class="text-xs sm:text-sm text-slate-600">Visualisasi titik survei perangkat CCTV dan WiFi Kota Banjarmasin.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    {{ $surveys->count() }} Titik Terpetakan
                </span>
                <a href="{{ route('public.surveys.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white text-slate-700 border border-slate-300 hover:bg-slate-50">
                    Katalog Data
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('map') }}" class="mt-4 bg-white p-3 rounded-xl border border-slate-200 flex flex-wrap gap-2 items-center">
            <select name="kecamatan" onchange="this.form.submit()" class="text-sm rounded-lg border-slate-300 py-1.5 px-3">
                <option value="">-- Semua Kecamatan --</option>
                @foreach ($kecamatanOptions as $kec)
                    <option value="{{ $kec }}" {{ $currentKecamatan === $kec ? 'selected' : '' }}>Kecamatan {{ $kec }}</option>
                @endforeach
            </select>
            <select name="kelurahan" onchange="this.form.submit()" class="text-sm rounded-lg border-slate-300 py-1.5 px-3" {{ empty($kelurahanOptions) ? 'disabled' : '' }}>
                <option value="">-- Semua Kelurahan --</option>
                @foreach ($kelurahanOptions as $kel)
                    <option value="{{ $kel }}" {{ $currentKelurahan === $kel ? 'selected' : '' }}>Kelurahan {{ $kel }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-semibold">Filter</button>
            @if ($currentKecamatan || $currentKelurahan)
                <a href="{{ route('map') }}" class="px-2 py-1 text-xs text-slate-500 hover:text-red-600">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div id="banjarmasin-map" class="w-full h-[560px] bg-slate-100 z-0"></div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const locations = @json($surveys);
            const map = L.map('banjarmasin-map').setView([-3.3194, 114.5908], 13);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);

            const esc = (t) => { if (!t) return '-'; const m = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }; return String(t).replace(/[&<>"']/g, (s) => m[s]); };
            const markers = [];
            locations.forEach(item => {
                if (item.latitude && item.longitude) {
                    const lat = parseFloat(item.latitude), lng = parseFloat(item.longitude);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        const m = L.marker([lat, lng]).addTo(map);
                        markers.push(m);
                        const popup = `<div class="min-w-[190px] text-xs">
                            <div class="font-bold text-slate-900 text-sm mb-1">${esc(item.alamat || 'Titik Survei')}</div>
                            <div class="text-slate-500 mb-2">Kel. ${esc(item.kelurahan)}, Kec. ${esc(item.kecamatan)}</div>
                            <div class="grid grid-cols-2 gap-1 py-1 border-y border-slate-100 mb-2 text-[11px]">
                                <div>CCTV: <strong>${item.jumlah_cctv || 0} unit</strong></div>
                                <div>WiFi AP: <strong>${item.jumlah_ap || 0} unit</strong></div>
                            </div>
                            <a href="/survey/${item.id}" class="block text-center py-1 bg-emerald-600 text-white rounded font-semibold text-xs hover:bg-emerald-700">Lihat Detail</a>
                        </div>`;
                        m.bindPopup(popup);
                    }
                }
            });
            if (markers.length > 0) { map.fitBounds(new L.featureGroup(markers).getBounds().pad(0.1)); }
        });
    </script>
@endpush
