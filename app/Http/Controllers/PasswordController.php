<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PasswordController extends Controller
{
    public function edit()
    {
        return view('auth.password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password_lama' => 'required|current_password',
            'password_baru' => ['required', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'confirmed', 'different:password_lama'],
        ], [
            'password_lama.required' => 'Password lama wajib diisi',
            'password_lama.current_password' => 'Password lama salah',
            'password_baru.required' => 'Password baru wajib diisi',
            'password_baru.min' => 'Password baru minimal 8 karakter',
            'password_baru.regex' => 'Password baru harus ada huruf besar, huruf kecil, dan angka',
            'password_baru.confirmed' => 'Konfirmasi password tidak sama',
            'password_baru.different' => 'Password baru harus beda dari password lama',
        ]);

        $user = $request->user();
        $user->update(['password' => $request->password_baru]);

        $user->internalProfile->update([
            'must_change_password' => false,
            'password_changed_at' => now(),
        ]);

        // password sementara dari sistem sudah tidak berlaku lagi
        $user->credentialIssuances()->whereNull('consumed_at')->update(['consumed_at' => now()]);

        return redirect()->route('beranda')->with('status', 'Password berhasil diubah');
    }
}
