@extends('layouts.public')

@section('title', 'Peta Persebaran Titik Survei CCTV & WiFi Kota Banjarmasin')
@section('meta_description', 'Peta interaktif persebaran titik survei lokasi CCTV dan WiFi di Kota Banjarmasin.')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
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
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    {{ $isUser ? 'Peta Sebaran Titik Survei Saya' : 'Peta Sebaran Titik Survei' }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-600">
                    {{ $isUser ? 'Visualisasi titik survei perangkat CCTV dan WiFi yang Anda input.' : 'Visualisasi
                    titik survei perangkat CCTV dan WiFi Kota Banjarmasin.' }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span
                    class="px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    {{ $surveys->count() }} Titik Terpetakan
                </span>
                <a href="{{ auth()->check() ? route('surveys.index') : route('public.surveys.index') }}"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white text-slate-700 border border-slate-300 hover:bg-slate-50">
                    {{ auth()->check() ? 'Kelola Data' : 'Katalog Data' }}
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('map') }}"
            class="mt-4 bg-white p-3 rounded-xl border border-slate-200 flex flex-wrap gap-2 items-center">
            <select name="kecamatan" onchange="this.form.submit()"
                class="text-sm rounded-lg border-slate-300 py-1.5 px-3">
                <option value="">-- Semua Kecamatan --</option>
                @foreach ($kecamatanOptions as $kec)
                <option value="{{ $kec }}" {{ $currentKecamatan===$kec ? 'selected' : '' }}>Kecamatan {{ $kec }}
                </option>
                @endforeach
            </select>
            <select name="kelurahan" onchange="this.form.submit()"
                class="text-sm rounded-lg border-slate-300 py-1.5 px-3" {{ empty($kelurahanOptions) ? 'disabled' : ''
                }}>
                <option value="">-- Semua Kelurahan --</option>
                @foreach ($kelurahanOptions as $kel)
                <option value="{{ $kel }}" {{ $currentKelurahan===$kel ? 'selected' : '' }}>Kelurahan {{ $kel }}
                </option>
                @endforeach
            </select>
            <select name="perangkat" onchange="this.form.submit()"
                class="text-sm rounded-lg border-slate-300 py-1.5 px-3">
                <option value="">-- Semua Perangkat --</option>
                <option value="cctv" {{ ($currentPerangkat ?? '') === 'cctv' ? 'selected' : '' }}>CCTV</option>
                <option value="wifi" {{ ($currentPerangkat ?? '') === 'wifi' ? 'selected' : '' }}>WiFi AP</option>
            </select>
            <select name="status" onchange="this.form.submit()"
                class="text-sm rounded-lg border-slate-300 py-1.5 px-3">
                <option value="">-- Semua Status --</option>
                @foreach ($statusOptions ?? \App\Models\SurveyLocation::STATUS_OPTIONS as $st)
                <option value="{{ $st }}" {{ ($currentStatus ?? '') === $st ? 'selected' : '' }}>{{ $st }}</option>
                @endforeach
            </select>
            <button type="submit"
                class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-semibold">Filter</button>
            @if ($currentKecamatan || $currentKelurahan || ($currentPerangkat ?? '') || ($currentStatus ?? ''))
            <a href="{{ route('map') }}" class="px-2 py-1 text-xs text-slate-500 hover:text-red-600">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div id="banjarmasin-map" class="w-full h-[560px] bg-slate-100 z-0"></div>
        <div class="px-5 py-3 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-600">
            <div class="flex flex-wrap items-center gap-4">
                <span class="font-semibold text-slate-700">Keterangan Status Marker:</span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-red-600 inline-block shadow-xs"></span>
                    <span>Belum Terpasang (Merah)</span>
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-blue-600 inline-block shadow-xs"></span>
                    <span>Proses Pemasangan (Biru)</span>
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-green-600 inline-block shadow-xs"></span>
                    <span>Selesai Dipasang (Hijau)</span>
                </span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
            const locations = @json($surveys);
            const isSuperAdmin = @json($isSuperAdmin);
            const map = L.map('banjarmasin-map').setView([-3.3194, 114.5908], 13);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);

            const esc = (t) => { if (!t) return '-'; const m = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }; return String(t).replace(/[&<>"']/g, (s) => m[s]); };

            const getMarkerColor = (status) => {
                if (status === 'Selesai Dipasang') return '#16a34a';
                if (status === 'Proses Pemasangan') return '#2563eb';
                return '#dc2626';
            };

            const getStatusBadgeClasses = (status) => {
                if (status === 'Selesai Dipasang') return 'bg-emerald-100 text-emerald-800 border-emerald-200';
                if (status === 'Proses Pemasangan') return 'bg-amber-100 text-amber-800 border-amber-200';
                return 'bg-rose-100 text-rose-800 border-rose-200';
            };

            const createColoredIcon = (status) => {
                const color = getMarkerColor(status);
                const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 36" width="26" height="38">
                    <path d="M12 0C5.373 0 0 5.373 0 12c0 8.5 12 24 12 24s12-15.5 12-24c0-6.627-5.373-12-12-12z" fill="${color}" stroke="#ffffff" stroke-width="2"/>
                    <circle cx="12" cy="12" r="5" fill="#ffffff"/>
                </svg>`;
                return L.divIcon({
                    className: 'custom-status-marker',
                    html: svg,
                    iconSize: [26, 38],
                    iconAnchor: [13, 38],
                    popupAnchor: [0, -34],
                });
            };

            const markers = [];
            locations.forEach(item => {
                if (item.latitude && item.longitude) {
                    const lat = parseFloat(item.latitude), lng = parseFloat(item.longitude);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        const statusText = item.status || 'Belum Terpasang';
                        const m = L.marker([lat, lng], { icon: createColoredIcon(statusText) }).addTo(map);
                        markers.push(m);

                        let creatorHtml = '';
                        if (isSuperAdmin && item.diinput_oleh) {
                            creatorHtml = `<div class="mb-2 text-[11px] text-slate-500">Diinput Oleh: <strong class="text-slate-800">${esc(item.diinput_oleh)}</strong></div>`;
                        }

                        const popup = `<div class="min-w-[190px] text-xs">
                            <div class="mb-1.5">
                                <span class="inline-block px-2 py-0.5 rounded border text-[10px] font-semibold ${getStatusBadgeClasses(statusText)}">${esc(statusText)}</span>
                            </div>
                            <div class="font-bold text-slate-900 text-sm mb-1">${esc(item.alamat || 'Titik Survei')}</div>
                            <div class="text-slate-500 mb-2">Kel. ${esc(item.kelurahan)}, Kec. ${esc(item.kecamatan)}</div>
                            ${creatorHtml}
                            <div class="grid grid-cols-2 gap-1 py-1 border-y border-slate-100 mb-2 text-[11px]">
                                <div>CCTV: <strong>${item.jumlah_cctv || 0} unit</strong></div>
                                <div>WiFi AP: <strong>${item.jumlah_ap || 0} unit</strong></div>
                            </div>
                            <a href="${item.detail_url}" style="color: #ffffff;" class="block text-center py-1.5 bg-emerald-600 !text-white hover:!text-white rounded font-semibold text-xs hover:bg-emerald-700 no-underline">Lihat Detail</a>
                        </div>`;
                        m.bindPopup(popup);
                    }
                }
            });
            if (markers.length > 0) { map.fitBounds(new L.featureGroup(markers).getBounds().pad(0.1)); }
        });
</script>
@endpush