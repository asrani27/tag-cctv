@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-emerald-800 to-teal-700 rounded-2xl p-6 text-white shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight">Survey CCTV & WiFi Banjarmasin</h1>
            <p class="text-emerald-100 text-xs md:text-sm mt-1">Monitoring dan inventarisasi infrastruktur CCTV publik dan access point WiFi Kota Banjarmasin</p>
        </div>
        <div class="flex items-center gap-2.5 shrink-0">
            <x-button variant="outline" size="sm" :href="route('surveys.create')" class="!bg-white !text-emerald-800 hover:!bg-emerald-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Survey
            </x-button>
            <x-button variant="secondary" size="sm" :href="route('map')" class="!bg-emerald-900/60 !text-white hover:!bg-emerald-900 border-emerald-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                Buka Peta
            </x-button>
        </div>
    </div>

    <!-- 5 Stat Cards Required -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Total Survey</span>
            <p class="text-2xl font-bold text-slate-900 mt-2">{{ number_format($totalSurvey) }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Titik Lokasi Terdata</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Total CCTV</span>
            <p class="text-2xl font-bold text-slate-900 mt-2">{{ number_format($totalCctv) }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Unit Kamera</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Total AP</span>
            <p class="text-2xl font-bold text-slate-900 mt-2">{{ number_format($totalAp) }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Access Point WiFi</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Kecamatan</span>
            <p class="text-2xl font-bold text-slate-900 mt-2">{{ number_format($totalKecamatan) }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Kecamatan Aktif</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Kelurahan</span>
            <p class="text-2xl font-bold text-slate-900 mt-2">{{ number_format($totalKelurahan) }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Kelurahan Aktif</p>
        </div>
    </div>

    <!-- Recent Surveys Card -->
    <x-card title="Survey Terbaru" subtitle="Daftar 5 data survey titik lokasi CCTV & WiFi terakhir">
        <x-slot:actions>
            <a href="{{ route('surveys.index') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                Semua Survey
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </x-slot:actions>

        @include('surveys._recent_table')
    </x-card>
</div>
@endsection
