@extends('layouts.admin')

@section('title', 'Data Usulan Titik (' . ($activeTab === \App\Models\Usulan::JENIS_WIFI ? 'Wifi' : 'CCTV') . ')')
@section('page_title', 'Data Usulan')

@section('content')
<div class="space-y-5">
    @if (auth()->user()?->isKelurahan() && auth()->user()?->hasDefaultPassword())
        <div class="p-4 rounded-xl border border-amber-300 bg-amber-50 text-amber-900 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-start gap-3">
                <div class="p-2 rounded-lg bg-amber-100 text-amber-700 shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-amber-900">Perhatian: Wajib Ganti Kata Sandi</h3>
                    <p class="text-xs text-amber-800 mt-0.5">
                        Akun Kelurahan Anda masih menggunakan kata sandi default sistem. Anda tidak dapat melakukan input data usulan baru sebelum mengganti kata sandi akun Anda.
                    </p>
                </div>
            </div>
            <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-600 text-white hover:bg-amber-700 transition shrink-0 shadow-xs">
                <span>Ganti Password Sekarang</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    @endif

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
            @if (auth()->user()?->isKelurahan() && auth()->user()?->hasDefaultPassword())
                <x-button variant="secondary" size="md" :href="route('profile.edit')" class="border-amber-300 bg-amber-50 text-amber-800 hover:bg-amber-100" title="Ganti password terlebih dahulu untuk menambah usulan">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Ganti Password untuk Tambah Usulan
                </x-button>
            @else
                <x-button variant="primary" size="md" :href="route('usulans.create', ['jenis' => $activeTab])">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Usulan {{ $activeTab === \App\Models\Usulan::JENIS_WIFI ? 'Wifi' : 'CCTV' }}
                </x-button>
            @endif

            @can('export', \App\Models\Usulan::class)
            <div class="relative inline-flex" x-data="{ exportMenuOpen: false }">
                <a
                    href="{{ route('usulans.export', array_merge(request()->query(), ['tab' => $activeTab])) }}"
                    id="btn-export-excel"
                    class="inline-flex items-center justify-center font-medium rounded-l-lg transition-all focus:outline-hidden focus:ring-2 focus:ring-offset-1 cursor-pointer text-sm px-4 py-2.5 gap-2 bg-teal-600 text-white hover:bg-teal-700 active:bg-teal-800 focus:ring-teal-500 shadow-xs"
                    onclick="
                        this.classList.add('opacity-75', 'pointer-events-none');
                        this.innerHTML = '<svg class=\'w-4 h-4 animate-spin\' fill=\'none\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\'></circle><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z\'></path></svg> Menyiapkan Excel...';
                        setTimeout(() => { this.classList.remove('opacity-75', 'pointer-events-none'); this.innerHTML = '<svg class=\'w-4 h-4\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\'/></svg> Export Excel'; }, 5000);
                    "
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export Excel
                </a>
                <button
                    type="button"
                    @click="exportMenuOpen = !exportMenuOpen"
                    @click.outside="exportMenuOpen = false"
                    class="inline-flex items-center justify-center px-2 py-2.5 rounded-r-lg border-l border-teal-500 bg-teal-600 text-white hover:bg-teal-700 active:bg-teal-800 focus:outline-hidden cursor-pointer"
                    aria-label="Pilihan Export"
                    title="Pilihan Export"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div
                    x-show="exportMenuOpen"
                    x-transition
                    class="absolute right-0 top-full mt-1.5 w-56 bg-white rounded-xl shadow-lg border border-slate-200 py-1.5 z-50 text-xs"
                    style="display: none;"
                >
                    <a
                        href="{{ route('usulans.export', array_merge(request()->query(), ['tab' => $activeTab])) }}"
                        class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50 transition"
                        @click="exportMenuOpen = false"
                    >
                        <span>Export {{ $activeTab === \App\Models\Usulan::JENIS_WIFI ? 'Wifi' : 'CCTV' }} (Tab Ini)</span>
                    </a>
                    <a
                        href="{{ route('usulans.export', array_merge(request()->query(), ['tab' => $activeTab === \App\Models\Usulan::JENIS_WIFI ? \App\Models\Usulan::JENIS_CCTV : \App\Models\Usulan::JENIS_WIFI])) }}"
                        class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50 transition"
                        @click="exportMenuOpen = false"
                    >
                        <span>Export {{ $activeTab === \App\Models\Usulan::JENIS_WIFI ? 'CCTV' : 'Wifi' }}</span>
                    </a>
                    <div class="my-1 border-t border-slate-100"></div>
                    <a
                        href="{{ route('usulans.export', array_merge(request()->query(), ['tab' => 'all'])) }}"
                        class="flex items-center gap-2 px-3 py-2 text-emerald-700 hover:bg-emerald-50 transition font-medium"
                        @click="exportMenuOpen = false"
                    >
                        <span>Export Semua (CCTV & Wifi)</span>
                    </a>
                </div>
            </div>
            @endcan
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
                    @if (auth()->user()?->isKelurahan() && auth()->user()?->hasDefaultPassword())
                        <x-button variant="primary" size="sm" :href="route('profile.edit')">
                            Ganti Password untuk Tambah Usulan
                        </x-button>
                    @else
                        <x-button variant="primary" size="sm" :href="route('usulans.create', ['jenis' => $activeTab])">
                            Tambah Usulan {{ $activeTab === \App\Models\Usulan::JENIS_WIFI ? 'Wifi' : 'CCTV' }}
                        </x-button>
                    @endif
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
