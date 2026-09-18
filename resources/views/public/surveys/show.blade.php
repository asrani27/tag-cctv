@extends('layouts.public')
@section('title', 'Detail: ' . ($survey->alamat ?? '#' . $survey->id))

@push('styles')
    @if ($survey->hasCoordinates())
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @endif
@endpush

@section('content')
<div class="bg-slate-100 py-6 border-b border-slate-200">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <nav class="text-xs text-slate-500 mb-2">
            <a href="{{ route('home') }}" class="hover:text-emerald-600">Beranda</a> /
            <a href="{{ route('public.surveys.index') }}" class="hover:text-emerald-600">Data</a> /
            <span class="text-slate-800 font-medium">Detail</span>
        </nav>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $survey->alamat ?? 'Titik Survei' }}</h1>
                <p class="text-xs text-slate-600">Kel. {{ $survey->kelurahan ?? '-' }}, Kec. {{ $survey->kecamatan ?? '-' }}</p>
            </div>
            <a href="{{ route('public.surveys.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white text-slate-700 border border-slate-300">&larr; Kembali</a>
        </div>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 space-y-6">
    @include('public.surveys._detail_sections')
</div>
@endsection

@push('scripts')
    @if ($survey->hasCoordinates())
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const map = L.map('detail-mini-map').setView([{{ $survey->latitude }}, {{ $survey->longitude }}], 15);
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
                L.marker([{{ $survey->latitude }}, {{ $survey->longitude }}]).addTo(map);
            });
        </script>
    @endif
@endpush
