<section id="survei-terbaru" class="py-16 sm:py-20 bg-white border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Pembaruan Lapangan</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                    Data Survei Terbaru
                </h2>
                <p class="text-sm sm:text-base text-slate-600 mt-1 max-w-xl">
                    Informasi titik lokasi survei infrastruktur yang tercatat di sistem informasi.
                </p>
            </div>
            <div>
                <a href="{{ route('public.surveys.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-800">
                    Lihat Semua Data Survei
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                </a>
            </div>
        </div>

        @if ($recentSurveys->isEmpty())
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-12 text-center text-slate-500">
                <svg class="w-12 h-12 mx-auto text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                <p class="font-medium text-slate-700">Belum ada data survei yang tersedia.</p>
                <p class="text-xs text-slate-400 mt-1">Data hasil observasi lapangan akan ditampilkan di sini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($recentSurveys as $survey)
                    <div class="bg-white rounded-xl border border-slate-200 p-5 hover:border-emerald-300 hover:shadow-md transition flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-700">
                                    {{ $survey->kecamatan ?? 'Kecamatan -' }}
                                </span>
                                @if ($survey->tersedia_fiber_optik)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> FO Siap
                                    </span>
                                @endif
                            </div>

                            <div>
                                <h3 class="font-bold text-slate-900 text-base line-clamp-2" title="{{ $survey->alamat }}">
                                    {{ $survey->alamat ?? 'Alamat belum diinput' }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-1">
                                    Kel. {{ $survey->kelurahan ?? '-' }}
                                    @if($survey->rt || $survey->rw)
                                        &bull; RT {{ $survey->rt ?? '-' }}/RW {{ $survey->rw ?? '-' }}
                                    @endif
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-2 py-3 border-y border-slate-100 text-xs">
                                <div class="bg-slate-50 p-2 rounded-lg">
                                    <span class="text-slate-400 block text-[10px] uppercase">Rencana CCTV</span>
                                    <span class="font-bold text-slate-800 text-sm">{{ $survey->jumlah_cctv ?? 0 }} Unit</span>
                                </div>
                                <div class="bg-slate-50 p-2 rounded-lg">
                                    <span class="text-slate-400 block text-[10px] uppercase">Rencana WiFi</span>
                                    <span class="font-bold text-slate-800 text-sm">{{ $survey->jumlah_ap ?? 0 }} AP</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 flex items-center justify-between mt-2">
                            <div class="text-[11px] text-slate-400">
                                @if($survey->hasCoordinates())
                                    <span class="text-emerald-600 font-medium">&bull; Terkoordinat</span>
                                @else
                                    <span class="text-slate-400">&bull; Titik manual</span>
                                @endif
                            </div>
                            <a href="{{ route('public.surveys.show', $survey->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition">
                                Lihat Detail
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
