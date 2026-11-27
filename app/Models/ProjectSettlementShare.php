<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectSettlementShare extends Model
{
    protected $fillable = [
        'settlement_id',
        'party_id',
        'recipient_user_id',
        'percent',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'percent' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }

    public function party()
    {
        return $this->belongsTo(ProfitShareParty::class, 'party_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    public function settlement()
    {
        return $this->belongsTo(ProjectSettlement::class, 'settlement_id');
    }
}
