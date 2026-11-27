<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectStatusHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'from_status',
        'to_status',
        'changed_by',
        'note',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
        ];
    }
}
