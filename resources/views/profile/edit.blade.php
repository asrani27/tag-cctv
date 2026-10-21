@extends('layouts.admin')

@section('title', 'Profil Akun')
@section('page_title', 'Profil Pengguna')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Profil Akun</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola data profil dan keamanan kata sandi akun Anda</p>
        </div>
        <div>
            @if ($user->isKelurahan())
                <x-button variant="outline" size="sm" :href="route('usulans.index')">
                    Kembali ke Usulan
                </x-button>
            @else
                <x-button variant="outline" size="sm" :href="route('dashboard')">
                    Kembali ke Dashboard
                </x-button>
            @endif
        </div>
    </div>

    @if (session('success'))
        <x-alert type="success" :dismissible="true">
            {{ session('success') }}
        </x-alert>
    @endif

    <!-- Profile Overview Card -->
    <x-card>
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center font-bold text-xl shadow-md shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-lg font-bold text-slate-900 truncate">{{ $user->name }}</h2>
                    @if ($user->isSuperAdmin())
                        <x-badge variant="emerald" size="xs">Superadmin</x-badge>
                    @elseif ($user->isKelurahan())
                        <x-badge variant="indigo" size="xs">Kelurahan</x-badge>
                    @else
                        <x-badge variant="slate" size="xs">Surveyor</x-badge>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-0.5">{{ $user->email }}</p>
            </div>
        </div>

        @if ($user->isKelurahan())
            <div class="mt-4 pt-4 border-t border-slate-100 bg-indigo-50/60 -mx-6 -mb-6 p-4 rounded-b-xl text-xs text-indigo-900">
                <p class="font-semibold">Akun Resmi Kelurahan {{ $user->getKelurahanName() ?? $user->name }}</p>
                <p class="text-indigo-700 mt-0.5">
                    Wilayah: <span class="font-medium">{{ $user->getKelurahanName() }}</span>
                    @if ($kelurahan && $kelurahan->kecamatan)
                        , Kec. {{ $kelurahan->kecamatan->name }}
                    @endif
                    , Kota Banjarmasin
                </p>
            </div>
        @endif
    </x-card>

    <!-- Profile Edit Form -->
    <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <x-card title="Informasi Akun" subtitle="Perbarui nama pengguna">
            <div class="space-y-4">
                <x-input
                    name="name"
                    label="Nama Lengkap / Instansi"
                    placeholder="Masukkan nama lengkap"
                    :value="old('name', $user->name)"
                    required
                />

                @if ($user->isKelurahan())
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Alamat Email Resmi (Login)
                        </label>
                        <input
                            type="email"
                            value="{{ $user->email }}"
                            class="block w-full rounded-lg border border-slate-200 bg-slate-50 text-slate-500 text-sm px-3.5 py-2.5 cursor-not-allowed"
                            readonly
                            disabled
                        />
                        <p class="text-[11px] text-slate-400 mt-1">Alamat email resmi kelurahan tidak dapat diubah.</p>
                    </div>
                @else
                    <x-input
                        type="email"
                        name="email"
                        label="Alamat Email (Login)"
                        placeholder="user@banjarmasinkota.go.id"
                        :value="old('email', $user->email)"
                        required
                    />
                @endif
            </div>
        </x-card>

        <x-card title="Keamanan Kata Sandi" subtitle="Ubah kata sandi akun Anda" id="password-section">
            <div class="space-y-4">
                @if ($isDefaultPassword ?? ($user->isKelurahan() && $user->hasDefaultPassword()))
                    <div class="p-3.5 rounded-lg border border-amber-200 bg-amber-50 text-amber-900 text-xs flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <span class="font-semibold">Wajib Ganti Kata Sandi:</span>
                            Akun Anda saat ini masih menggunakan kata sandi bawaan default (<code class="bg-amber-100 text-amber-900 px-1 py-0.5 rounded font-mono text-[11px]">kelurahanbanjarmasin</code>). Anda wajib mengganti kata sandi ini sebelum dapat menginput data usulan.
                        </div>
                    </div>
                @else
                    <p class="text-xs text-slate-400">Kosongkan jika tidak ingin mengganti kata sandi.</p>
                @endif

                <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-lg text-xs space-y-1 text-slate-600">
                    <p class="font-semibold text-slate-700">Kriteria kata sandi baru:</p>
                    <ul class="list-disc list-inside space-y-0.5 text-[11px] text-slate-600">
                        <li>Minimal 8 karakter</li>
                        <li>Mengandung huruf besar (A-Z) dan huruf kecil (a-z)</li>
                        <li>Mengandung angka (0-9)</li>
                        <li>Mengandung simbol / karakter khusus (@$!%*#?& dll)</li>
                    </ul>
                </div>

                <x-input
                    type="password"
                    name="current_password"
                    label="Kata Sandi Saat Ini"
                    placeholder="Masukkan kata sandi saat ini"
                    :required="$isDefaultPassword ?? ($user->isKelurahan() && $user->hasDefaultPassword())"
                />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input
                        type="password"
                        name="password"
                        label="Kata Sandi Baru"
                        placeholder="Minimal 8 karakter"
                        :required="$isDefaultPassword ?? ($user->isKelurahan() && $user->hasDefaultPassword())"
                    />

                    <x-input
                        type="password"
                        name="password_confirmation"
                        label="Konfirmasi Kata Sandi Baru"
                        placeholder="Ulangi kata sandi baru"
                        :required="$isDefaultPassword ?? ($user->isKelurahan() && $user->hasDefaultPassword())"
                    />
                </div>
            </div>

            <x-slot:footer>
                <div class="flex items-center justify-end">
                    <x-button type="submit" variant="primary" size="md">
                        Simpan Perubahan
                    </x-button>
                </div>
            </x-slot:footer>
        </x-card>
    </form>
</div>
@endsection
