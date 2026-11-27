<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapacitySetting extends Model
{
    protected $fillable = [
        'capacity_per_week',
        'warning_threshold_percent',
        'effective_from',
        'note',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'effective_from' => 'date',
        ];
    }

    // pengaturan yang berlaku = yang terakhir mulai berlaku sampai minggu tersebut
    public static function untukMinggu($akhirMinggu)
    {
        return static::where('effective_from', '<=', $akhirMinggu)->latest('effective_from')->first();
    }
}
