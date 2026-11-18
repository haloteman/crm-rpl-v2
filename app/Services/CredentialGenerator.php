<?php

namespace App\Services;

use App\Models\NipSequence;
use Illuminate\Support\Str;

class CredentialGenerator
{
    // contoh NIP: MC-26-0001 (MC + 2 digit tahun + nomor urut 4 digit)
    public static function generateNip()
    {
        $prefix = 'MC-'.date('y');
        $urutan = NipSequence::firstOrCreate(['prefix' => $prefix]);

        return $prefix.'-'.str_pad($urutan->next(), 4, '0', STR_PAD_LEFT);
    }

    public static function generatePassword()
    {
        return Str::password(12, symbols: false);
    }
}
