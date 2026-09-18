@extends('layouts.admin')

@section('title', 'Edit Data Survey #' . $survey->id)
@section('page_title', 'Edit Survey')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Edit Data Survey #{{ $survey->id }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Perbarui informasi data survey lapangan titik {{ $survey->alamat ?? 'Lokasi #' . $survey->id }}</p>
        </div>
        <div class="flex items-center gap-2">
            <x-button variant="outline" size="sm" :href="route('surveys.show', $survey)">
                Batal
            </x-button>
            <x-button variant="secondary" size="sm" :href="route('surveys.index')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Daftar Survey
            </x-button>
        </div>
    </div>

    <form method="POST" action="{{ route('surveys.update', $survey) }}" class="space-y-6">
        @csrf
        @method('PUT')

        @include('surveys.partials._section_lokasi')
        @include('surveys.partials._section_infrastruktur')
        @include('surveys.partials._section_wifi_listrik')

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
            <x-button variant="outline" size="md" :href="route('surveys.show', $survey)">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Perbarui Data Survey
            </x-button>
        </div>
    </form>
</div>
@endsection
