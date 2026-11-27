<?php

namespace App\Services;

use App\Models\CapacitySetting;
use App\Models\Order;
use Carbon\Carbon;

class KapasitasMingguan
{
    const TERSEDIA = 'available';
    const HAMPIR_PENUH = 'almost_full';
    const PENUH = 'full';

    // ringkasan kapasitas untuk minggu (Senin-Minggu) yang memuat tanggal ini
    public static function ringkasan($tanggal)
    {
        $awal = Carbon::parse($tanggal)->startOfWeek();
        $akhir = Carbon::parse($tanggal)->endOfWeek();

        $pengaturan = CapacitySetting::untukMinggu($akhir->toDateString());
        $kapasitas = $pengaturan?->capacity_per_week ?? 0;
        $batasPeringatan = $pengaturan?->warning_threshold_percent ?? 100;

        // layanan terisi = pesanan dengan tenggat di minggu ini, kecuali yang dibatalkan atau ditolak
        $terisi = Order::whereBetween('expected_deadline', [$awal->toDateString(), $akhir->toDateString()])
            ->whereRelation('status', fn ($query) => $query->whereNotIn('code', ['dibatalkan', 'ditolak']))
            ->count();

        if ($terisi >= $kapasitas) {
            $status = self::PENUH;
        } elseif ($terisi * 100 / $kapasitas >= $batasPeringatan) {
            $status = self::HAMPIR_PENUH;
        } else {
            $status = self::TERSEDIA;
        }

        return [
            'awal_minggu' => $awal,
            'akhir_minggu' => $akhir,
            'kapasitas' => $kapasitas,
            'terisi' => $terisi,
            'sisa' => max($kapasitas - $terisi, 0),
            'status' => $status,
        ];
    }

    // dipakai form pesanan untuk memberi peringatan sesuai tenggat yang dipilih
    public static function beberapaMinggu($jumlahMinggu)
    {
        $hasil = [];

        for ($i = 0; $i < $jumlahMinggu; $i++) {
            $hasil[] = self::ringkasan(now()->addWeeks($i));
        }

        return $hasil;
    }
}
