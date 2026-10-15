<x-card title="Karakteristik & Kesiapan Teknis Titik Wifi" subtitle="Fasilitas publik, estimasi pengguna, akses jaringan, penempatan router, dan daya listrik">
    <dl class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
        <div class="p-3 bg-slate-50 rounded-lg">
            <dt class="text-slate-400 font-medium">Fasilitas Publik</dt>
            <dd class="text-sm font-bold text-slate-800 mt-1">{{ $usulan->nama_fasilitas_publik ?? '-' }}</dd>
        </div>
        <div class="p-3 bg-slate-50 rounded-lg">
            <dt class="text-slate-400 font-medium">Pengguna Potensial</dt>
            <dd class="text-sm font-bold text-slate-800 mt-1">
                {{ $usulan->jumlah_pengguna_potensial ? $usulan->jumlah_pengguna_potensial . ' Warga/Hari' : '-' }}
            </dd>
        </div>
        <div class="p-3 bg-slate-50 rounded-lg">
            <dt class="text-slate-400 font-medium">Akses Jaringan Terdekat</dt>
            <dd class="text-sm font-bold text-slate-800 mt-1">{{ $usulan->akses_jaringan_terdekat ?? '-' }}</dd>
        </div>
        <div class="p-3 bg-slate-50 rounded-lg">
            <dt class="text-slate-400 font-medium">Tempat Router / AP</dt>
            <dd class="text-sm font-bold text-slate-800 mt-1">{{ $usulan->tempat_pemasangan ?? '-' }}</dd>
        </div>
        <div class="p-3 bg-slate-50 rounded-lg">
            <dt class="text-slate-400 font-medium">Listrik 24 Jam</dt>
            <dd class="mt-1">
                <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold {{ $usulan->ketersediaan_listrik === 'Tersedia' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $usulan->ketersediaan_listrik ?? '-' }}
                </span>
            </dd>
        </div>
        <div class="p-3 bg-slate-50 rounded-lg">
            <dt class="text-slate-400 font-medium">Pengusul</dt>
            <dd class="text-sm font-bold text-slate-800 mt-1">{{ $usulan->user?->name ?? 'Pengguna' }}</dd>
        </div>
        <div class="p-3 bg-slate-50 rounded-lg sm:col-span-2">
            <dt class="text-slate-400 font-medium">Status Pengajuan</dt>
            <dd class="mt-1">
                <span class="inline-flex px-2.5 py-1 rounded text-xs font-semibold {{ $usulan->status_badge_class }}">
                    {{ $usulan->status }}
                </span>
            </dd>
        </div>
    </dl>

    <div class="mt-4 pt-4 border-t border-slate-100">
        <h4 class="text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Keterangan Tambahan</h4>
        <div class="p-3 bg-white border border-slate-200 rounded-lg text-xs text-slate-800 leading-relaxed min-h-[4rem]">
            {{ $usulan->keterangan ?: 'Tidak ada keterangan tambahan.' }}
        </div>
    </div>
</x-card>
