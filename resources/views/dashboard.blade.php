@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-emerald-800 to-teal-700 rounded-2xl p-6 text-white shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            @if ($isSuperAdmin)
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-700/80 text-emerald-100 mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
                    Panel Superadmin
                </div>
                <h1 class="text-xl md:text-2xl font-bold tracking-tight">Survey CCTV & WiFi Banjarmasin</h1>
                <p class="text-emerald-100 text-xs md:text-sm mt-1">Monitoring dan inventarisasi seluruh data infrastruktur CCTV publik dan access point WiFi Kota Banjarmasin</p>
            @else
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-700/80 text-emerald-100 mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
                    Surveyor Lapangan
                </div>
                <h1 class="text-xl md:text-2xl font-bold tracking-tight">Selamat datang, {{ auth()->user()->name }}</h1>
                <p class="text-emerald-100 text-xs md:text-sm mt-1">Dashboard pemantauan dan inventarisasi data survey CCTV dan access point WiFi milik Anda</p>
            @endif
        </div>
        <div class="flex items-center gap-2.5 shrink-0">
            <x-button variant="outline" size="sm" :href="route('surveys.create')" class="!bg-white !text-emerald-800 hover:!bg-emerald-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Survey
            </x-button>
            <x-button variant="secondary" size="sm" :href="route('map')" class="!bg-emerald-900/60 !text-white hover:!bg-emerald-900 border-emerald-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                {{ $isSuperAdmin ? 'Buka Peta' : 'Peta Data Saya' }}
            </x-button>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 {{ $isSuperAdmin ? 'lg:grid-cols-7' : 'lg:grid-cols-6' }} gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Total Survey</span>
            <p class="text-2xl font-bold text-slate-900 mt-2">{{ number_format($totalSurvey) }}</p>
            <p class="text-[11px] text-slate-400 mt-1">{{ $isSuperAdmin ? 'Titik Lokasi Terdata' : 'Survey Saya' }}</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Total CCTV</span>
            <p class="text-2xl font-bold text-slate-900 mt-2">{{ number_format($totalCctv) }}</p>
            <p class="text-[11px] text-slate-400 mt-1">{{ $isSuperAdmin ? 'Unit Kamera' : 'CCTV Saya' }}</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Total AP</span>
            <p class="text-2xl font-bold text-slate-900 mt-2">{{ number_format($totalAp) }}</p>
            <p class="text-[11px] text-slate-400 mt-1">{{ $isSuperAdmin ? 'Access Point WiFi' : 'WiFi/AP Saya' }}</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Foto</span>
            <p class="text-2xl font-bold text-slate-900 mt-2">{{ number_format($totalPhotos) }}</p>
            <p class="text-[11px] text-slate-400 mt-1">{{ $isSuperAdmin ? 'Total Dokumentasi' : 'Foto Saya' }}</p>
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

        @if ($isSuperAdmin)
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Total User</span>
                <p class="text-2xl font-bold text-emerald-700 mt-2">{{ number_format($totalUsers ?? 0) }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Pengguna Terdaftar</p>
            </div>
        @endif
    </div>

    <!-- Recent Surveys Card -->
    <x-card :title="$isSuperAdmin ? 'Survey Terbaru' : 'Survey Terbaru Saya'" :subtitle="$isSuperAdmin ? 'Daftar 5 data survey titik lokasi CCTV & WiFi terakhir dari semua user' : 'Daftar 5 data survey titik lokasi CCTV & WiFi terakhir yang Anda input'">
        <x-slot:actions>
            <a href="{{ route('surveys.index') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                {{ $isSuperAdmin ? 'Semua Survey' : 'Data Saya' }}
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </x-slot:actions>

        @include('surveys._recent_table')
    </x-card>

</div>
@endsection
