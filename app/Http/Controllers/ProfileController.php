<?php

namespace App\Http\Controllers;

use App\Models\Kelurahan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
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
            'isDefaultPassword' => $user->isKelurahan() && $user->hasDefaultPassword(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $isKelurahanDefault = $user->isKelurahan() && $user->hasDefaultPassword();

        $passwordRule = Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols();

        $passwordRules = ['nullable', 'string', 'confirmed', $passwordRule];
        $currentPasswordRules = ['nullable', 'required_with:password', 'current_password'];

        if ($isKelurahanDefault) {
            $passwordRules = ['required', 'string', 'confirmed', 'different:current_password', $passwordRule];
            $currentPasswordRules = ['required', 'current_password'];
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'current_password' => $currentPasswordRules,
            'password' => $passwordRules,
        ];

        // Kelurahan accounts retain their official Banjarmasin email
        if (! $user->isKelurahan()) {
            $rules['email'] = ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)];
        }

        $messages = [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.required' => 'Kata sandi baru wajib diisi untuk mengganti kata sandi default.',
            'password.different' => 'Kata sandi baru harus berbeda dengan kata sandi saat ini.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.letters' => 'Kata sandi baru harus mengandung huruf.',
            'password.mixed' => 'Kata sandi baru harus mengandung huruf besar dan huruf kecil.',
            'password.numbers' => 'Kata sandi baru harus mengandung angka.',
            'password.symbols' => 'Kata sandi baru harus mengandung simbol.',
        ];

        $validated = $request->validate($rules, $messages);

        $user->name = $validated['name'];

        if (! $user->isKelurahan() && isset($validated['email'])) {
            $user->email = $validated['email'];
        }

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
            $user->password_changed_at = now();
            $user->resetDefaultPasswordCache();
        }

        $user->save();

        $message = $isKelurahanDefault
            ? 'Kata sandi Anda berhasil diperbarui. Anda sekarang dapat melakukan input data usulan.'
            : 'Profil Anda berhasil diperbarui.';

        return redirect()
            ->route('profile.edit')
            ->with('success', $message);
    }
}
