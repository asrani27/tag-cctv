@extends('layouts.admin')

@section('title', 'Tambah Survey Baru')
@section('page_title', 'Tambah Survey')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Tambah Data Survey CCTV & WiFi</h1>
            <p class="text-xs text-slate-500 mt-0.5">Isi seluruh informasi survey lapangan sesuai data observasi teknis</p>
        </div>
        <x-button variant="outline" size="sm" :href="route('surveys.index')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </x-button>
    </div>

    <form method="POST" action="{{ route('surveys.store') }}" class="space-y-6">
        @csrf

        @include('surveys.partials._section_lokasi')
        @include('surveys.partials._section_infrastruktur')
        @include('surveys.partials._section_wifi_listrik')
        @include('surveys.partials._section_photos')

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
            <x-button variant="outline" size="md" :href="route('surveys.index')">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Data Survey
            </x-button>
        </div>
    </form>
</div>
@endsection
