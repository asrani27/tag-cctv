<div class="overflow-x-auto">
    <table class="w-full text-left text-xs text-slate-600">
        <thead class="bg-slate-50 text-slate-700 uppercase font-semibold border-b border-slate-200">
            <tr>
                <th class="px-4 py-3">Kode Kemendagri</th>
                <th class="px-4 py-3">Nama Kelurahan</th>
                <th class="px-4 py-3">Kecamatan</th>
                <th class="px-4 py-3 text-center">Data Survey</th>
                <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($kelurahans as $item)
                <tr class="hover:bg-slate-50/70 transition">
                    <td class="px-4 py-3.5 whitespace-nowrap">
                        @if ($item->kode)
                            <span class="inline-flex items-center px-2 py-0.5 rounded font-mono text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                {{ $item->kode }}
                            </span>
                        @else
                            <span class="text-slate-400 italic">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="font-semibold text-slate-900 text-sm">{{ $item->name }}</div>
                        <div class="text-[11px] text-slate-400">Kota Banjarmasin</div>
                    </td>
                    <td class="px-4 py-3.5 whitespace-nowrap">
                        @if ($item->kecamatan)
                            <a
                                href="{{ route('kelurahans.index', ['kecamatan_id' => $item->kecamatan_id]) }}"
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition"
                            >
                                {{ $item->kecamatan->name }}
                            </a>
                        @else
                            <span class="text-slate-400 italic">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                            {{ number_format($item->surveys_count) }} Lokasi
                        </span>
                    </td>
                    <td class="px-4 py-3.5 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-1.5">
                            <a
                                href="{{ route('kelurahans.edit', $item) }}"
                                class="p-1.5 text-slate-500 hover:text-blue-700 hover:bg-blue-50 rounded-md transition"
                                title="Edit Kelurahan"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>

                            <form
                                method="POST"
                                action="{{ route('kelurahans.destroy', $item) }}"
                                class="inline"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelurahan {{ $item->name }}?');"
                            >
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition"
                                    title="Hapus Kelurahan"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
