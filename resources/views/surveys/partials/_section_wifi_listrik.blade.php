<!-- Section 5: WiFi -->
<x-card title="5. Karakteristik WiFi Publik" subtitle="Cakupan area sinyal dan titik pasang access point">
    <div class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-input
                name="luas_area"
                label="Luas Area (m²)"
                type="number"
                step="any"
                min="0"
                :value="$survey->luas_area ?? null"
                placeholder="Contoh: 250.5"
            />
            <x-input
                name="prakiraan_pengguna_max"
                label="Prakiraan Pengguna Maksimal"
                type="number"
                min="0"
                :value="$survey->prakiraan_pengguna_max ?? null"
                placeholder="Contoh: 100"
            />
            <x-input
                name="potensi_interferensi"
                label="Potensi Interferensi"
                :value="$survey->potensi_interferensi ?? null"
                placeholder="Rendah / Sedang / Banyak SSID Lain"
            />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input
                name="tempat_ap_latitude"
                label="Titik Pasang AP - Latitude"
                type="number"
                step="any"
                :value="$survey->tempat_ap_latitude ?? null"
                placeholder="-3.319400"
            />
            <x-input
                name="tempat_ap_longitude"
                label="Titik Pasang AP - Longitude"
                type="number"
                step="any"
                :value="$survey->tempat_ap_longitude ?? null"
                placeholder="114.590800"
            />
        </div>
    </div>
</x-card>

<!-- Section 6: Ketersediaan Listrik -->
<x-card title="6. Ketersediaan Daya & Listrik" subtitle="Sumber catu daya, panel kontrol, dan proteksi lonjakan">
    <div class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-input
                name="sumber_listrik"
                label="Sumber Listrik"
                :value="$survey->sumber_listrik ?? null"
                placeholder="PLN / Solar Cell / PJU"
            />
            <x-input
                name="daya_tersedia"
                label="Daya Tersedia (VA)"
                type="number"
                step="any"
                min="0"
                :value="$survey->daya_tersedia ?? null"
                placeholder="Contoh: 2200"
            />
            <x-input
                name="proteksi_petir"
                label="Proteksi Petir"
                :value="$survey->proteksi_petir ?? null"
                placeholder="Surge Arrester / Grounding Rod"
            />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input
                name="posisi_panel_latitude"
                label="Posisi Panel - Latitude"
                type="number"
                step="any"
                :value="$survey->posisi_panel_latitude ?? null"
                placeholder="-3.319400"
            />
            <x-input
                name="posisi_panel_longitude"
                label="Posisi Panel - Longitude"
                type="number"
                step="any"
                :value="$survey->posisi_panel_longitude ?? null"
                placeholder="114.590800"
            />
        </div>
    </div>
</x-card>

<!-- Section 7: Komunikasi & Konektivitas -->
<x-card title="7. Jalur Komunikasi & Transmisi" subtitle="Ketersediaan jaringan FO, seluler nirkabel, dan provider">
    <div class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-checkbox
                name="tersedia_fiber_optik"
                label="Tersedia Fiber Optik"
                description="Jalur kabel FO siap sambung"
                :checked="$survey->tersedia_fiber_optik ?? false"
            />
            <x-checkbox
                name="tersedia_4g_5g"
                label="Tersedia Jaringan 4G/5G"
                description="Cakupan sinyal seluler stabil"
                :checked="$survey->tersedia_4g_5g ?? false"
            />
            <x-checkbox
                name="tersedia_link_p2p"
                label="Tersedia Link P2P"
                description="Wireless Point-to-Point LoS"
                :checked="$survey->tersedia_link_p2p ?? false"
            />
        </div>

        <div class="max-w-xs">
            <x-input
                name="jumlah_provider"
                label="Jumlah Provider Tersedia"
                type="number"
                min="0"
                :value="$survey->jumlah_provider ?? null"
                placeholder="0"
                helper="Jumlah ISP/operator di sekitar titik"
            />
        </div>
    </div>
</x-card>
