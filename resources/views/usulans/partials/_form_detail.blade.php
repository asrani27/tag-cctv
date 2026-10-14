<!-- Bagian 2: RT & Pengurus Lingkungan -->
<x-card title="2. Informasi RT & Pengurus" subtitle="Rincian nomor RT dan data kontak Ketua RT setempat">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-input
            name="rt"
            label="Nomor RT"
            :value="$usulan->rt ?? null"
            placeholder="Contoh: 01"
            helper="Nomor RT lokasi usulan"
            required
        />
        <x-input
            name="nama_ketua_rt"
            label="Nama Ketua RT"
            :value="$usulan->nama_ketua_rt ?? null"
            placeholder="Nama lengkap Ketua RT"
        />
        <x-input
            name="no_hp"
            label="Nomor HP / WhatsApp"
            :value="$usulan->no_hp ?? null"
            placeholder="Contoh: 08123456789"
            helper="Nomor aktif untuk konfirmasi survey"
        />
    </div>
</x-card>

<!-- Bagian 3: Titik Usulan & Kondisi Lapangan -->
<x-card title="3. Karakteristik Titik & Kesiapan Teknis" subtitle="Kategori usulan, ketersediaan tiang penyangga, daya listrik, dan CCTV eksisting">
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-select
                name="nama_lokasi"
                label="Nama Lokasi / Titik Usulan"
                :value="$usulan->nama_lokasi ?? null"
                :options="$namaLokasiOptions"
                placeholder="-- Pilih Jenis Lokasi --"
                helper="Pilihan: Gang, Jalan, atau Fasum"
                required
            />
            <x-select
                name="kategori_titik"
                label="Kategori Titik"
                :value="$usulan->kategori_titik ?? null"
                :options="$kategoriTitikOptions"
                placeholder="-- Pilih Kategori Titik --"
                helper="Pilihan: Persimpangan, Pintu Masuk RT, Rawan Kriminalitas, Fasos-Fasum"
                required
            />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-select
                name="infrastruktur_penyangga"
                label="Infrastruktur Penyangga"
                :value="$usulan->infrastruktur_penyangga ?? null"
                :options="$infrastrukturOptions"
                placeholder="-- Pilih Penyangga --"
                helper="Tiang PLN, Telkom, Poskamling, dll"
            />
            <x-select
                name="sumber_daya_listrik"
                label="Sumber Daya Listrik"
                :value="$usulan->sumber_daya_listrik ?? null"
                :options="$sumberListrikOptions"
                placeholder="-- Ketersediaan Listrik --"
                helper="Pilihan: Ada / Tidak"
            />
            <x-select
                name="cctv_eksisting"
                label="Ketersediaan CCTV Eksisting"
                :value="$usulan->cctv_eksisting ?? null"
                :options="$cctvEksistingOptions"
                placeholder="-- CCTV Eksisting --"
                helper="Pilihan: Sudah Ada / Belum"
            />
        </div>

        @if (isset($statusOptions) && auth()->user()->isSuperAdmin())
            <div class="pt-2 border-t border-slate-100">
                <x-select
                    name="status"
                    label="Status Pengajuan Usulan"
                    :value="$usulan->status ?? \App\Models\Usulan::STATUS_DIAJUKAN"
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
                placeholder="Tuliskan keterangan detail, pertimbangan keamanan, atau catatan khusus lapangan..."
                rows="3"
                helper="Informasi tambahan untuk memperkuat telaah usulan titik CCTV"
            />
        </div>
    </div>
</x-card>
