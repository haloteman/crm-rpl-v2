<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'setting_key',
        'setting_value',
        'value_type',
        'description',
        'updated_by',
    ];

    public static function get($key)
    {
        return static::where('setting_key', $key)->value('setting_value');
    }
}
