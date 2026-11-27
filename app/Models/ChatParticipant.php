<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatParticipant extends Model
{
    const CUSTOMER = 'customer';
    const DESAINER = 'designer';

    protected $fillable = [
        'chat_id',
        'user_id',
        'participant_role',
        'joined_at',
        'left_at',
        'last_read_at',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
            'left_at' => 'datetime',
            'last_read_at' => 'datetime',
        ];
    }

    public function chat()
    {
        return $this->belongsTo(ProjectChat::class, 'chat_id');
    }

    // pesan dari lawan bicara yang masuk setelah terakhir dibaca
    public function jumlahBelumDibaca()
    {
        return $this->chat->messages()
            ->where('sender_id', '!=', $this->user_id)
            ->when($this->last_read_at, fn ($query) => $query->where('created_at', '>', $this->last_read_at))
            ->count();
    }
}
