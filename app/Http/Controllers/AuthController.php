<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
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

        $user = User::where('email', $request->email)->first();

        if ($user && $user->isLocked()) {
            $minutes = (int) ceil(now()->diffInSeconds($user->locked_until, false) / 60);

            throw ValidationException::withMessages([
                'email' => 'Akun terkunci sementara. Silakan coba lagi dalam '.max($minutes, 1).' menit.',
            ]);
        }

        // Cek kecocokan data
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            RateLimiter::clear($this->throttleKey($request));

            if ($user) {
                $user->forceFill(['failed_login_attempts' => 0, 'locked_until' => null])->save();
            }

            $request->session()->regenerate();

            ActivityLog::record('login', 'users', Auth::id(), 'Login berhasil');

            return redirect()->intended('/transactions'); // Kalau sukses, lempar ke transaksi
        }

        // Catat percobaan gagal
        RateLimiter::hit($this->throttleKey($request), 60);

        if ($user && $this->registerFailedAttempt($user)) {
            $minutes = $this->lockoutMinutes();

            return back()->with('error', 'Terlalu banyak percobaan gagal. Akun dikunci sementara selama '.$minutes.' menit.');
        }

        // Kalau gagal
        return back()->with('error', 'Email atau Password salah!');
    }

    // Tambah hitungan gagal; kunci akun bila melewati batas. True bila baru dikunci.
    protected function registerFailedAttempt(User $user): bool
    {
        $user->failed_login_attempts = $user->failed_login_attempts + 1;

        if ($user->failed_login_attempts >= $this->maxAttempts()) {
            $user->locked_until = now()->addMinutes($this->lockoutMinutes());
            $user->failed_login_attempts = 0;
            $user->save();

            return true;
        }

        $user->save();

        return false;
    }

    protected function maxAttempts(): int
    {
        return max((int) config('keuangan.security.max_login_attempts', 5), 1);
    }

    protected function lockoutMinutes(): int
    {
        return max((int) config('keuangan.security.lockout_minutes', 15), 1);
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
