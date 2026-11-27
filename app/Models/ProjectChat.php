<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectChat extends Model
{
    protected $fillable = [
        'project_id',
        'opened_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function participants()
    {
        return $this->hasMany(ChatParticipant::class, 'chat_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_id');
    }

    // hanya peserta yang belum keluar yang boleh membuka chat
    public function pesertaAktif($user)
    {
        return $this->participants()->where('user_id', $user->id)->whereNull('left_at')->first();
    }
}
