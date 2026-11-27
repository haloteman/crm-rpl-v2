<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressAttachment extends Model
{
    const FILE = 'file';
    const LINK = 'link';

    const UPDATED_AT = null;

    protected $fillable = [
        'progress_update_id',
        'attachment_type',
        'url_or_path',
        'original_name',
        'mime_type',
        'file_size',
        'is_final_result',
    ];

    protected function casts(): array
    {
        return [
            'is_final_result' => 'boolean',
        ];
    }

    public function progressUpdate()
    {
        return $this->belongsTo(ProjectProgressUpdate::class, 'progress_update_id');
    }
}
