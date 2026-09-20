@extends('layouts.admin')

@section('title', $isSuperAdmin ? 'Semua Data Survey' : 'Data Survey Saya')
@section('page_title', $isSuperAdmin ? 'Data Survey CCTV & WiFi' : 'Data Survey Saya')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">{{ $isSuperAdmin ? 'Daftar Semua Data Survey' : 'Daftar Data Survey Saya' }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ $isSuperAdmin ? 'Kelola seluruh titik lokasi survey kamera CCTV dan akses point WiFi Kota Banjarmasin' : 'Kelola titik lokasi survey kamera CCTV dan akses point WiFi yang Anda input' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <x-button variant="primary" size="md" :href="route('surveys.create')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Survey
            </x-button>
        </div>
    </div>

    @include('surveys._filters')

    <x-card padding="p-0">
        @if ($surveys->isEmpty())
            <div class="text-center py-12 px-4">
                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                @if ($currentSearch || $currentKecamatan || $currentKoneksi || $currentUserId)
                    <h3 class="text-sm font-semibold text-slate-800">Data survey tidak ditemukan.</h3>
                    <p class="text-xs text-slate-500 mt-1 mb-4">Tidak ada data yang cocok dengan kriteria pencarian atau filter yang dipilih.</p>
                    <x-button variant="outline" size="sm" :href="route('surveys.index')">Reset Filter</x-button>
                @else
                    <h3 class="text-sm font-semibold text-slate-800">Belum ada data survey.</h3>
                    <p class="text-xs text-slate-500 mt-1 mb-4">Silakan mulai tambahkan data survey baru ke dalam sistem.</p>
                    <x-button variant="primary" size="sm" :href="route('surveys.create')">Tambah Survey</x-button>
                @endif
            </div>
        @else
            @include('surveys._table')

            <div class="px-6 py-4 border-t border-slate-200">
                {{ $surveys->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection
