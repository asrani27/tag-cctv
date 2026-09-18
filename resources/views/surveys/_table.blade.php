<div class="overflow-x-auto">
    <table class="w-full text-left text-xs text-slate-600">
        <thead class="bg-slate-50 text-slate-700 uppercase font-semibold border-b border-slate-200">
            <tr>
                <th class="px-4 py-3 text-center w-12">ID</th>
                <th class="px-4 py-3 min-w-[200px]">Alamat</th>
                <th class="px-4 py-3">Kelurahan</th>
                <th class="px-4 py-3">Kecamatan</th>
                <th class="px-3 py-3 text-center">CCTV</th>
                <th class="px-3 py-3 text-center">AP</th>
                <th class="px-3 py-3">Bandwidth</th>
                <th class="px-3 py-3">Latitude</th>
                <th class="px-3 py-3">Longitude</th>
                <th class="px-4 py-3">Created At</th>
                <th class="px-4 py-3 text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($surveys as $survey)
                <tr class="hover:bg-slate-50/70 transition">
                    <td class="px-4 py-3.5 text-center font-mono text-slate-400 text-[11px]">#{{ $survey->id }}</td>
                    <td class="px-4 py-3.5 font-medium text-slate-900">
                        <div class="line-clamp-2">{{ $survey->alamat ?? '-' }}</div>
                        @if ($survey->rt || $survey->rw)
                            <span class="text-[10px] text-slate-400">RT {{ $survey->rt ?? '-' }} / RW {{ $survey->rw ?? '-' }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5">{{ $survey->kelurahan ?? '-' }}</td>
                    <td class="px-4 py-3.5">
                        <x-badge variant="emerald" size="xs">{{ $survey->kecamatan ?? '-' }}</x-badge>
                    </td>
                    <td class="px-3 py-3.5 text-center font-semibold text-slate-800">{{ $survey->jumlah_cctv ?? 0 }}</td>
                    <td class="px-3 py-3.5 text-center font-semibold text-slate-800">{{ $survey->jumlah_ap ?? 0 }}</td>
                    <td class="px-3 py-3.5 font-mono text-[11px] text-slate-600">{{ $survey->bandwidth ?? '-' }}</td>
                    <td class="px-3 py-3.5 font-mono text-[11px] text-slate-500">{{ $survey->latitude ? number_format($survey->latitude, 6) : '-' }}</td>
                    <td class="px-3 py-3.5 font-mono text-[11px] text-slate-500">{{ $survey->longitude ? number_format($survey->longitude, 6) : '-' }}</td>
                    <td class="px-4 py-3.5 text-slate-400 whitespace-nowrap">{{ $survey->created_at ? $survey->created_at->format('d/m/Y') : '-' }}</td>
                    <td class="px-4 py-3.5 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('surveys.show', $survey) }}" class="p-1.5 text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 rounded-md transition" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('surveys.edit', $survey) }}" class="p-1.5 text-slate-500 hover:text-blue-700 hover:bg-blue-50 rounded-md transition" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('surveys.destroy', $survey) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data survey ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition cursor-pointer" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
