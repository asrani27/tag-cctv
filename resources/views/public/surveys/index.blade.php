@extends('layouts.public')
@section('title', 'Katalog Data Survei CCTV & WiFi Kota Banjarmasin')

@section('content')
<div class="bg-slate-100 py-6 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="text-xs text-slate-500 mb-1"><a href="{{ route('home') }}" class="hover:text-emerald-600">Beranda</a> / <span>Data Survei</span></nav>
                <h1 class="text-2xl font-extrabold text-slate-900">Katalog Data Survei</h1>
                <p class="text-xs text-slate-600">Hasil survei titik lokasi CCTV dan WiFi Kota Banjarmasin.</p>
            </div>
            <a href="{{ route('map') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-500 self-start">Peta Interaktif</a>
        </div>

        <form method="GET" action="{{ route('public.surveys.index') }}" class="mt-4 bg-white p-3 rounded-xl border border-slate-200 flex flex-wrap gap-2 items-center">
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari alamat..." class="text-xs rounded-lg border-slate-300 py-1.5 px-3">
            <select name="kecamatan" onchange="this.form.submit()" class="text-xs rounded-lg border-slate-300 py-1.5 px-3">
                <option value="">-- Semua Kecamatan --</option>
                @foreach ($kecamatanList as $kec)
                    <option value="{{ $kec }}" {{ $currentKecamatan === $kec ? 'selected' : '' }}>{{ $kec }}</option>
                @endforeach
            </select>
            <select name="kelurahan" onchange="this.form.submit()" class="text-xs rounded-lg border-slate-300 py-1.5 px-3" {{ empty($kelurahanList) ? 'disabled' : '' }}>
                <option value="">-- Semua Kelurahan --</option>
                @foreach ($kelurahanList as $kel)
                    <option value="{{ $kel }}" {{ $currentKelurahan === $kel ? 'selected' : '' }}>{{ $kel }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-3 py-1.5 bg-slate-900 text-white rounded-lg text-xs font-semibold">Cari</button>
            @if ($search || $currentKecamatan || $currentKelurahan)
                <a href="{{ route('public.surveys.index') }}" class="text-xs text-red-600 px-2">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if ($surveys->isEmpty())
        <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center text-slate-500 max-w-md mx-auto">
            <p class="font-bold text-slate-800">Tidak ada data ditemukan.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($surveys as $survey)
                <div class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-md transition flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-start justify-between">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">{{ $survey->kecamatan ?? '-' }}</span>
                            @if ($survey->tersedia_fiber_optik)<span class="text-[11px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">FO Siap</span>@endif
                        </div>
                        <h3 class="font-bold text-slate-900 text-base line-clamp-2">{{ $survey->alamat ?? 'Alamat belum diinput' }}</h3>
                        <p class="text-xs text-slate-500">Kel. {{ $survey->kelurahan ?? '-' }} &bull; RT {{ $survey->rt ?? '-' }}/RW {{ $survey->rw ?? '-' }}</p>
                        <div class="grid grid-cols-2 gap-2 py-2 border-y border-slate-100 text-xs">
                            <div class="bg-slate-50 p-2 rounded"><span class="text-slate-400 block text-[10px]">CCTV</span><strong>{{ $survey->jumlah_cctv ?? 0 }} Unit</strong></div>
                            <div class="bg-slate-50 p-2 rounded"><span class="text-slate-400 block text-[10px]">WiFi AP</span><strong>{{ $survey->jumlah_ap ?? 0 }} Unit</strong></div>
                        </div>
                    </div>
                    <div class="pt-3 flex items-center justify-between border-t border-slate-100 mt-3">
                        <span class="text-[11px] text-slate-400">{{ $survey->hasCoordinates() ? 'Terkoordinat' : 'Manual' }}</span>
                        <a href="{{ route('public.surveys.show', $survey->id) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100">Detail &rarr;</a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-8">{{ $surveys->links() }}</div>
    @endif
</div>
@endsection
