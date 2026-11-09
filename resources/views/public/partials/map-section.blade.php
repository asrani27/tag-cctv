<section id="peta" class="py-16 sm:py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Peta Interaktif</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                    Persebaran Titik Survei
                </h2>
                <p class="text-sm sm:text-base text-slate-600 mt-1 max-w-2xl">
                    Visualisasi titik lokasi survei lapangan yang telah diverifikasi koordinatnya di Kota Banjarmasin.
                </p>
            </div>
            <div>
                <a href="{{ route('map') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm transition">
                    Lihat Peta Lengkap
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div id="landing-map" class="w-full h-[460px] sm:h-[520px] bg-slate-100 z-0"></div>
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500">
                <div class="flex flex-wrap items-center gap-4">
                    <span class="font-medium text-slate-700">Total Terkoordinat ({{ $mapSurveys->count() }})</span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span> Belum Terpasang
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span> Proses Pemasangan
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-green-600"></span> Selesai Dipasang
                    </span>
                </div>
                <span>Pusat: Kota Banjarmasin (-3.3194, 114.5908)</span>
            </div>
        </div>
    </div>
</section>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapElement = document.getElementById('landing-map');
            if (!mapElement) return;

            const map = L.map('landing-map', { scrollWheelZoom: false }).setView([-3.3194, 114.5908], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            const escapeHtml = (text) => {
                if (!text) return '-';
                const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
                return String(text).replace(/[&<>"']/g, (m) => map[m]);
            };

            const surveys = @json($mapSurveys);
            const bounds = [];

            const getMarkerColor = (status) => {
                if (status === 'Selesai Dipasang') return '#16a34a';
                if (status === 'Proses Pemasangan') return '#f97316';
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

            surveys.forEach(survey => {
                if (survey.latitude && survey.longitude) {
                    const lat = parseFloat(survey.latitude);
                    const lng = parseFloat(survey.longitude);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        bounds.push([lat, lng]);
                        const statusText = survey.status || 'Belum Terpasang';
                        const marker = L.marker([lat, lng], { icon: createColoredIcon(statusText) }).addTo(map);
                        const popupContent = `
                            <div class="p-1 text-slate-800 font-sans text-xs min-w-[200px]">
                                <div class="mb-1.5">
                                    <span class="inline-block px-2 py-0.5 rounded border text-[10px] font-semibold ${getStatusBadgeClasses(statusText)}">${escapeHtml(statusText)}</span>
                                </div>
                                <div class="font-bold text-sm text-slate-900 mb-1">${escapeHtml(survey.alamat || 'Titik Survei')}</div>
                                <div class="text-slate-500 mb-2">Kel. ${escapeHtml(survey.kelurahan || '-')}, Kec. ${escapeHtml(survey.kecamatan || '-')}</div>
                                <div class="grid grid-cols-2 gap-1.5 py-1.5 border-t border-slate-100 text-[11px] mb-2">
                                    <div><span class="text-slate-400">CCTV:</span> <strong>${survey.jumlah_cctv ?? 0}</strong></div>
                                    <div><span class="text-slate-400">AP:</span> <strong>${survey.jumlah_ap ?? 0}</strong></div>
                                </div>
                                <a href="/survey/${survey.id}" style="color: #ffffff;" class="block text-center py-1.5 px-2 rounded bg-emerald-600 !text-white hover:!text-white font-semibold text-xs hover:bg-emerald-700 no-underline">Lihat Detail</a>
                            </div>
                        `;
                        marker.bindPopup(popupContent);
                    }
                }
            });

            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
            }
        });
    </script>
@endpush
