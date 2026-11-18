<?php

namespace App\Services;

class WaLinkBuilder
{
    // contoh: "0812-3456 7890" jadi "+6281234567890"
    public static function toInternational($phone)
    {
        $angka = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($angka, '0')) {
            $angka = '62'.substr($angka, 1);
        }

        return '+'.$angka;
    }
}
