@extends('layouts.admin')

@section('title', $currentJenis === \App\Models\Usulan::JENIS_WIFI ? 'Tambah Usulan Titik Wifi' : 'Tambah Usulan Titik CCTV')
@section('page_title', 'Tambah Usulan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">
                {{ $currentJenis === \App\Models\Usulan::JENIS_WIFI ? 'Tambah Data Usulan Titik Wifi' : 'Tambah Data Usulan Titik CCTV' }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ $currentJenis === \App\Models\Usulan::JENIS_WIFI ? 'Ajukan permohonan usulan titik baru penyediaan akses Wifi publik untuk lingkungan Anda' : 'Ajukan permohonan usulan titik baru pemasangan kamera CCTV lingkungan Anda' }}
            </p>
        </div>
        <x-button variant="outline" size="sm" :href="route('usulans.index', ['tab' => $currentJenis])">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </x-button>
    </div>

    <!-- Switcher Jenis Usulan (Tab) -->
    <div class="bg-white p-1.5 rounded-xl border border-slate-200 inline-flex items-center gap-1.5 shadow-xs">
        <a
            href="{{ route('usulans.create', ['jenis' => \App\Models\Usulan::JENIS_CCTV]) }}"
            class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold transition {{ $currentJenis === \App\Models\Usulan::JENIS_CCTV ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            Usulan Titik CCTV
        </a>
        <a
            href="{{ route('usulans.create', ['jenis' => \App\Models\Usulan::JENIS_WIFI]) }}"
            class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold transition {{ $currentJenis === \App\Models\Usulan::JENIS_WIFI ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>
            Usulan Titik Wifi
        </a>
    </div>

    <form method="POST" action="{{ route('usulans.store') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="jenis" value="{{ $currentJenis }}">

        @include('usulans.partials._form_lokasi')
        @include('usulans.partials._form_detail')

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
            <x-button variant="outline" size="md" :href="route('usulans.index', ['tab' => $currentJenis])">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Data Usulan {{ $currentJenis === \App\Models\Usulan::JENIS_WIFI ? 'Wifi' : 'CCTV' }}
            </x-button>
        </div>
    </form>
</div>
@endsection
