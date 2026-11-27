<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClosureReason extends Model
{
    protected $fillable = [
        'closure_type',
        'code',
        'label',
        'sort_order',
        'is_active',
    ];
}
