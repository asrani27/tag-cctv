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
