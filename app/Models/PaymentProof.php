<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentProof extends Model
{
    protected $fillable = [
        'transaction_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
    ];
}
