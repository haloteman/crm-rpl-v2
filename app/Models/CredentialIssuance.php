<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CredentialIssuance extends Model
{
    const INITIAL = 'initial';
    const RESET = 'reset';

    protected $fillable = [
        'user_id',
        'issued_by',
        'type',
        'issued_at',
        'consumed_at',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'consumed_at' => 'datetime',
        ];
    }
}
