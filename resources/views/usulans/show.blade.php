@extends('layouts.admin')

@section('title', 'Detail Usulan #' . $usulan->id . ' (' . ($usulan->isWifi() ? 'Wifi' : 'CCTV') . ')')
@section('page_title', $usulan->isWifi() ? 'Detail Usulan Titik Wifi' : 'Detail Usulan Titik CCTV')

@push('styles')
@if ($usulan->hasCoordinates())
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endif
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $usulan->isWifi() ? 'bg-sky-100 text-sky-800' : 'bg-emerald-100 text-emerald-800' }}">
                    {{ $usulan->isWifi() ? '📶 Titik Wifi' : '📹 Titik CCTV' }}
                </span>
                <x-badge variant="slate" size="sm">ID #{{ $usulan->id }}</x-badge>
                @if ($usulan->isWifi() && $usulan->nama_fasilitas_publik)
                    <x-badge variant="sky" size="sm">{{ $usulan->nama_fasilitas_publik }}</x-badge>
                @elseif ($usulan->nama_lokasi)
                    <x-badge variant="slate" size="sm">{{ $usulan->nama_lokasi }}</x-badge>
                @endif
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $usulan->status_badge_class }}">
                    {{ $usulan->status }}
                </span>
            </div>
            <h1 class="text-xl font-bold text-slate-900">
                @if ($usulan->isWifi())
                    Usulan Titik Wifi: {{ $usulan->nama_fasilitas_publik ?? 'Fasilitas Publik' }} - RT {{ $usulan->rt ?? '-' }} {{ $usulan->kelurahan ?? '' }}
                @else
                    Usulan Titik: {{ $usulan->nama_lokasi ?? 'Lokasi' }} - RT {{ $usulan->rt ?? '-' }} {{ $usulan->kelurahan ?? '' }}
                @endif
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Kelurahan {{ $usulan->kelurahan ?? '-' }} &bull; RT {{ $usulan->rt ?? '-' }} &bull; Diajukan: {{ $usulan->created_at ? $usulan->created_at->format('d/m/Y H:i') : '-' }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <x-button variant="outline" size="sm" :href="route('usulans.index', ['tab' => $usulan->jenis ?? 'cctv'])">
                Kembali
            </x-button>
            @can('update', $usulan)
                <x-button variant="primary" size="sm" :href="route('usulans.edit', $usulan)">
                    Edit
                </x-button>
            @endcan
            @can('delete', $usulan)
                <form method="POST" action="{{ route('usulans.destroy', $usulan) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data usulan ini?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" variant="danger" size="sm">
                        Hapus
                    </x-button>
                </form>
            @endcan
        </div>
    </div>

    @include('usulans.partials._detail_map')

    @if ($usulan->isWifi())
        @include('usulans.partials._detail_card_wifi')
    @else
        @include('usulans.partials._detail_card_cctv')
    @endif
</div>
@endsection

@push('scripts')
@if ($usulan->hasCoordinates())
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lat = {{ $usulan->latitude }};
        const lng = {{ $usulan->longitude }};
        const map = L.map('mini-map').setView([lat, lng], 16);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        const marker = L.marker([lat, lng]).addTo(map);
        const titleText = @json($usulan->isWifi() ? ($usulan->nama_fasilitas_publik ?? 'Titik Wifi') : ($usulan->nama_lokasi ?? 'Titik Usulan'));
        marker.bindPopup(`
            <div class="text-xs p-1">
                <div class="font-bold text-slate-900 mb-0.5">${titleText} - RT {{ addslashes($usulan->rt ?? '') }}</div>
                <div class="text-slate-600">{{ addslashes($usulan->kelurahan ?? '') }}</div>
            </div>
        `).openPopup();
    });
</script>
@endif
@endpush

