<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectSettlement extends Model
{
    protected $fillable = [
        'project_id',
        'scheme_id',
        'gross_revenue',
        'direct_cost_total',
        'net_revenue',
        'rounding_diff',
        'calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'gross_revenue' => 'decimal:2',
            'direct_cost_total' => 'decimal:2',
            'net_revenue' => 'decimal:2',
            'rounding_diff' => 'decimal:2',
            'calculated_at' => 'datetime',
        ];
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function shares()
    {
        return $this->hasMany(ProjectSettlementShare::class, 'settlement_id');
    }

    public function scheme()
    {
        return $this->belongsTo(ProfitShareScheme::class, 'scheme_id');
    }
}
