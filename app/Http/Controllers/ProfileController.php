<?php

namespace App\Http\Controllers;

use App\Models\Kelurahan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $kelurahan = null;

        if ($user->isKelurahan()) {
            try {
                $kelurahan = Kelurahan::with('kecamatan')->where('name', $user->getKelurahanName())->first();
            } catch (\Throwable) {
            }
        }

        return view('profile.edit', [
            'user' => $user,
            'kelurahan' => $kelurahan,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];

        // Kelurahan accounts retain their official Banjarmasin email
        if (! $user->isKelurahan()) {
            $rules['email'] = ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)];
        }

        $validated = $request->validate($rules);

        $user->name = $validated['name'];

        if (! $user->isKelurahan() && isset($validated['email'])) {
            $user->email = $validated['email'];
        }

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
