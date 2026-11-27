<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfitShareScheme extends Model
{
    protected $fillable = [
        'label',
        'effective_from',
        'is_active',
        'operating_cash_percent',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'effective_from' => 'date',
            'is_active' => 'boolean',
            'operating_cash_percent' => 'decimal:2',
        ];
    }

    public function items()
    {
        return $this->hasMany(ProfitShareSchemeItem::class, 'scheme_id');
    }

    public static function aktif()
    {
        return static::with('items.party')->where('is_active', true)->latest('effective_from')->first();
    }
}
