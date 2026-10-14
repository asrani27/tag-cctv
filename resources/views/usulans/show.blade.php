@extends('layouts.admin')

@section('title', 'Detail Usulan #' . $usulan->id)
@section('page_title', 'Detail Usulan Titik CCTV')

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
                <x-badge variant="emerald" size="sm">ID #{{ $usulan->id }}</x-badge>
                @if ($usulan->nama_lokasi)
                    <x-badge variant="slate" size="sm">{{ $usulan->nama_lokasi }}</x-badge>
                @endif
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $usulan->status_badge_class }}">
                    {{ $usulan->status }}
                </span>
            </div>
            <h1 class="text-xl font-bold text-slate-900">
                Usulan Titik: {{ $usulan->nama_lokasi ?? 'Lokasi' }} - RT {{ $usulan->rt ?? '-' }} {{ $usulan->kelurahan ?? '' }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Kelurahan {{ $usulan->kelurahan ?? '-' }} &bull; RT {{ $usulan->rt ?? '-' }} &bull; Diajukan: {{ $usulan->created_at ? $usulan->created_at->format('d/m/Y H:i') : '-' }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <x-button variant="outline" size="sm" :href="route('usulans.index')">
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

    <!-- Karakteristik Teknis & Lapangan -->
    <x-card title="Karakteristik & Kesiapan Teknis Titik Usulan" subtitle="Spesifikasi jenis titik, kesiapan daya, dan CCTV eksisting">
        <dl class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
            <div class="p-3 bg-slate-50 rounded-lg">
                <dt class="text-slate-400 font-medium">Jenis Lokasi</dt>
                <dd class="text-sm font-bold text-slate-800 mt-1">{{ $usulan->nama_lokasi ?? '-' }}</dd>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg">
                <dt class="text-slate-400 font-medium">Kategori Titik</dt>
                <dd class="text-sm font-bold text-slate-800 mt-1">{{ $usulan->kategori_titik ?? '-' }}</dd>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg">
                <dt class="text-slate-400 font-medium">Infrastruktur Penyangga</dt>
                <dd class="text-sm font-bold text-slate-800 mt-1">{{ $usulan->infrastruktur_penyangga ?? '-' }}</dd>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg">
                <dt class="text-slate-400 font-medium">Sumber Listrik</dt>
                <dd class="mt-1">
                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold {{ $usulan->sumber_daya_listrik === 'Ada' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                        {{ $usulan->sumber_daya_listrik ?? '-' }}
                    </span>
                </dd>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg">
                <dt class="text-slate-400 font-medium">CCTV Eksisting</dt>
                <dd class="mt-1">
                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold {{ $usulan->cctv_eksisting === 'Sudah Ada' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}">
                        {{ $usulan->cctv_eksisting ?? '-' }}
                    </span>
                </dd>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg">
                <dt class="text-slate-400 font-medium">Pengusul</dt>
                <dd class="text-sm font-bold text-slate-800 mt-1">{{ $usulan->user?->name ?? 'Pengguna' }}</dd>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg sm:col-span-2">
                <dt class="text-slate-400 font-medium">Status Pengajuan</dt>
                <dd class="mt-1">
                    <span class="inline-flex px-2.5 py-1 rounded text-xs font-semibold {{ $usulan->status_badge_class }}">
                        {{ $usulan->status }}
                    </span>
                </dd>
            </div>
        </dl>

        <div class="mt-4 pt-4 border-t border-slate-100">
            <h4 class="text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Keterangan Tambahan</h4>
            <div class="p-3 bg-white border border-slate-200 rounded-lg text-xs text-slate-800 leading-relaxed min-h-[4rem]">
                {{ $usulan->keterangan ?: 'Tidak ada keterangan tambahan.' }}
            </div>
        </div>
    </x-card>
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
        marker.bindPopup(`
            <div class="text-xs p-1">
                <div class="font-bold text-slate-900 mb-0.5">{{ addslashes($usulan->nama_lokasi ?? 'Titik Usulan') }} - RT {{ addslashes($usulan->rt ?? '') }}</div>
                <div class="text-slate-600">{{ addslashes($usulan->kelurahan ?? '') }}</div>
            </div>
        `).openPopup();
    });
</script>
@endif
@endpush
