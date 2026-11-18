<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class LupaPasswordController extends Controller
{
    public function create()
    {
        return view('customer.lupa-password');
    }

    public function kirim(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Tautan reset password sudah dikirim ke email Anda');
        }

        if ($status === Password::RESET_THROTTLED) {
            return back()->withErrors(['email' => 'Tunggu sebentar sebelum meminta tautan lagi'])->onlyInput('email');
        }

        return back()->withErrors(['email' => 'Email tidak ditemukan'])->onlyInput('email');
    }

    public function edit(Request $request, $token)
    {
        return view('customer.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'confirmed'],
        ], [
            'password.required' => 'Password baru wajib diisi',
            'password.min' => 'Password baru minimal 8 karakter',
            'password.regex' => 'Password baru harus ada huruf besar, huruf kecil, dan angka',
            'password.confirmed' => 'Konfirmasi password tidak sama',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->update(['password' => $request->password]);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('customer.login')->with('status', 'Password berhasil direset, silakan masuk');
        }

        return back()->withErrors(['email' => 'Tautan reset tidak valid atau sudah kedaluwarsa']);
    }
}
