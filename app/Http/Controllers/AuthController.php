<?php

namespace App\Http\Controllers;

use App\Models\InternalProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nip' => 'required',
            'password' => 'required',
        ], [
            'nip.required' => 'NIP wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        $profil = InternalProfile::where('nip', $request->nip)->first();
        $user = $profil?->user;

        // pesan error disamain biar nggak ketahuan NIP mana yang terdaftar
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['nip' => 'NIP atau password salah'])
                ->onlyInput('nip');
        }

        if (! $user->is_active) {
            return back()
                ->withErrors(['nip' => 'Akun Anda sedang tidak aktif'])
                ->onlyInput('nip');
        }

        Auth::login($user);
        $request->session()->regenerate();
        $user->update(['last_login_at' => now()]);

        return redirect()->route('beranda');
    }

    public function logout(Request $request)
    {
        $halamanLogin = $request->user()->user_type === User::CUSTOMER ? 'customer.login' : 'login';

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($halamanLogin);
    }
}
