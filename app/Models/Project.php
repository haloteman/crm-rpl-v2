<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'order_id',
        'status_id',
        'designer_id',
        'deadline',
        'progress_percentage',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function status()
    {
        return $this->belongsTo(StatusDefinition::class, 'status_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(ProjectStatusHistory::class);
    }
}
