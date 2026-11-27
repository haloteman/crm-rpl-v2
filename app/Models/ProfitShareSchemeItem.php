<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfitShareSchemeItem extends Model
{
    protected $fillable = [
        'scheme_id',
        'party_id',
        'percent',
    ];

    protected function casts(): array
    {
        return [
            'percent' => 'decimal:2',
        ];
    }

    public function party()
    {
        return $this->belongsTo(ProfitShareParty::class, 'party_id');
    }
}
