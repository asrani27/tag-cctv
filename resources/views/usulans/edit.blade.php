@extends('layouts.admin')

@section('title', 'Edit Data Usulan Titik CCTV')
@section('page_title', 'Edit Usulan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Edit Data Usulan Titik CCTV</h1>
            <p class="text-xs text-slate-500 mt-0.5">Perbarui rincian data usulan titik #{{ $usulan->id }}</p>
        </div>
        <div class="flex items-center gap-2">
            <x-button variant="outline" size="sm" :href="route('usulans.show', $usulan)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Detail
            </x-button>
            <x-button variant="outline" size="sm" :href="route('usulans.index')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </x-button>
        </div>
    </div>

    <form method="POST" action="{{ route('usulans.update', $usulan) }}" class="space-y-6">
        @csrf
        @method('PUT')

        @include('usulans.partials._form_lokasi')
        @include('usulans.partials._form_detail')

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
            <x-button variant="outline" size="md" :href="route('usulans.show', $usulan)">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Perbarui Data Usulan
            </x-button>
        </div>
    </form>
</div>
@endsection
