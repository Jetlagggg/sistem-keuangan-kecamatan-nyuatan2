<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // 1. Tampilkan Form Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // 2. Proses Login
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Batasi percobaan login (anti brute-force)
        $this->ensureIsNotRateLimited($request);

        // Cek kecocokan data
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            RateLimiter::clear($this->throttleKey($request));
            $request->session()->regenerate();

            ActivityLog::record('login', 'users', Auth::id(), 'Login berhasil');

            return redirect()->intended('/transactions'); // Kalau sukses, lempar ke transaksi
        }

        // Catat percobaan gagal
        RateLimiter::hit($this->throttleKey($request), 60);

        // Kalau gagal
        return back()->with('error', 'Email atau Password salah!');
    }

    // 3. Proses Logout
    public function logout(Request $request)
    {
        if (auth()->check()) {
            ActivityLog::record('logout', 'users', auth()->id(), 'Logout');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    // Batasi 5 percobaan gagal per (email + IP) dalam 1 menit
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
        ]);
    }

    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower((string) $request->input('email')).'|'.$request->ip());
    }
}
