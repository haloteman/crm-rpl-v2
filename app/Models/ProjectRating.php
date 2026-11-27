<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectRating extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'project_id',
        'customer_id',
        'rating',
        'review',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
