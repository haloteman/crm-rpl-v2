<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    const DP = 'dp';
    const PELUNASAN = 'settlement';

    const VALID = 'valid';
    const DIBATALKAN = 'cancelled';

    protected $fillable = [
        'order_id',
        'customer_id',
        'payment_type',
        'amount',
        'paid_at',
        'method',
        'recorded_by',
        'status',
        'cancel_reason',
        'cancelled_at',
        'cancelled_by',
        'replaces_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'date',
            'cancelled_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
