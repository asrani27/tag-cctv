<!-- Section 1: Informasi Lokasi -->
<x-card title="1. Informasi Lokasi" subtitle="Koordinat dan alamat administratif titik survey">
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-input
                name="latitude"
                label="Latitude"
                type="number"
                step="any"
                :value="$survey->latitude ?? null"
                placeholder="Contoh: -3.3194372"
                helper="Rentang: -90 sampai 90 (dapat diedit manual)"
            />
            <x-input
                name="longitude"
                label="Longitude"
                type="number"
                step="any"
                :value="$survey->longitude ?? null"
                placeholder="Contoh: 114.5908123"
                helper="Rentang: -180 sampai 180 (dapat diedit manual)"
            />
        </div>

        <!-- Tombol Geolocation & Kontrol Lokasi -->
        <div class="space-y-2">
            <div class="flex flex-wrap items-center gap-2.5">
                <button
                    type="button"
                    id="btn-get-location"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white shadow-xs transition-all focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer"
                >
                    <span id="btn-location-spinner" class="hidden">
                        <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </span>
                    <span id="btn-location-icon" class="text-sm leading-none">📍</span>
                    <span id="btn-location-text">Gunakan Lokasi Saya</span>
                </button>

                <button
                    type="button"
                    id="btn-clear-location"
                    class="hidden inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 border border-slate-200 transition-all cursor-pointer"
                    title="Hapus koordinat dan reset marker"
                >
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <span>Reset Koordinat</span>
                </button>
            </div>

            <p class="text-xs text-slate-500">
                Gunakan lokasi perangkat untuk mengisi koordinat secara otomatis. Pastikan izin lokasi pada browser telah diaktifkan.
            </p>

            <!-- Alert Notifikasi Status Lokasi -->
            <div id="location-feedback-alert" class="hidden rounded-xl border p-3.5 text-xs transition-all duration-200">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-2.5">
                        <span id="feedback-alert-icon" class="text-base shrink-0 leading-none mt-0.5"></span>
                        <div>
                            <p id="feedback-alert-title" class="font-semibold"></p>
                            <p id="feedback-alert-desc" class="mt-0.5"></p>
                        </div>
                    </div>
                    <button
                        type="button"
                        id="btn-dismiss-feedback"
                        class="text-slate-400 hover:text-slate-600 rounded p-0.5 transition shrink-0"
                        title="Tutup pemberitahuan"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Peta Interaktif Lokasi Titik Survei -->
        <div class="space-y-1.5 pt-1">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                    Peta Titik Lokasi
                </label>
                <span class="text-[11px] text-slate-500">
                    Klik peta atau geser penanda untuk menyesuaikan titik
                </span>
            </div>

            <div class="relative rounded-xl border border-slate-200 overflow-hidden shadow-inner">
                <div id="survey-form-map" class="h-64 sm:h-80 w-full bg-slate-100 z-0"></div>
                <div class="absolute bottom-2 left-2 z-[500] bg-white/95 backdrop-blur-xs px-2.5 py-1 rounded-md text-[11px] font-mono text-slate-700 border border-slate-200 shadow-xs flex items-center gap-1.5 pointer-events-none">
                    <span id="coords-status-dot" class="w-2 h-2 rounded-full bg-slate-300"></span>
                    <span id="coords-display-text">Belum ada titik dipilih</span>
                </div>
            </div>

            <div class="p-2.5 bg-slate-50 border border-slate-200/80 rounded-lg text-[11px] text-slate-600 flex items-start gap-2">
                <span class="text-emerald-600 font-bold shrink-0">💡</span>
                <div>
                    <strong>3 Cara Penentuan Titik:</strong>
                    (1) Klik <span class="font-semibold text-slate-800">"Gunakan Lokasi Saya"</span> untuk GPS perangkat.
                    (2) <span class="font-semibold text-slate-800">Klik langsung</span> pada peta.
                    (3) <span class="font-semibold text-slate-800">Geser (drag) penanda</span> ke titik yang tepat.
                    Field koordinat di atas akan otomatis tersinkronisasi dan tetap dapat Anda ubah secara manual.
                </div>
            </div>
        </div>

        <x-textarea
            name="alamat"
            label="Alamat Lengkap"
            :value="$survey->alamat ?? null"
            placeholder="Nama jalan, nomor, patokan gedung atau persimpangan..."
            rows="2"
        />

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <x-input
                name="rt"
                label="RT"
                :value="$survey->rt ?? null"
                placeholder="01"
            />
            <x-input
                name="rw"
                label="RW"
                :value="$survey->rw ?? null"
                placeholder="01"
            />
            <x-input
                name="kelurahan"
                label="Kelurahan"
                :value="$survey->kelurahan ?? null"
                placeholder="Contoh: Kertak Baru Ilir"
            />
            <x-select
                name="kecamatan"
                label="Kecamatan"
                :value="$survey->kecamatan ?? null"
                :options="$kecamatanList ?? []"
                placeholder="-- Pilih Kecamatan --"
            />
        </div>
    </div>
</x-card>

<!-- Section 2: Tujuan Layanan -->
<x-card title="2. Tujuan Layanan" subtitle="Pilih peruntukan atau fungsi layanan di lokasi titik survey ini">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <x-checkbox
            name="keamanan"
            label="Keamanan (Kamtibmas)"
            description="Pantauan kamtibmas & pencegahan kriminalitas"
            :checked="$survey->keamanan ?? false"
        />
        <x-checkbox
            name="akses_wifi"
            label="Akses WiFi Publik"
            description="Layanan internet publik bagi masyarakat"
            :checked="$survey->akses_wifi ?? false"
        />
        <x-checkbox
            name="lalin"
            label="Lalu Lintas (Lalin)"
            description="Pemantauan arus kemacetan & simpang"
            :checked="$survey->lalin ?? false"
        />
        <x-checkbox
            name="panic_sensor"
            label="Panic / Sensor"
            description="Tombol darurat dan sensor kebencanaan"
            :checked="$survey->panic_sensor ?? false"
        />
    </div>
</x-card>


@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="{{ asset('js/survey-location-picker.js') }}"></script>
@endpush

