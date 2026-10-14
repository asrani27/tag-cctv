@extends('layouts.admin')

@section('title', 'Tambah Usulan Titik CCTV')
@section('page_title', 'Tambah Usulan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Tambah Data Usulan Titik CCTV</h1>
            <p class="text-xs text-slate-500 mt-0.5">Ajukan permohonan usulan titik baru pemasangan kamera CCTV lingkungan Anda</p>
        </div>
        <x-button variant="outline" size="sm" :href="route('usulans.index')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </x-button>
    </div>

    <form method="POST" action="{{ route('usulans.store') }}" class="space-y-6">
        @csrf

        @include('usulans.partials._form_lokasi')
        @include('usulans.partials._form_detail')

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
            <x-button variant="outline" size="md" :href="route('usulans.index')">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Data Usulan
            </x-button>
        </div>
    </form>
</div>
@endsection
