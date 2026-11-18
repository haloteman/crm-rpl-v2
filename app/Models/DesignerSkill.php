<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignerSkill extends Model
{
    protected $fillable = [
        'user_id',
        'service_category_id',
        'proficiency',
    ];

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
    }
}
