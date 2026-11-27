<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfitShareParty extends Model
{
    const PERUSAHAAN_OWNER = 'company_owner';
    const DESAINER = 'designer';
    const KEPALA_STAF = 'head_designer';
    const ADMIN = 'admin';

    protected $fillable = [
        'code',
        'label',
        'sort_order',
        'is_active',
    ];
}
