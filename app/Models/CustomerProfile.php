<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerProfile extends Model
{
    const LEAD = 'lead';
    const NEW_CUSTOMER = 'new_customer';
    const RETURNING_CUSTOMER = 'returning_customer';

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'phone',
        'institution',
        'status',
        'lead_source',
        'registered_at',
        'converted_at',
        'returning_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
            'converted_at' => 'datetime',
            'returning_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
