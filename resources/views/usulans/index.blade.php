@extends('layouts.admin')

@section('title', 'Data Usulan Titik (' . ($activeTab === \App\Models\Usulan::JENIS_WIFI ? 'Wifi' : 'CCTV') . ')')
@section('page_title', 'Data Usulan')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">
                Data Usulan Titik {{ $activeTab === \App\Models\Usulan::JENIS_WIFI ? 'Wifi' : 'CCTV' }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ $isSuperAdmin ? 'Kelola seluruh permohonan dan usulan titik CCTV dan Wifi dari kelurahan / masyarakat' : 'Daftar permohonan usulan titik yang Anda / kelurahan ajukan' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <x-button variant="primary" size="md" :href="route('usulans.create', ['jenis' => $activeTab])">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Usulan {{ $activeTab === \App\Models\Usulan::JENIS_WIFI ? 'Wifi' : 'CCTV' }}
            </x-button>
        </div>
    </div>

    <!-- Tab Navigasi CCTV & Wifi -->
    <div class="border-b border-slate-200">
        <nav class="-mb-px flex space-x-6" aria-label="Tabs">
            <a
                href="{{ route('usulans.index', array_merge(request()->query(), ['tab' => \App\Models\Usulan::JENIS_CCTV, 'page' => 1])) }}"
                class="group inline-flex items-center gap-2 py-3 px-1 border-b-2 text-sm font-semibold transition {{ $activeTab === \App\Models\Usulan::JENIS_CCTV ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}"
            >
                <svg class="w-4 h-4 {{ $activeTab === \App\Models\Usulan::JENIS_CCTV ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                <span>Usulan Titik CCTV</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $activeTab === \App\Models\Usulan::JENIS_CCTV ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                    {{ $countCctv }}
                </span>
            </a>

            <a
                href="{{ route('usulans.index', array_merge(request()->query(), ['tab' => \App\Models\Usulan::JENIS_WIFI, 'page' => 1])) }}"
                class="group inline-flex items-center gap-2 py-3 px-1 border-b-2 text-sm font-semibold transition {{ $activeTab === \App\Models\Usulan::JENIS_WIFI ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}"
            >
                <svg class="w-4 h-4 {{ $activeTab === \App\Models\Usulan::JENIS_WIFI ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>
                <span>Usulan Titik Wifi</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $activeTab === \App\Models\Usulan::JENIS_WIFI ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                    {{ $countWifi }}
                </span>
            </a>
        </nav>
    </div>

    @include('usulans._filters')

    <x-card padding="p-0">
        @if ($usulans->isEmpty())
            <div class="text-center py-12 px-4">
                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400 mb-3">
                    @if ($activeTab === \App\Models\Usulan::JENIS_WIFI)
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>
                    @else
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    @endif
                </div>
                @if ($currentSearch || $currentKelurahan || $currentKategori || $currentNamaLokasi || $currentFasilitasPublik)
                    <h3 class="text-sm font-semibold text-slate-800">Data usulan tidak ditemukan.</h3>
                    <p class="text-xs text-slate-500 mt-1 mb-4">Tidak ada data usulan {{ $activeTab === \App\Models\Usulan::JENIS_WIFI ? 'Wifi' : 'CCTV' }} yang cocok dengan kriteria filter yang dipilih.</p>
                    <x-button variant="outline" size="sm" :href="route('usulans.index', ['tab' => $activeTab])">Reset Filter</x-button>
                @else
                    <h3 class="text-sm font-semibold text-slate-800">Belum ada data usulan {{ $activeTab === \App\Models\Usulan::JENIS_WIFI ? 'Wifi' : 'CCTV' }}.</h3>
                    <p class="text-xs text-slate-500 mt-1 mb-4">
                        {{ $activeTab === \App\Models\Usulan::JENIS_WIFI ? 'Silakan ajukan usulan titik Wifi publik baru untuk lingkungan Anda.' : 'Silakan ajukan usulan titik CCTV baru untuk lingkungan Anda.' }}
                    </p>
                    <x-button variant="primary" size="sm" :href="route('usulans.create', ['jenis' => $activeTab])">
                        Tambah Usulan {{ $activeTab === \App\Models\Usulan::JENIS_WIFI ? 'Wifi' : 'CCTV' }}
                    </x-button>
                @endif
            </div>
        @else
            @include('usulans._table')

            <div class="px-6 py-4 border-t border-slate-200">
                {{ $usulans->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection
