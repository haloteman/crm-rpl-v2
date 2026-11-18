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
}
