<?php

namespace App\Http\Controllers;

use App\Models\CredentialIssuance;
use App\Models\Role;
use App\Models\User;
use App\Services\CredentialGenerator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('user_type', User::INTERNAL)
            ->with('roles', 'internalProfile')
            ->orderBy('name');

        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->cari.'%')
                    ->orWhereRelation('internalProfile', 'nip', 'like', '%'.$request->cari.'%');
            });
        }

        if ($request->filled('role')) {
            $query->whereRelation('roles', 'roles.id', $request->role);
        }

        // biar password sementara nggak kesimpen di cache browser
        return response()
            ->view('users.index', [
                'users' => $query->paginate(10)->withQueryString(),
                'totalAkun' => User::where('user_type', User::INTERNAL)->count(),
                'semuaRole' => Role::where('name', '!=', Role::CUSTOMER)->orderBy('id')->get(),
                'pilihanRole' => $this->pilihanRole(),
            ])
            ->header('Cache-Control', 'no-store');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'role_id' => ['required', Rule::in($this->pilihanRole()->pluck('id'))],
        ], [
            'name.required' => 'Nama lengkap wajib diisi',
            'name.max' => 'Nama lengkap maksimal 100 karakter',
            'role_id.required' => 'Role wajib dipilih',
            'role_id.in' => 'Role tidak tersedia',
        ]);

        $password = CredentialGenerator::generatePassword();

        $user = User::create([
            'user_type' => User::INTERNAL,
            'name' => $request->name,
            'password' => $password,
            'created_by' => auth()->id(),
        ]);

        $profil = $user->internalProfile()->create([
            'nip' => CredentialGenerator::generateNip(),
            'must_change_password' => true,
        ]);

        $user->assignRole(Role::findById($request->role_id));

        $user->credentialIssuances()->create([
            'issued_by' => auth()->id(),
            'type' => CredentialIssuance::INITIAL,
            'issued_at' => now(),
        ]);

        return redirect()->route('users.index')->with('kredensial', [
            'judul' => 'Akun Berhasil Dibuat',
            'nip' => $profil->nip,
            'password' => $password,
        ]);
    }

    private function pilihanRole()
    {
        return Role::whereIn('name', [Role::ADMIN, Role::KEPALA_STAF_DESAINER, Role::DESAINER])->orderBy('id')->get();
    }
}
