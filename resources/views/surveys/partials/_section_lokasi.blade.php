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
                placeholder="Contoh: -3.319400"
                helper="Rentang: -90 sampai 90"
            />
            <x-input
                name="longitude"
                label="Longitude"
                type="number"
                step="any"
                :value="$survey->longitude ?? null"
                placeholder="Contoh: 114.590800"
                helper="Rentang: -180 sampai 180"
            />
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
