<!-- Bagian 1: Koordinat & Peta Lokasi -->
<x-card title="1. Koordinat & Peta Lokasi" subtitle="Pilih titik koordinat usulan CCTV menggunakan peta Leaflet atau GPS perangkat">
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-input
                name="latitude"
                label="Latitude"
                type="number"
                step="any"
                :value="$usulan->latitude ?? null"
                placeholder="Contoh: -3.3194372"
                helper="Rentang: -90 sampai 90 (dapat diedit manual)"
                required
            />
            <x-input
                name="longitude"
                label="Longitude"
                type="number"
                step="any"
                :value="$usulan->longitude ?? null"
                placeholder="Contoh: 114.5908123"
                helper="Rentang: -180 sampai 180 (dapat diedit manual)"
                required
            />
        </div>

        <!-- Tombol Geolocation & Reset -->
        <div class="space-y-2">
            <div class="flex flex-wrap items-center gap-2.5">
                <button
                    type="button"
                    id="btn-get-location"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white shadow-xs transition-all cursor-pointer"
                >
                    <span id="btn-location-spinner" class="hidden">
                        <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    </span>
                    <span id="btn-location-icon">📍</span>
                    <span id="btn-location-text">Gunakan Lokasi Saya</span>
                </button>

                <button
                    type="button"
                    id="btn-clear-location"
                    class="hidden inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 cursor-pointer"
                >
                    <span>Reset Koordinat</span>
                </button>
            </div>

            <!-- Feedback Alert -->
            <div id="location-feedback-alert" class="hidden rounded-xl border p-3 text-xs">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-2">
                        <span id="feedback-alert-icon"></span>
                        <div>
                            <p id="feedback-alert-title" class="font-semibold"></p>
                            <p id="feedback-alert-desc"></p>
                        </div>
                    </div>
                    <button type="button" id="btn-dismiss-feedback" class="text-slate-400">✕</button>
                </div>
            </div>
        </div>

        <!-- Peta Interaktif Leaflet -->
        <div class="space-y-1.5 pt-1">
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Peta Titik Usulan</label>
            <div class="relative rounded-xl border border-slate-200 overflow-hidden">
                <div id="survey-form-map" class="h-64 sm:h-80 w-full bg-slate-100 z-0"></div>
                <div class="absolute bottom-2 left-2 z-[500] bg-white/95 px-2.5 py-1 rounded text-[11px] font-mono text-slate-700 border shadow-xs pointer-events-none">
                    <span id="coords-status-dot" class="inline-block w-2 h-2 rounded-full bg-slate-300 mr-1"></span>
                    <span id="coords-display-text">Belum ada titik dipilih</span>
                </div>
            </div>
        </div>

        <!-- Wilayah Administratif -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
            <div>
                <x-input
                    name="kelurahan"
                    id="kelurahan"
                    label="Kelurahan"
                    :value="$usulan->kelurahan ?? null"
                    placeholder="Pilih atau ketik kelurahan..."
                    list="kelurahan-suggestions"
                    required
                />
                <datalist id="kelurahan-suggestions">
                    @if (isset($kelurahanMap))
                        @foreach ($kelurahanMap as $kec => $kels)

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="{{ asset('js/survey-location-picker.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const kelInput = document.getElementById('kelurahan');
            const kecSelect = document.getElementById('kecamatan');
            const mapData = @json($kelurahanMap ?? []);
            if (kelInput && kecSelect) {
                kelInput.addEventListener('change', function () {
                    const q = this.value.trim().toLowerCase();
                    if (!q) return;
                    for (const [k, arr] of Object.entries(mapData)) {
                        if (arr.some(item => item.toLowerCase() === q)) {
                            kecSelect.value = k;
                            break;
                        }
                    }
                });
            }
        });
    </script>
@endpush

                            @foreach ($kels as $kel)
                                <option value="{{ $kel }}">{{ $kel }} ({{ $kec }})</option>
                            @endforeach
                        @endforeach
                    @endif
                </datalist>
            </div>
            <div>
                <x-select
                    name="kecamatan"
                    id="kecamatan"
                    label="Kecamatan"
                    :value="$usulan->kecamatan ?? null"
                    :options="$kecamatanList ?? []"
                    placeholder="-- Pilih / Otomatis dari Kelurahan --"
                />
            </div>
        </div>
    </div>
</x-card>
