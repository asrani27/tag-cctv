<div class="overflow-x-auto">
    <table class="w-full text-left text-xs text-slate-600">
        <thead class="bg-slate-50 text-slate-700 uppercase font-semibold border-b border-slate-200">
            <tr>
                <th class="px-4 py-3">Pengguna</th>
                <th class="px-4 py-3">Role</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center">Survey Dibuat</th>
                <th class="px-4 py-3">Terdaftar</th>
                <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($users as $user)
                <tr class="hover:bg-slate-50/70 transition">
                    <td class="px-4 py-3.5">
                        <div class="font-medium text-slate-900 text-sm">{{ $user->name }}</div>
                        <div class="text-slate-400 text-xs">{{ $user->email }}</div>
                        @if ($user->id === auth()->id())
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-emerald-100 text-emerald-800">Akun Anda</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5">
                        @if ($user->isSuperAdmin())
                            <x-badge variant="emerald" size="xs">Superadmin</x-badge>
                        @else
                            <x-badge variant="slate" size="xs">Surveyor</x-badge>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        @if ($user->is_active)
                            <x-badge variant="emerald" size="xs">Aktif</x-badge>
                        @else
                            <x-badge variant="rose" size="xs">Nonaktif</x-badge>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-center font-bold text-slate-800">
                        {{ number_format($user->surveys_count) }}
                    </td>
                    <td class="px-4 py-3.5 text-slate-400 whitespace-nowrap">
                        {{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-4 py-3.5 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="{{ route('users.edit', $user) }}" class="p-1.5 text-slate-500 hover:text-blue-700 hover:bg-blue-50 rounded-md transition" title="Edit Pengguna">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>

                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-1.5 rounded-md transition {{ $user->is_active ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }}" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}" onclick="return confirm('Apakah Anda yakin ingin mengubah status aktif pengguna ini?');">
                                        @if ($user->is_active)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
