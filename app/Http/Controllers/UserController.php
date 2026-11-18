<?php

namespace App\Http\Controllers;

use App\Models\CredentialIssuance;
use App\Models\Role;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Services\CredentialGenerator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('user_type', User::INTERNAL)
            ->with('roles', 'internalProfile', 'designerSkills.serviceCategory')
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
                'semuaKeahlian' => ServiceCategory::where('is_active', true)->orderBy('sort_order')->get(),
                'idDesainer' => Role::findByName(Role::DESAINER)->id,
            ])
            ->header('Cache-Control', 'no-store');
    }

    public function store(Request $request)
    {
        $idDesainer = Role::findByName(Role::DESAINER)->id;

        $request->validate([
            'name' => 'required|max:100',
            'role_id' => ['required', Rule::in($this->pilihanRole()->pluck('id'))],
            'keahlian' => 'nullable|array',
            'keahlian.*' => 'exists:service_categories,id',
        ], [
            'name.required' => 'Nama lengkap wajib diisi',
            'name.max' => 'Nama lengkap maksimal 100 karakter',
            'role_id.required' => 'Role wajib dipilih',
            'role_id.in' => 'Role tidak tersedia',
            'keahlian.*.exists' => 'Keahlian tidak tersedia',
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

        if ($request->role_id == $idDesainer && $request->filled('keahlian')) {
            foreach ($request->keahlian as $kategoriId) {
                $user->designerSkills()->create(['service_category_id' => $kategoriId]);
            }
        }

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

    public function resetPassword(User $user)
    {
        if (! auth()->user()->canResetPassword($user)) {
            abort(403);
        }

        $password = CredentialGenerator::generatePassword();

        $user->update(['password' => $password]);
        $user->internalProfile->update(['must_change_password' => true]);

        $user->credentialIssuances()->create([
            'issued_by' => auth()->id(),
            'type' => CredentialIssuance::RESET,
            'issued_at' => now(),
        ]);

        return redirect()->route('users.index')->with('kredensial', [
            'judul' => 'Password Berhasil Direset',
            'nip' => $user->internalProfile->nip,
            'password' => $password,
        ]);
    }

    public function destroy(User $user)
    {
        if (! auth()->user()->canDelete($user)) {
            abort(403);
        }

        $user->delete();

        return redirect()->route('users.index')->with('status', 'Akun '.$user->name.' berhasil dihapus');
    }

    private function pilihanRole()
    {
        return Role::whereIn('name', [Role::ADMIN, Role::KEPALA_STAF_DESAINER, Role::DESAINER])->orderBy('id')->get();
    }
}
