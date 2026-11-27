<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatAttachment extends Model
{
    protected $fillable = [
        'message_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    public function message()
    {
        return $this->belongsTo(ChatMessage::class, 'message_id');
    }
}
