<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Display the login view.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->isDoctor()) {
                return redirect()->route('doctor.dashboard');
            }

            if ($user->isApoteker()) {
                return redirect()->route('apoteker.dashboard');
            }

            return redirect()->route('home');
        }

        return view('auth.login', [
            'initialRole' => request('role', 'user'),
        ]);
    }

    /**
     * Display the dedicated doctor login view.
     */
    public function showDoctorLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()->isDoctor()
                ? redirect()->route('doctor.dashboard')
                : redirect()->route('home');
        }

        return view('auth.login', ['initialRole' => 'doctor']);
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Selamat datang kembali di Portal Administrator, '.$user->name);
            }

            if ($user->isDoctor()) {
                return redirect()->intended(route('doctor.dashboard'))
                    ->with('success', 'Selamat datang di Portal Layanan Dokter, '.$user->name);
            }

            if ($user->isApoteker()) {
                return redirect()->intended(route('apoteker.dashboard'))
                    ->with('success', 'Selamat datang di Portal Apoteker, '.$user->name);
            }

            return redirect()->intended(route('home'))
                ->with('success', 'Selamat datang kembali, '.$user->name);
        }

        return back()->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'Kombinasi email dan kata sandi tidak cocok dengan data kami.',
            ]);
    }

    /**
     * Handle quick demo login for testing purposes.
     */
    public function quickLogin(string $role): RedirectResponse
    {
        $email = match ($role) {
            'admin' => 'admin@aihealth.com',
            'lina' => 'lina@aihealth.com',
            'saran' => 'saran@aihealth.com',
            'anaya', 'doctor', 'dokter' => 'anaya@aihealth.com',
            'apoteker', 'pharmacist' => 'apoteker@aihealth.com',
            default => 'user@aihealth.com',
        };

        $user = User::where('email', $email)->first();

        // Fallback for doctor default if anaya@ is not yet seeded.
        if (! $user && in_array($role, ['anaya', 'doctor', 'dokter'])) {
            $user = User::where('email', 'dokter@aihealth.com')->first();
        }

        if ($user) {
            Auth::login($user);
            request()->session()->regenerate();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Masuk cepat sebagai Administrator ('.$user->name.')');
            }

            if ($user->isDoctor()) {
                return redirect()->route('doctor.dashboard')
                    ->with('success', 'Masuk cepat sebagai '.$user->name);
            }

            if ($user->isApoteker()) {
                return redirect()->route('apoteker.dashboard')
                    ->with('success', 'Masuk cepat sebagai Apoteker ('.$user->name.')');
            }

            return redirect()->route('home')
                ->with('success', 'Masuk cepat sebagai Pasien / User ('.$user->name.')');
        }

        return redirect()->route('login')
            ->with('error', 'Akun demo tidak ditemukan.');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'Anda telah berhasil keluar.');
    }
}
