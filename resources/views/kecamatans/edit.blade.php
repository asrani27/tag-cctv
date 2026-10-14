@extends('layouts.admin')

@section('title', 'Edit Kecamatan: ' . $kecamatan->name)
@section('page_title', 'Edit Kecamatan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Edit Kecamatan: {{ $kecamatan->name }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Perbarui kode wilayah Kemendagri atau nama kecamatan</p>
        </div>
        <x-button variant="outline" size="sm" :href="route('kecamatans.index')">
            Kembali
        </x-button>
    </div>

    <form method="POST" action="{{ route('kecamatans.update', $kecamatan) }}">
        @csrf
        @method('PUT')

        <x-card title="Data Kecamatan" subtitle="Lengkapi informasi kode wilayah dan nama kecamatan">
            <div class="space-y-4">
                <x-input
                    name="kode"
                    label="Kode Kemendagri (Opsional)"
                    placeholder="Contoh: 63.71.01"
                    :value="old('kode', $kecamatan->kode)"
                    helper="Format kode wilayah Kemendagri, contoh: 63.71.01"
                />

                <x-input
                    name="name"
                    label="Nama Kecamatan"
                    placeholder="Contoh: Banjarmasin Selatan"
                    :value="old('name', $kecamatan->name)"
                    required
                />
            </div>

            <x-slot:footer>
                <div class="flex items-center justify-end gap-3">
                    <x-button variant="outline" size="sm" :href="route('kecamatans.index')">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" size="sm">
                        Simpan Perubahan
                    </x-button>
                </div>
            </x-slot:footer>
        </x-card>
    </form>
</div>
@endsection
