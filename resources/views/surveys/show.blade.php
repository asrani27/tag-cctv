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
                <x-badge :variant="$survey->status_badge_variant" size="sm">{{ $survey->status ?? 'Belum Terpasang' }}</x-badge>
            </div>
            <h1 class="text-xl font-bold text-slate-900">{{ $survey->alamat ?? 'Survey #' . $survey->id }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelurahan {{ $survey->kelurahan ?? '-' }} &bull; Tanggal {{ $survey->created_at ? $survey->created_at->format('d/m/Y') : '-' }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @can('update', $survey)
                <form method="POST" action="{{ route('surveys.update-status', $survey) }}" class="inline-flex items-center gap-1.5">
                    @csrf
                    @method('PATCH')
                    <select
                        name="status"
                        onchange="this.form.submit()"
                        aria-label="Status Pemasangan"
                        class="text-xs font-semibold rounded-lg border px-2.5 py-1.5 pr-7 cursor-pointer transition {{ $survey->status === 'Selesai Dipasang' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($survey->status === 'Proses Pemasangan' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200') }}"
                    >
                        @foreach (\App\Models\SurveyLocation::STATUS_OPTIONS as $st)
                            <option value="{{ $st }}" @selected(($survey->status ?? \App\Models\SurveyLocation::STATUS_BELUM_TERPASANG) === $st)>{{ $st }}</option>
                        @endforeach
                    </select>
                </form>
            @endcan
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
        const markerColor = @json($survey->marker_color);
        const map = L.map('mini-map').setView([lat, lng], 16);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        const icon = L.divIcon({
            className: 'custom-status-marker',
            html: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 36" width="26" height="38">
                <path d="M12 0C5.373 0 0 5.373 0 12c0 8.5 12 24 12 24s12-15.5 12-24c0-6.627-5.373-12-12-12z" fill="${markerColor}" stroke="#ffffff" stroke-width="2"/>
                <circle cx="12" cy="12" r="5" fill="#ffffff"/>
            </svg>`,
            iconSize: [26, 38],
            iconAnchor: [13, 38],
            popupAnchor: [0, -34],
        });
        const marker = L.marker([lat, lng], { icon }).addTo(map);
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
