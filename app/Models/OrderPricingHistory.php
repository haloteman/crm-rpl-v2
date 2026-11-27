<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPricingHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'old_final_price',
        'old_dp_type',
        'old_dp_value',
        'new_final_price',
        'new_dp_type',
        'new_dp_value',
        'changed_by',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
        ];
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
