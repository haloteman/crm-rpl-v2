<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderReference extends Model
{
    const FILE = 'file';
    const LINK = 'link';

    protected $fillable = [
        'order_id',
        'reference_type',
        'url_or_path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
