<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusDefinition extends Model
{
    protected $fillable = [
        'scope',
        'code',
        'label',
        'customer_label',
        'sort_order',
        'allowed_next',
        'is_final',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'allowed_next' => 'array',
            'is_final' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'status_id');
    }

    public static function pesanan($code)
    {
        return static::where('scope', 'order')->where('code', $code)->firstOrFail();
    }

    public static function proyek($code)
    {
        return static::where('scope', 'project')->where('code', $code)->firstOrFail();
    }
}
