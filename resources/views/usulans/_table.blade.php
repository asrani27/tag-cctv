<div class="overflow-x-auto">
    <table class="w-full text-left text-xs text-slate-600">
        <thead class="bg-slate-50 text-slate-700 uppercase font-semibold border-b border-slate-200">
            <tr>
                <th class="px-4 py-3 text-center w-12">ID</th>
                <th class="px-4 py-3">Kelurahan / RT</th>
                <th class="px-4 py-3">Ketua RT & HP</th>
                @if ($activeTab === \App\Models\Usulan::JENIS_WIFI)
                    <th class="px-4 py-3">Fasilitas Publik</th>
                    <th class="px-4 py-3">Pengguna / Hari</th>
                    <th class="px-4 py-3">Jaringan Kabel/Tiang</th>
                    <th class="px-4 py-3">Tempat Router/AP</th>
                    <th class="px-3 py-3 text-center">Listrik 24 Jam</th>
                @else
                    <th class="px-4 py-3">Titik Usulan</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Penyangga</th>
                    <th class="px-3 py-3 text-center">Listrik</th>
                    <th class="px-3 py-3 text-center">CCTV</th>
                @endif
                <th class="px-3 py-3 text-center">Peta</th>
                @if ($isSuperAdmin)
                    <th class="px-4 py-3">Pengusul</th>
                @endif
                <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($usulans as $usulan)
                <tr class="hover:bg-slate-50/70 transition">
                    <td class="px-4 py-3.5 text-center font-mono text-slate-400 text-[11px]">#{{ $usulan->id }}</td>
                    <td class="px-4 py-3.5 font-medium text-slate-900">
                        <div class="font-semibold">{{ $usulan->kelurahan ?? '-' }}</div>
                        <span class="text-[11px] text-slate-500">RT {{ $usulan->rt ?? '-' }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="font-medium text-slate-800">{{ $usulan->nama_ketua_rt ?? '-' }}</div>
                        @if ($usulan->no_hp)
                            <div class="text-[11px] font-mono text-emerald-700">{{ $usulan->no_hp }}</div>
                        @endif
                    </td>
                    @if ($activeTab === \App\Models\Usulan::JENIS_WIFI)
                        <td class="px-4 py-3.5">
                            @if ($usulan->nama_fasilitas_publik)
                                <x-badge variant="sky" size="xs">{{ $usulan->nama_fasilitas_publik }}</x-badge>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 font-medium text-slate-700">
                            {{ $usulan->jumlah_pengguna_potensial ? $usulan->jumlah_pengguna_potensial . ' Warga' : '-' }}
                        </td>
                        <td class="px-4 py-3.5 text-slate-600 truncate max-w-[180px]" title="{{ $usulan->akses_jaringan_terdekat }}">
                            {{ $usulan->akses_jaringan_terdekat ?? '-' }}
                        </td>
                        <td class="px-4 py-3.5">
                            @if ($usulan->tempat_pemasangan)
                                <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-medium bg-purple-50 text-purple-700 border border-purple-200">
                                    {{ $usulan->tempat_pemasangan }}
                                </span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-3 py-3.5 text-center">
                            <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $usulan->ketersediaan_listrik === 'Tersedia' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $usulan->ketersediaan_listrik ?? '-' }}
                            </span>
                        </td>
                    @else
                        <td class="px-4 py-3.5">
                            @if ($usulan->nama_lokasi)
                                <x-badge variant="emerald" size="xs">{{ $usulan->nama_lokasi }}</x-badge>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            @if ($usulan->kategori_titik)
                                <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $usulan->kategori_titik }}
                                </span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-slate-700">{{ $usulan->infrastruktur_penyangga ?? '-' }}</td>
                        <td class="px-3 py-3.5 text-center">
                            <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $usulan->sumber_daya_listrik === 'Ada' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                {{ $usulan->sumber_daya_listrik ?? '-' }}
                            </span>
                        </td>
                        <td class="px-3 py-3.5 text-center">
                            <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $usulan->cctv_eksisting === 'Sudah Ada' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">
                                {{ $usulan->cctv_eksisting ?? '-' }}
                            </span>
                        </td>
                    @endif
                    <td class="px-3 py-3.5 text-center">
                        @if ($usulan->hasCoordinates())
                            <span class="text-emerald-700 font-semibold text-xs">📍 Ya</span>
                        @else
                            <span class="text-slate-300 text-xs">-</span>
                        @endif
                    </td>
                    @if ($isSuperAdmin)
                        <td class="px-4 py-3.5 text-slate-800">{{ $usulan->user?->name ?? 'Pengguna' }}</td>
                    @endif
                    <td class="px-4 py-3.5 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('usulans.show', $usulan) }}" class="p-1.5 text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 rounded-md transition" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @can('update', $usulan)
                                <a href="{{ route('usulans.edit', $usulan) }}" class="p-1.5 text-slate-500 hover:text-blue-700 hover:bg-blue-50 rounded-md transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                            @endcan
                            @can('delete', $usulan)
                                <form method="POST" action="{{ route('usulans.destroy', $usulan) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data usulan ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition cursor-pointer" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
