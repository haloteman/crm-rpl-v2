<?php

namespace App\Services;

use App\Models\PaymentTransaction;
use App\Models\ProjectCost;
use Carbon\Carbon;

class RingkasanPendapatan
{
    // kotor = pembayaran valid, bersih = kotor dikurangi biaya langsung, pesanan dibatalkan/ditolak tidak dihitung
    public static function periode($dari, $sampai)
    {
        $kotor = PaymentTransaction::where('status', PaymentTransaction::VALID)
            ->whereBetween('paid_at', [$dari, $sampai])
            ->whereRelation('order.status', fn ($query) => $query->whereNotIn('code', ['dibatalkan', 'ditolak']))
            ->sum('amount');

        $biaya = ProjectCost::whereBetween('incurred_at', [$dari, $sampai])->sum('amount');

        return [
            'kotor' => $kotor,
            'biaya' => $biaya,
            'bersih' => $kotor - $biaya,
        ];
    }

    public static function bulan($tahun, $bulan)
    {
        $awal = Carbon::create($tahun, $bulan, 1);

        return self::periode($awal->toDateString(), $awal->copy()->endOfMonth()->toDateString());
    }

    public static function tahun($tahun)
    {
        return self::periode($tahun.'-01-01', $tahun.'-12-31');
    }

    // perbandingan kotor dan bersih tiap bulan dalam satu tahun
    public static function perBulan($tahun)
    {
        $hasil = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $hasil[$bulan] = self::bulan($tahun, $bulan);
        }

        return $hasil;
    }
}
