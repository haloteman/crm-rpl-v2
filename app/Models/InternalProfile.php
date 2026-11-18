<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternalProfile extends Model
{
    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'nip',
        'must_change_password',
        'password_changed_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'must_change_password' => 'boolean',
            'password_changed_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
