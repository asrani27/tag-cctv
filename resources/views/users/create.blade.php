@extends('layouts.admin')

@section('title', 'Tambah Pengguna')
@section('page_title', 'Tambah Pengguna Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Tambah Pengguna Baru</h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftarkan akun administrator atau surveyor baru ke dalam sistem</p>
        </div>
        <x-button variant="outline" size="sm" :href="route('users.index')">
            Kembali
        </x-button>
    </div>

    <form method="POST" action="{{ route('users.store') }}">
        @csrf

        <x-card title="Informasi Akun" subtitle="Lengkapi profil dan hak akses pengguna">
            <div class="space-y-4">
                <x-input
                    name="name"
                    label="Nama Lengkap"
                    placeholder="Contoh: Budi Santoso"
                    :value="old('name')"
                    required
                />

                <x-input
                    type="email"
                    name="email"
                    label="Alamat Email"
                    placeholder="user@banjarmasinkota.go.id"
                    :value="old('email')"
                    required
                />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input
                        type="password"
                        name="password"
                        label="Password"
                        placeholder="Minimal 8 karakter"
                        required
                    />

                    <x-input
                        type="password"
                        name="password_confirmation"
                        label="Konfirmasi Password"
                        placeholder="Ulangi password"
                        required
                    />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="role" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Role / Hak Akses <span class="text-rose-500">*</span>
                        </label>
                        <select name="role" id="role" class="block w-full rounded-lg border border-slate-300 text-sm px-3.5 py-2.5 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200/60" required>
                            <option value="user" @selected(old('role') === 'user' || !old('role'))>Surveyor Lapangan (User)</option>
                            <option value="superadmin" @selected(old('role') === 'superadmin')>Superadmin (Akses Penuh)</option>
                        </select>
                        @error('role')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center pt-6">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" @checked(old('is_active', '1') == '1')>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none ring-2 ring-transparent peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            <span class="ml-3 text-xs font-medium text-slate-700">Akun Aktif</span>
                        </label>
                    </div>
                </div>
            </div>

            <x-slot:footer>
                <div class="flex items-center justify-end gap-3">
                    <x-button variant="outline" size="sm" :href="route('users.index')">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" size="sm">
                        Simpan Pengguna
                    </x-button>
                </div>
            </x-slot:footer>
        </x-card>
    </form>
</div>
@endsection
