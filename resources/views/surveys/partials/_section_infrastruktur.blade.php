<!-- Section 3: Infrastruktur -->
<x-card title="3. Infrastruktur CCTV & WiFi" subtitle="Spesifikasi perangkat dan kapasitas jaringan">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
        <x-input
            name="jumlah_cctv"
            label="Jumlah CCTV"
            type="number"
            min="0"
            :value="$survey->jumlah_cctv ?? null"
            placeholder="0"
        />
        <x-input
            name="jumlah_ap"
            label="Jumlah AP"
            type="number"
            min="0"
            :value="$survey->jumlah_ap ?? null"
            placeholder="0"
        />
        <x-input
            name="bandwidth"
            label="Bandwidth"
            :value="$survey->bandwidth ?? null"
            placeholder="Contoh: 100 Mbps"
        />
        <x-input
            name="backhaul"
            label="Backhaul"
            :value="$survey->backhaul ?? null"
            placeholder="Contoh: Fiber Optik"
        />
        <x-input
            name="storage"
            label="Storage"
            :value="$survey->storage ?? null"
            placeholder="Contoh: 2 TB NVR"
        />
    </div>
</x-card>

<!-- Section 4: Kondisi Kamera -->
<x-card title="4. Kondisi Kamera & Lingkungan Lokasi" subtitle="Detail fisik tiang, orientasi, pencahayaan dan faktor lingkungan">
    <div class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <x-input
                name="jenis_jalan"
                label="Jenis Jalan"
                :value="$survey->jenis_jalan ?? null"
                placeholder="Protokol, Kolektor, Lingkungan..."
            />
            <x-input
                name="tiang"
                label="Tiang"
                :value="$survey->tiang ?? null"
                placeholder="Ada / Tidak Ada / Rencana"
            />
            <x-input
                name="jenis_tiang"
                label="Jenis Tiang"
                :value="$survey->jenis_tiang ?? null"
                placeholder="Oktagonal 9M, Pipa Besi..."
            />
            <x-input
                name="tembok"
                label="Tembok / Bangunan"
                :value="$survey->tembok ?? null"
                placeholder="Ada / Tidak Ada"
            />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <x-input
                name="arah_pantau"
                label="Arah Pantau"
                :value="$survey->arah_pantau ?? null"
                placeholder="Simpang 4 utara, arah jembatan..."
            />
            <x-input
                name="jarak_pantau"
                label="Jarak Pantau"
                :value="$survey->jarak_pantau ?? null"
                placeholder="Contoh: 50 meter"
            />
            <x-input
                name="pencahayaan_malam"
                label="Pencahayaan Malam"
                :value="$survey->pencahayaan_malam ?? null"
                placeholder="Terang / Cukup / Gelap"
            />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-input
                name="potensi_silau"
                label="Potensi Silau"
                :value="$survey->potensi_silau ?? null"
                placeholder="Rendah / Sedang / Tinggi"
            />
            <x-input
                name="penghalang"
                label="Penghalang / Obstacle"
                :value="$survey->penghalang ?? null"
                placeholder="Pohon rimbun, reklame, kabel..."
            />
            <x-input
                name="potensi_vandalisme"
                label="Potensi Vandalisme"
                :value="$survey->potensi_vandalisme ?? null"
                placeholder="Rendah / Sedang / Tinggi"
            />
        </div>
    </div>
</x-card>
