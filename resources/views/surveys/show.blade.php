@extends('layouts.admin')

@section('title', 'Detail Survey #' . $survey->id)
@section('page_title', 'Detail Survey')

@push('styles')
@if ($survey->hasCoordinates())
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endif
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <x-badge variant="emerald" size="sm">ID #{{ $survey->id }}</x-badge>
                @if ($survey->kecamatan)
                    <x-badge variant="slate" size="sm">{{ $survey->kecamatan }}</x-badge>
                @endif
            </div>
            <h1 class="text-xl font-bold text-slate-900">{{ $survey->alamat ?? 'Survey #' . $survey->id }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelurahan {{ $survey->kelurahan ?? '-' }} &bull; Tanggal {{ $survey->created_at ? $survey->created_at->format('d/m/Y') : '-' }}</p>
        </div>

        <div class="flex items-center gap-2">
            <x-button variant="outline" size="sm" :href="route('surveys.index')">
                Kembali
            </x-button>
            @can('update', $survey)
                <x-button variant="primary" size="sm" :href="route('surveys.edit', $survey)">
                    Edit
                </x-button>
            @endcan
            @can('delete', $survey)
                <form method="POST" action="{{ route('surveys.destroy', $survey) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data survey ini?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" variant="danger" size="sm">
                        Hapus
                    </x-button>
                </form>
            @endcan
        </div>
    </div>

    <!-- Metadata Pendataan -->
    <x-card title="Informasi Pendataan" subtitle="Metadata riwayat pembuatan dan kepemilikan survey">
        <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div class="p-3 bg-slate-50 rounded-lg">
                <dt class="text-slate-400 font-medium">ID Survey</dt>
                <dd class="text-sm font-bold text-slate-800 font-mono mt-1">#{{ $survey->id }}</dd>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg">
                <dt class="text-slate-400 font-medium">Tanggal Input</dt>
                <dd class="text-sm font-semibold text-slate-800 mt-1">{{ $survey->created_at ? $survey->created_at->format('d/m/Y H:i') : '-' }}</dd>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg">
                <dt class="text-slate-400 font-medium">Terakhir Diubah</dt>
                <dd class="text-sm font-semibold text-slate-800 mt-1">{{ $survey->updated_at ? $survey->updated_at->format('d/m/Y H:i') : '-' }}</dd>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg">
                <dt class="text-slate-400 font-medium">Diinput Oleh</dt>
                <dd class="text-sm font-bold text-slate-900 mt-1">
                    {{ $survey->user?->name ?? 'Data Lama' }}
                    @if ($survey->user?->email)
                        <span class="block text-[11px] font-normal text-slate-500">{{ $survey->user->email }}</span>
                    @endif
                </dd>
            </div>
        </dl>
    </x-card>

    @include('surveys.partials._detail_map')
    @include('surveys.partials._detail_infrastruktur')
    @include('surveys.partials._detail_koneksi')
    @include('surveys.partials._detail_photos')
</div>
@endsection

@push('scripts')
@if ($survey->hasCoordinates())
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lat = {{ $survey->latitude }};
        const lng = {{ $survey->longitude }};
        const map = L.map('mini-map').setView([lat, lng], 16);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        const marker = L.marker([lat, lng]).addTo(map);
        marker.bindPopup(`
            <div class="text-xs p-1">
                <div class="font-bold text-slate-900 mb-1">{{ addslashes($survey->alamat ?? 'Titik Survey') }}</div>
                <div class="text-slate-600">{{ addslashes($survey->kelurahan ?? '') }}, {{ addslashes($survey->kecamatan ?? '') }}</div>
            </div>
        `).openPopup();
    });
</script>
@endif
@endpush
