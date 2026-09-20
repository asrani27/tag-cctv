<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->withCount('surveys')->latest();

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $users = $query->paginate(10)->withQueryString();

        return view('users.index', [
            'users' => $users,
            'search' => $search,
            'currentRole' => $request->input('role', ''),
            'currentStatus' => $request->input('status', ''),
        ]);
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in([User::ROLE_SUPERADMIN, User::ROLE_USER])],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->role = $validated['role'];
        $user->is_active = $request->boolean('is_active', true);
        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', "Pengguna {$user->name} berhasil ditambahkan.");
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in([User::ROLE_SUPERADMIN, User::ROLE_USER])],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($user->id === $request->user()->id) {
            if ($validated['role'] !== User::ROLE_SUPERADMIN) {
                return back()->withInput()->withErrors(['role' => 'Anda tidak dapat menurunkan role akun Anda sendiri.']);
            }
            if (! $request->boolean('is_active', true)) {
                return back()->withInput()->withErrors(['is_active' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.']);
            }
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->role = $validated['role'];
        $user->is_active = $request->boolean('is_active');
        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->route('users.index')
            ->with('success', "Akun {$user->name} berhasil {$status}.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', "Pengguna {$userName} berhasil dihapus.");
    }
}
