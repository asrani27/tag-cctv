<!-- Bagian 3: Titik Usulan & Kesiapan Teknis Wifi -->
<x-card title="3. Karakteristik Titik & Kesiapan Teknis Wifi" subtitle="Nama fasilitas publik, estimasi pengguna harian, akses kabel/tiang, tempat perangkat, dan daya listrik">
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-select
                name="nama_fasilitas_publik"
                label="Nama Fasilitas Publik Lokasi Pemasangan"
                :value="$usulan->nama_fasilitas_publik ?? null"
                :options="$fasilitasPublikOptions"
                placeholder="-- Pilih Fasilitas Publik --"
                helper="Pilihan: Pos Kamling, Balai RT, Taman Warga, Posyandu"
                required
            />
            <x-input
                name="jumlah_pengguna_potensial"
                label="Perkiraan Jumlah Pengguna Potensial (Warga/Hari)"
                type="number"
                min="1"
                :value="$usulan->jumlah_pengguna_potensial ?? null"
                placeholder="Contoh: 50"
                helper="Estimasi rata-rata jumlah warga pemakai per hari"
            />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-input
                name="akses_jaringan_terdekat"
                label="Akses Jaringan Kabel/Tiang Terdekat"
                :value="$usulan->akses_jaringan_terdekat ?? null"
                placeholder="Contoh: Tiang Telkom jarak 10 meter"
                helper="Kabel FO atau tiang jaringan terdekat"
            />
            <x-select
                name="tempat_pemasangan"
                label="Tempat Pemasangan Perangkat Router/AP"
                :value="$usulan->tempat_pemasangan ?? null"
                :options="$tempatPemasanganOptions"
                placeholder="-- Pilih Tempat Perangkat --"
                helper="Pilihan: Indoor / Outdoor Terlindung"
                required
            />
            <x-select
                name="ketersediaan_listrik"
                label="Ketersediaan Listrik 24 Jam"
                :value="$usulan->ketersediaan_listrik ?? null"
                :options="$ketersediaanListrikOptions"
                placeholder="-- Ketersediaan Listrik --"
                helper="Pilihan: Tersedia / Perlu Tarikan Baru"
                required
            />
        </div>

        @if (isset($statusOptions) && auth()->user()?->isSuperAdmin())
            <div class="pt-2 border-t border-slate-100">
                <x-select
                    name="status"
                    label="Status Pengajuan Usulan"
                    :value="$usulan->status ?? \App\Models\Usulan::STATUS_OPTIONS[0]"
                    :options="$statusOptions"
                    placeholder="-- Pilih Status --"
                    helper="Khusus Administrator / Superadmin"
                />
            </div>
        @endif

        <div class="pt-2">
            <x-textarea
                name="keterangan"
                label="Keterangan Tambahan"
                :value="$usulan->keterangan ?? null"
                placeholder="Tuliskan keterangan detail, pertimbangan kebutuhan publik, atau catatan khusus lapangan..."
                rows="3"
                helper="Informasi tambahan untuk memperkuat telaah usulan titik Wifi publik"
            />
        </div>
    </div>
</x-card>
