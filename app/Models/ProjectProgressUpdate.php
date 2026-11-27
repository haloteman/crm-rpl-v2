<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectProgressUpdate extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'project_id',
        'updated_by',
        'stage',
        'percentage',
        'note',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function attachments()
    {
        return $this->hasMany(ProgressAttachment::class, 'progress_update_id');
    }
}
