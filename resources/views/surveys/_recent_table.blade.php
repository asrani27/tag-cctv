@if ($recentSurveys->isEmpty())
    <div class="text-center py-10">
        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400 mb-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
        </div>
        <h3 class="text-sm font-semibold text-slate-800">Belum ada data survey.</h3>
        <p class="text-xs text-slate-500 mt-1 mb-4">Mulai tambahkan data titik survey pertama untuk memetakan CCTV & WiFi.</p>
        <x-button variant="primary" size="sm" :href="route('surveys.create')">Tambah Survey</x-button>
    </div>
@else
    <div class="overflow-x-auto -mx-6">
        <table class="w-full text-left text-xs text-slate-600">
            <thead class="bg-slate-50 text-slate-700 uppercase font-semibold border-y border-slate-200">
                <tr>
                    <th class="px-6 py-3">Alamat</th>
                    <th class="px-4 py-3">Kelurahan</th>
                    <th class="px-4 py-3">Kecamatan</th>
                    <th class="px-3 py-3 text-center">CCTV</th>
                    <th class="px-3 py-3 text-center">AP</th>
                    @if (auth()->user()?->isSuperAdmin())
                        <th class="px-4 py-3">Diinput Oleh</th>
                    @endif
                    <th class="px-4 py-3">Latitude</th>
                    <th class="px-4 py-3">Longitude</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-6 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($recentSurveys as $survey)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-6 py-3.5 font-medium text-slate-900 max-w-xs truncate">{{ $survey->alamat ?? '-' }}</td>
                        <td class="px-4 py-3.5">{{ $survey->kelurahan ?? '-' }}</td>
                        <td class="px-4 py-3.5"><x-badge variant="emerald" size="xs">{{ $survey->kecamatan ?? '-' }}</x-badge></td>
                        <td class="px-3 py-3.5 text-center font-semibold text-slate-800">{{ $survey->jumlah_cctv ?? 0 }}</td>
                        <td class="px-3 py-3.5 text-center font-semibold text-slate-800">{{ $survey->jumlah_ap ?? 0 }}</td>
                        @if (auth()->user()?->isSuperAdmin())
                            <td class="px-4 py-3.5">
                                <span class="font-medium text-slate-800">{{ $survey->user?->name ?? 'Data Lama' }}</span>
                                @if ($survey->user?->email)
                                    <span class="block text-[10px] text-slate-400">{{ $survey->user->email }}</span>
                                @endif
                            </td>
                        @endif
                        <td class="px-4 py-3.5 font-mono text-[11px] text-slate-500">{{ $survey->latitude ? number_format($survey->latitude, 6) : '-' }}</td>
                        <td class="px-4 py-3.5 font-mono text-[11px] text-slate-500">{{ $survey->longitude ? number_format($survey->longitude, 6) : '-' }}</td>
                        <td class="px-4 py-3.5 text-slate-400 whitespace-nowrap">{{ $survey->created_at ? $survey->created_at->format('d/m/Y') : '-' }}</td>
                        <td class="px-6 py-3.5 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('surveys.show', $survey) }}" class="p-1.5 text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 rounded-md transition" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('surveys.edit', $survey) }}" class="p-1.5 text-slate-500 hover:text-blue-700 hover:bg-blue-50 rounded-md transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
