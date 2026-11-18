<?php

namespace App\Http\Controllers;

use App\Models\CustomerProfile;
use App\Models\Role;
use App\Models\User;
use App\Services\WaLinkBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends Controller
{
    public function showRegister()
    {
        return view('customer.daftar');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => ['required', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'institution' => 'nullable|max:255',
            'password' => ['required', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi',
            'name.max' => 'Nama lengkap maksimal 100 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'phone.required' => 'Nomor HP wajib diisi',
            'phone.max' => 'Nomor HP maksimal 20 karakter',
            'phone.regex' => 'Nomor HP hanya boleh berisi angka',
            'institution.max' => 'Instansi maksimal 255 karakter',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.regex' => 'Password harus ada huruf besar, huruf kecil, dan angka',
            'password.confirmed' => 'Konfirmasi password tidak sama',
        ]);

        $user = User::create([
            'user_type' => User::CUSTOMER,
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // customer yang baru daftar otomatis masuk CRM sebagai lead
        $user->customerProfile()->create([
            'phone' => WaLinkBuilder::toInternational($request->phone),
            'institution' => $request->institution,
            'status' => CustomerProfile::LEAD,
            'lead_source' => 'website',
            'registered_at' => now(),
        ]);

        $user->assignRole(Role::CUSTOMER);

        return redirect()->route('customer.login')->with('status', 'Registrasi berhasil, silakan login');
    }

    public function showLogin()
    {
        return view('customer.masuk');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
        ]);

        $user = User::where('user_type', User::CUSTOMER)->where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['email' => 'Email atau password salah'])
                ->onlyInput('email');
        }

        if (! $user->is_active) {
            return back()
                ->withErrors(['email' => 'Akun Anda sedang tidak aktif'])
                ->onlyInput('email');
        }

        Auth::login($user);
        $request->session()->regenerate();
        $user->update(['last_login_at' => now()]);

        return redirect()->route('beranda');
    }
}
