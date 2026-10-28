<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir login (User / Pelanggan & Petugas Backoffice)
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isAdmin() || $user->isStaff()) {
                return redirect()->route('dashboard');
            }
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    /**
     * Tampilkan formulir registrasi untuk Pelanggan / Role User
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.register');
    }

    /**
     * Proses pendaftaran akun baru dengan role 'user'
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan masuk menggunakan akun Anda.',
            'phone.required' => 'Nomor WhatsApp / telepon wajib diisi untuk konfirmasi pesanan.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('home'))
            ->with('success', 'Akun berhasil didaftarkan! Selamat datang di Bloom Moment, ' . $user->name . ' 🌸');
    }

    /**
     * Proses autentikasi user (Admin, Staff, maupun User Pelanggan)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Jika user adalah Pelanggan / Role User
            if ($user->isUser()) {
                return redirect()->intended(route('home'))
                    ->with('success', 'Selamat datang kembali, ' . $user->name . '! Selamat memilih bunga favorit Anda.');
            }

            // Jika user adalah Admin atau Staff
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . $user->name . ' (' . ucfirst($user->role) . ')!');
        }

        return back()->withErrors([
            'email' => 'Kombinasi email dan kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Proses logout dari sistem
     */
    public function logout(Request $request)
    {
        $wasStaff = Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isStaff());
        
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($wasStaff) {
            return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem backoffice Bloom Moment.');
        }

        return redirect()->route('home')->with('success', 'Anda telah berhasil keluar (logout).');
    }
}
