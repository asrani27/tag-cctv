@extends('layouts.admin')

@section('title', 'Tambah Kelurahan')
@section('page_title', 'Tambah Kelurahan Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Tambah Kelurahan Baru</h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftarkan kelurahan baru beserta kode wilayah Kemendagri</p>
        </div>
        <x-button variant="outline" size="sm" :href="route('kelurahans.index', ['kecamatan_id' => $selectedKecamatanId])">
            Kembali
        </x-button>
    </div>

    <form method="POST" action="{{ route('kelurahans.store') }}">
        @csrf

        <x-card title="Data Kelurahan" subtitle="Lengkapi informasi kecamatan induk, kode wilayah, dan nama kelurahan">
            <div class="space-y-4">
                <div>
                    <label for="kecamatan_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kecamatan <span class="text-rose-500">*</span>
                    </label>
                    <select
                        name="kecamatan_id"
                        id="kecamatan_id"
                        class="block w-full rounded-lg border border-slate-300 text-sm px-3.5 py-2.5 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200/60 @error('kecamatan_id') border-rose-400 bg-rose-50/20 text-rose-900 @enderror"
                        required
                    >
                        <option value="">-- Pilih Kecamatan --</option>
                        @foreach ($kecamatans as $kec)
                            <option value="{{ $kec->id }}" @selected(old('kecamatan_id', $selectedKecamatanId) == $kec->id)>
                                {{ $kec->name }} ({{ $kec->kode ?? 'Belum ada kode' }})
                            </option>
                        @endforeach
                    </select>
                    @error('kecamatan_id')
                        <p class="mt-1 text-xs text-rose-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <x-input
                    name="kode"
                    label="Kode Kemendagri (Opsional)"
                    placeholder="Contoh: 63.71.01.1001"
                    :value="old('kode')"
                    helper="Format kode wilayah Kemendagri, contoh: 63.71.01.1001"
                />

                <x-input
                    name="name"
                    label="Nama Kelurahan"
                    placeholder="Contoh: Mantuil"
                    :value="old('name')"
                    required
                />
            </div>

            <x-slot:footer>
                <div class="flex items-center justify-end gap-3">
                    <x-button variant="outline" size="sm" :href="route('kelurahans.index', ['kecamatan_id' => $selectedKecamatanId])">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" size="sm">
                        Simpan Kelurahan
                    </x-button>
                </div>
            </x-slot:footer>
        </x-card>
    </form>
</div>
@endsection
