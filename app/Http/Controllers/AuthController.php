<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            // Batasi hanya satu akun guru yang bisa login
            if ($user->role === 'guru' && $user->email !== 'guru@sekolah.com') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun guru hanya boleh satu. Silakan login dengan akun guru utama.',
                ])->onlyInput('email');
            }

            // Redirect berdasarkan role
            if ($user->role === 'admin') {
                return redirect()->intended('/admin');
            } elseif ($user->role === 'guru') {
                return redirect()->intended('/guru');
            } else {
                return redirect()->intended('/siswa');
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
