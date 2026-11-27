<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, Notifiable, SoftDeletes;

    const INTERNAL = 'internal';
    const CUSTOMER = 'customer';

    protected $fillable = [
        'user_type',
        'name',
        'email',
        'password',
        'is_active',
        'last_login_at',
        'created_by',
        'meta',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function internalProfile()
    {
        return $this->hasOne(InternalProfile::class);
    }

    public function customerProfile()
    {
        return $this->hasOne(CustomerProfile::class);
    }

    public function credentialIssuances()
    {
        return $this->hasMany(CredentialIssuance::class);
    }

    public function designerSkills()
    {
        return $this->hasMany(DesignerSkill::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'designer_id');
    }

    // desainer dan kepala staf beserta keahlian dan jumlah proyek aktif, untuk menimbang beban kerja
    public static function daftarDesainer()
    {
        return static::role([Role::DESAINER, Role::KEPALA_STAF_DESAINER])
            ->with('designerSkills.service')
            ->withCount(['projects as proyek_aktif' => function ($query) {
                $query->whereRelation('status', 'code', '!=', 'selesai');
            }])
            ->orderBy('name')
            ->get();
    }

    // tidak ada yang bisa reset atau hapus akunnya sendiri, dan akun Owner tidak bisa dihapus
    public function canResetPassword($target)
    {
        return $this->can('user.reset-password') && $target->id !== $this->id && ! $target->hasRole(Role::OWNER);
    }

    public function canDelete($target)
    {
        return $this->can('user.delete') && $target->id !== $this->id && ! $target->hasRole(Role::OWNER);
    }
}
