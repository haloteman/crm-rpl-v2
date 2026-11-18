<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NipSequence extends Model
{
    protected $fillable = [
        'prefix',
        'last_number',
    ];

    public function next()
    {
        $this->increment('last_number');

        return $this->last_number;
    }
}
