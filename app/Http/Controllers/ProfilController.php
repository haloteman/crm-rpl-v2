<?php

namespace App\Http\Controllers;

use App\Services\WaLinkBuilder;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function show()
    {
        return view('profil.show', ['user' => auth()->user()]);
    }

    public function edit()
    {
        return view('profil.form', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'phone' => ['required', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'institution' => 'nullable|max:255',
        ], [
            'name.required' => 'Nama lengkap wajib diisi',
            'name.max' => 'Nama lengkap maksimal 100 karakter',
            'phone.required' => 'Nomor HP wajib diisi',
            'phone.max' => 'Nomor HP maksimal 20 karakter',
            'phone.regex' => 'Nomor HP hanya boleh berisi angka',
            'institution.max' => 'Instansi maksimal 255 karakter',
        ]);

        $user = auth()->user();
        $user->update(['name' => $request->name]);
        $user->customerProfile->update([
            'phone' => WaLinkBuilder::toInternational($request->phone),
            'institution' => $request->institution,
        ]);

        return redirect()->route('profil.show')->with('status', 'Profil berhasil diperbarui');
    }
}
