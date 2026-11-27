<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectCost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'cost_type_id',
        'amount',
        'incurred_at',
        'note',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'incurred_at' => 'date',
        ];
    }

    public function costType()
    {
        return $this->belongsTo(CostType::class);
    }
}
