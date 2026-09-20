@extends('layouts.admin')

@section('title', 'Edit Pengguna: ' . $user->name)
@section('page_title', 'Edit Pengguna')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Edit Pengguna: {{ $user->name }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Perbarui profil, kata sandi, role, atau status akun pengguna</p>
        </div>
        <x-button variant="outline" size="sm" :href="route('users.index')">Kembali</x-button>
    </div>

    @if ($user->id === auth()->id())
        <div class="p-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-lg text-xs">
            Perhatian: Anda sedang mengedit akun sendiri. Role dan status aktif akun sendiri tidak dapat diubah di sini.
        </div>
    @endif

    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')

        <x-card title="Informasi Akun" subtitle="Lengkapi data pengguna">
            <div class="space-y-4">
                <x-input name="name" label="Nama Lengkap" placeholder="Contoh: Budi Santoso" :value="old('name', $user->name)" required />
                <x-input type="email" name="email" label="Alamat Email" placeholder="user@banjarmasinkota.go.id" :value="old('email', $user->email)" required />

                <div class="pt-2 border-t border-slate-100">
                    <p class="text-xs font-semibold text-slate-700 mb-1">Ubah Kata Sandi (Opsional)</p>
                    <p class="text-[11px] text-slate-400 mb-3">Kosongkan jika tidak ingin mengubah kata sandi.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-input type="password" name="password" label="Password Baru" placeholder="Minimal 8 karakter" />
                        <x-input type="password" name="password_confirmation" label="Konfirmasi Password" placeholder="Ulangi password" />
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="role" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Role / Hak Akses <span class="text-rose-500">*</span>
                        </label>
                        <select name="role" id="role" class="block w-full rounded-lg border border-slate-300 text-sm px-3.5 py-2.5 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200/60 {{ $user->id === auth()->id() ? 'pointer-events-none opacity-60' : '' }}" required>
                            <option value="user" @selected(old('role', $user->role) === 'user')>Surveyor Lapangan (User)</option>
                            <option value="superadmin" @selected(old('role', $user->role) === 'superadmin')>Superadmin (Akses Penuh)</option>
                        </select>
                        @error('role')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center pt-6">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" @checked(old('is_active', $user->is_active ? '1' : '0') == '1') {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                            @if ($user->id === auth()->id())
                                <input type="hidden" name="is_active" value="1">
                            @endif
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            <span class="ml-3 text-xs font-medium text-slate-700">Akun Aktif</span>
                        </label>
                    </div>
                </div>
            </div>

            <x-slot:footer>
                <div class="flex items-center justify-end gap-3">
                    <x-button variant="outline" size="sm" :href="route('users.index')">Batal</x-button>
                    <x-button type="submit" variant="primary" size="sm">Simpan Perubahan</x-button>
                </div>
            </x-slot:footer>
        </x-card>
    </form>
</div>
@endsection
