<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\WaLinkBuilder;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('user_type', User::CUSTOMER)
            ->with('customerProfile')
            ->latest();

        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->cari.'%')
                    ->orWhere('email', 'like', '%'.$request->cari.'%')
                    ->orWhereRelation('customerProfile', 'phone', 'like', '%'.$request->cari.'%')
                    ->orWhereRelation('customerProfile', 'institution', 'like', '%'.$request->cari.'%');
            });
        }

        if ($request->filled('status')) {
            $query->whereRelation('customerProfile', 'status', $request->status);
        }

        return view('pelanggan.index', [
            'pelanggans' => $query->paginate(10)->withQueryString(),
        ]);
    }

    public function show(User $pelanggan)
    {
        $this->pastikanCustomer($pelanggan);

        return view('pelanggan.show', ['pelanggan' => $pelanggan]);
    }

    public function edit(User $pelanggan)
    {
        $this->pastikanCustomer($pelanggan);

        return view('pelanggan.form', ['pelanggan' => $pelanggan]);
    }

    public function update(Request $request, User $pelanggan)
    {
        $this->pastikanCustomer($pelanggan);

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

        $pelanggan->update(['name' => $request->name]);
        $pelanggan->customerProfile->update([
            'phone' => WaLinkBuilder::toInternational($request->phone),
            'institution' => $request->institution,
        ]);

        return redirect()->route('pelanggan.show', $pelanggan)->with('status', 'Data kontak pelanggan berhasil diperbarui');
    }

    private function pastikanCustomer(User $user)
    {
        if ($user->user_type !== User::CUSTOMER) {
            abort(404);
        }
    }
}
