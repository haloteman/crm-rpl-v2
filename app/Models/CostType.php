<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostType extends Model
{
    protected $fillable = [
        'code',
        'label',
        'sort_order',
        'is_active',
    ];
}
