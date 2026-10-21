<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->isKelurahan()) {
                return $user->hasDefaultPassword()
                    ? redirect()->route('profile.edit')
                    : redirect()->route('usulans.index');
            }

            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (! $user->isActive()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withInput($request->only('email', 'remember'))
                    ->withErrors([
                        'email' => 'Akun Anda dinonaktifkan. Silakan hubungi administrator.',
                    ]);
            }

            $request->session()->regenerate();

            if ($user->isKelurahan() && $user->hasDefaultPassword()) {
                return redirect()->route('profile.edit')
                    ->with('warning', 'Akun Anda masih menggunakan kata sandi default. Anda wajib mengganti kata sandi terlebih dahulu sebelum dapat menginput data usulan.');
            }

            $targetRoute = $user->isKelurahan()
                ? route('usulans.index')
                : route('dashboard');

            return redirect()->intended($targetRoute);
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'Email atau kata sandi yang Anda masukkan salah.',
            ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
