<x-card padding="p-4">
    <form method="GET" action="{{ route('usulans.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <label for="search" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Pencarian
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ $currentSearch }}"
                        placeholder="Cari Ketua RT, No HP, Kelurahan, Keterangan..."
                        class="block w-full pl-9 pr-3 py-2 text-xs rounded-lg border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200/60 transition"
                    />
                </div>
            </div>

            <div>
                <label for="kelurahan" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Kelurahan
                </label>
                <select
                    name="kelurahan"
                    id="kelurahan"
                    class="block w-full py-2 px-3 text-xs rounded-lg border border-slate-300 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200/60 transition"
                >
                    <option value="">Semua Kelurahan</option>
                    @foreach ($kelurahanOptions as $kel)
                        <option value="{{ $kel }}" @selected($currentKelurahan === $kel)>{{ $kel }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="kategori_titik" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Kategori Titik
                </label>
                <select
                    name="kategori_titik"
                    id="kategori_titik"
                    class="block w-full py-2 px-3 text-xs rounded-lg border border-slate-300 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200/60 transition"
                >
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoriOptions as $kat)
                        <option value="{{ $kat }}" @selected($currentKategori === $kat)>{{ $kat }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="nama_lokasi" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Jenis Lokasi
                </label>
                <select
                    name="nama_lokasi"
                    id="nama_lokasi"
                    class="block w-full py-2 px-3 text-xs rounded-lg border border-slate-300 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200/60 transition"
                >
                    <option value="">Semua Jenis</option>
                    @foreach ($namaLokasiOptions as $loc)
                        <option value="{{ $loc }}" @selected($currentNamaLokasi === $loc)>{{ $loc }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-100">
            <div class="text-xs text-slate-500">
                Total: <strong class="text-slate-800">{{ $usulans->total() }}</strong> usulan
            </div>
            <div class="flex items-center gap-2">
                @if ($currentSearch || $currentKelurahan || $currentKategori || $currentNamaLokasi || $currentStatus || $currentUserId)
                    <a
                        href="{{ route('usulans.index') }}"
                        class="px-3 py-1.5 text-xs font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition"
                    >
                        Reset Filter
                    </a>
                @endif
                <button
                    type="submit"
                    class="px-4 py-1.5 text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-lg transition cursor-pointer"
                >
                    Terapkan Filter
                </button>
            </div>
        </div>
    </form>
</x-card>
