<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function show(): View|RedirectResponse
    {
        if (session()->has('demo_user')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle demo login request.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $validUsers = [
            'admin' => 'admin',
            'staff' => 'staff',
        ];

        $user = $credentials['username'];
        $pass = $credentials['password'];

        if (!isset($validUsers[$user]) || $validUsers[$user] !== $pass) {
            return back()->withErrors([
                'username' => 'Username atau password yang dimasukkan tidak sesuai.',
            ])->onlyInput('username');
        }

        $request->session()->regenerate();
        $request->session()->put('demo_user', $user);
        $request->session()->put('demo_role', ucfirst($user));

        return redirect()->route('dashboard')->with('success', "Selamat datang kembali, {$user}!");
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar sistem.');
    }
}
