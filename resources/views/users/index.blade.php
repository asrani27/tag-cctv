@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')
@section('page_title', 'Manajemen Pengguna')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Manajemen Pengguna</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola akun administrator dan surveyor lapangan</p>
        </div>
        <div>
            <x-button variant="primary" size="md" :href="route('users.create')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Pengguna
            </x-button>
        </div>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('users.index') }}" class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <label for="search" class="block text-xs font-semibold text-slate-700 uppercase mb-1">Pencarian</label>
                <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Cari nama atau email..." class="block w-full rounded-lg border border-slate-300 text-sm px-3.5 py-2 focus:border-emerald-500" />
            </div>
            <div>
                <label for="role" class="block text-xs font-semibold text-slate-700 uppercase mb-1">Role</label>
                <select name="role" id="role" class="block w-full rounded-lg border border-slate-300 text-sm px-3 py-2 bg-white">
                    <option value="">Semua Role</option>
                    <option value="superadmin" @selected($currentRole === 'superadmin')>Superadmin</option>
                    <option value="user" @selected($currentRole === 'user')>Surveyor (User)</option>
                </select>
            </div>
            <div>
                <label for="status" class="block text-xs font-semibold text-slate-700 uppercase mb-1">Status</label>
                <select name="status" id="status" class="block w-full rounded-lg border border-slate-300 text-sm px-3 py-2 bg-white">
                    <option value="">Semua Status</option>
                    <option value="active" @selected($currentStatus === 'active')>Aktif</option>
                    <option value="inactive" @selected($currentStatus === 'inactive')>Nonaktif</option>
                </select>
            </div>
        </div>
        <div class="flex items-center justify-between pt-1 border-t border-slate-100 text-xs">
            <div class="text-slate-500">Total: <span class="font-semibold text-slate-800">{{ $users->total() }}</span> pengguna</div>
            <div class="flex items-center gap-2">
                @if ($search || $currentRole || $currentStatus)
                    <a href="{{ route('users.index') }}" class="px-3 py-1.5 text-xs text-slate-600 hover:underline">Reset</a>
                @endif
                <x-button type="submit" variant="primary" size="sm">Terapkan Filter</x-button>
            </div>
        </div>
    </form>

    <!-- Users Table -->
    <x-card padding="p-0">
        @if ($users->isEmpty())
            <div class="text-center py-12 px-4">
                <h3 class="text-sm font-semibold text-slate-800">Tidak ada data pengguna</h3>
                <p class="text-xs text-slate-500 mt-1 mb-4">Pengguna yang cocok tidak ditemukan.</p>
                <x-button variant="outline" size="sm" :href="route('users.index')">Reset Filter</x-button>
            </div>
        @else
            @include('users._table')

            <div class="px-6 py-4 border-t border-slate-200">
                {{ $users->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection