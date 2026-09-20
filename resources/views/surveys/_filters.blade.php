<form method="GET" action="{{ route('surveys.index') }}" class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs space-y-3">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ $isSuperAdmin ? '5' : '4' }} gap-3">
        <!-- Search Input -->
        <div class="{{ $isSuperAdmin ? 'lg:col-span-2' : 'md:col-span-2' }}">
            <label for="search" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                Pencarian
            </label>
            <div class="relative">
                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ $currentSearch }}"
                    placeholder="Cari alamat, kelurahan, atau kecamatan..."
                    class="block w-full rounded-lg border border-slate-300 text-sm px-3.5 py-2 pl-9 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200/60"
                />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        @if ($isSuperAdmin)
        <!-- Filter User (Superadmin Only) -->
        <div>
            <label for="user_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                Penginput
            </label>
            <select
                name="user_id"
                id="user_id"
                class="block w-full rounded-lg border border-slate-300 text-sm px-3 py-2 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200/60"
            >
                <option value="">Semua User</option>
                <option value="legacy" @selected($currentUserId === 'legacy')>Data Lama (Tanpa User)</option>
                @foreach ($userOptions as $u)
                    <option value="{{ $u->id }}" @selected((string)$currentUserId === (string)$u->id)>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <!-- Filter Kecamatan -->
        <div>
            <label for="kecamatan" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                Kecamatan
            </label>
            <select
                name="kecamatan"
                id="kecamatan"
                class="block w-full rounded-lg border border-slate-300 text-sm px-3 py-2 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200/60"
            >
                <option value="">Semua Kecamatan</option>
                @foreach ($kecamatanOptions as $kec)
                    <option value="{{ $kec }}" @selected($currentKecamatan === $kec)>{{ $kec }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter Konektivitas -->
        <div>
            <label for="koneksi" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                Konektivitas
            </label>
            <select
                name="koneksi"
                id="koneksi"
                class="block w-full rounded-lg border border-slate-300 text-sm px-3 py-2 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200/60"
            >
                <option value="">Semua Koneksi</option>
                <option value="fiber" @selected($currentKoneksi === 'fiber')>Fiber Optik</option>
                <option value="4g" @selected($currentKoneksi === '4g')>4G / 5G</option>
                <option value="p2p" @selected($currentKoneksi === 'p2p')>Point to Point (P2P)</option>
            </select>
        </div>
    </div>

    <div class="flex items-center justify-between pt-1 border-t border-slate-100 text-xs">
        <div class="text-slate-500">
            Total hasil: <span class="font-semibold text-slate-800">{{ $surveys->total() }}</span> data
        </div>
        <div class="flex items-center gap-2">
            @if ($currentSearch || $currentKecamatan || $currentKelurahan || $currentKoneksi || $currentUserId)
                <a href="{{ route('surveys.index') }}" class="px-3 py-1.5 text-xs text-slate-600 hover:text-slate-900 hover:underline">
                    Reset Filter
                </a>
            @endif
            <x-button type="submit" variant="primary" size="sm">
                Terapkan Filter
            </x-button>
        </div>
    </div>
</form>
