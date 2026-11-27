<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectAssignment extends Model
{
    const AWAL = 'initial';
    const PINDAH = 'transfer';

    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'designer_id',
        'assigned_by',
        'assignment_type',
        'is_current',
        'assigned_at',
        'ended_at',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'is_current' => 'boolean',
            'assigned_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function designer()
    {
        return $this->belongsTo(User::class, 'designer_id');
    }
}
