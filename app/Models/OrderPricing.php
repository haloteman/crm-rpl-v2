<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPricing extends Model
{
    const PERSEN = 'percent';
    const NOMINAL = 'nominal';

    protected $fillable = [
        'order_id',
        'final_price',
        'dp_type',
        'dp_value',
        'dp_amount',
        'set_by',
        'set_at',
    ];

    protected function casts(): array
    {
        return [
            'final_price' => 'decimal:2',
            'dp_value' => 'decimal:2',
            'dp_amount' => 'decimal:2',
            'set_at' => 'datetime',
        ];
    }

    public function sisaPelunasan()
    {
        return $this->final_price - $this->dp_amount;
    }
}
