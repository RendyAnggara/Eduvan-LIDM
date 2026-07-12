<?php

namespace App\Http\Controllers\Web\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check() && in_array(Auth::user()->role, ['teacher', 'admin'])) {
            return redirect()->route('teacher.dashboard');
        }
        return view('teacher.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            if (in_array(Auth::user()->role, ['teacher', 'admin'])) {
                $request->session()->regenerate();
                return redirect()->intended(route('teacher.dashboard'));
            }

            Auth::logout();
            return back()->withErrors([
                'email' => 'Akses ditolak! Akun Anda terdaftar sebagai Siswa.',
            ])->withInput($request->only('email'));
        }

        return back()->withErrors([
            'email' => 'Email atau password yang dimasukkan salah.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('teacher.login');
    }
}
