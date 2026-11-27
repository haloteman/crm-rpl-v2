<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    const PERTANYAAN = 'question';
    const PERMINTAAN_REVISI = 'revision_request';
    const BALASAN = 'reply';

    protected $fillable = [
        'chat_id',
        'sender_id',
        'message_type',
        'body',
    ];

    public function chat()
    {
        return $this->belongsTo(ProjectChat::class, 'chat_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function attachments()
    {
        return $this->hasMany(ChatAttachment::class, 'message_id');
    }
}
