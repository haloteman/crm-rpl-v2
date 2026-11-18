<?php

namespace App\Services;

use App\Models\SystemSetting;

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

    public static function buildAdminInquiry($layanan = null)
    {
        $pesan = 'Halo Admin Media Creative, saya ingin bertanya';

        if ($layanan) {
            $pesan .= ' tentang layanan '.$layanan->name;
        }

        return 'https://wa.me/'.ltrim(SystemSetting::get('wa_admin'), '+').'?text='.urlencode($pesan.'.');
    }
}
