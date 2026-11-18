<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderConsultation extends Model
{
    protected $fillable = [
        'order_id',
        'notes',
        'revision_terms',
        'noted_by',
        'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'locked_at' => 'datetime',
        ];
    }
}
