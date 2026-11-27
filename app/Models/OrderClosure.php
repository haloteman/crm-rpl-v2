<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderClosure extends Model
{
    const BATAL = 'cancelled';
    const TOLAK = 'rejected';

    protected $fillable = [
        'order_id',
        'closure_type',
        'reason_id',
        'reason_note',
        'closed_by',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'closed_at' => 'datetime',
        ];
    }

    public function reason()
    {
        return $this->belongsTo(ClosureReason::class, 'reason_id');
    }
}
