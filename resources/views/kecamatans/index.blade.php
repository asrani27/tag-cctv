@extends('layouts.admin')

@section('title', 'Data Kecamatan')
@section('page_title', 'Data Kecamatan')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Manajemen Wilayah</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola data master Kecamatan dan Kelurahan beserta kode Kemendagri Kota Banjarmasin</p>
        </div>
        <div>
            <x-button variant="primary" size="md" :href="route('kecamatans.create')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Kecamatan
            </x-button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-slate-200">
        <nav class="-mb-px flex space-x-6 text-sm font-medium">
            <a
                href="{{ route('kecamatans.index') }}"
                class="py-3 px-1 border-b-2 flex items-center gap-2 border-emerald-600 text-emerald-600 font-semibold"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>Kecamatan</span>
                <span class="px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-800">
                    {{ $kecamatans->total() }}
                </span>
            </a>
            <a
                href="{{ route('kelurahans.index') }}"
                class="py-3 px-1 border-b-2 flex items-center gap-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Kelurahan</span>
                <span class="px-2 py-0.5 rounded-full text-xs bg-slate-100 text-slate-600">
                    {{ $totalKelurahan }}
                </span>
            </a>
        </nav>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('kecamatans.index') }}" class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2">
                <label for="search" class="block text-xs font-semibold text-slate-700 uppercase mb-1">Pencarian</label>
                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari nama kecamatan atau kode Kemendagri..."
                    class="block w-full rounded-lg border border-slate-300 text-sm px-3.5 py-2 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200/60"
                />
            </div>
            <div class="flex items-end gap-2">
                <x-button type="submit" variant="primary" size="md" class="w-full sm:w-auto">Cari</x-button>
                @if ($search)
                    <a href="{{ route('kecamatans.index') }}" class="px-3 py-2 text-xs font-medium text-slate-600 hover:text-slate-900 border border-slate-300 rounded-lg hover:bg-slate-50 transition">Reset</a>
                @endif
            </div>
        </div>
    </form>

    <!-- Table Card -->
    <x-card padding="p-0">
        @if ($kecamatans->isEmpty())
            <div class="text-center py-12 px-4">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <h3 class="text-sm font-semibold text-slate-800">Tidak ada data kecamatan</h3>
                <p class="text-xs text-slate-500 mt-1 mb-4">Kecamatan yang cocok dengan pencarian tidak ditemukan.</p>
                <x-button variant="outline" size="sm" :href="route('kecamatans.index')">Reset Filter</x-button>
            </div>
        @else
            @include('kecamatans._table')

            <div class="px-6 py-4 border-t border-slate-200">
                {{ $kecamatans->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection
