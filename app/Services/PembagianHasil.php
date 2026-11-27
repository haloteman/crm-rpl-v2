<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProfitShareParty;
use App\Models\ProfitShareScheme;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;

class PembagianHasil
{
    // dihitung sekali saat proyek Selesai dan lunas, persentase disalin supaya tidak ikut berubah
    public static function hitung(Project $project)
    {
        if ($project->status->code != 'selesai' || $project->order->payment_status != Order::LUNAS || $project->settlement) {
            return;
        }

        $skema = ProfitShareScheme::aktif();

        $kotor = $project->order->totalDibayar();
        $biaya = $project->costs()->sum('amount');
        $bersih = $kotor - $biaya;

        $bagian = [];
        $totalBagian = 0;

        foreach ($skema->items as $item) {
            // dibulatkan ke bawah ke rupiah
            $nominal = floor($bersih * $item->percent / 100);
            $totalBagian += $nominal;

            $bagian[] = [
                'party_id' => $item->party_id,
                'recipient_user_id' => self::penerima($item->party->code, $project),
                'percent' => $item->percent,
                'amount' => $nominal,
            ];
        }

        // selisih pembulatan masuk ke Kas Perusahaan dan Owner
        $selisih = $bersih - $totalBagian;

        foreach ($bagian as $i => $b) {
            if ($skema->items[$i]->party->code == ProfitShareParty::PERUSAHAAN_OWNER) {
                $bagian[$i]['amount'] += $selisih;
            }
        }

        $settlement = $project->settlement()->create([
            'scheme_id' => $skema->id,
            'gross_revenue' => $kotor,
            'direct_cost_total' => $biaya,
            'net_revenue' => $bersih,
            'rounding_diff' => $selisih,
            'calculated_at' => now(),
        ]);

        foreach ($bagian as $b) {
            $settlement->shares()->create($b);
        }
    }

    // bagian Desainer untuk yang mengerjakan proyek, Kas dan Admin sebagai satu kelompok
    private static function penerima($kodePihak, Project $project)
    {
        if ($kodePihak == ProfitShareParty::DESAINER) {
            return $project->designer_id;
        }

        if ($kodePihak == ProfitShareParty::KEPALA_STAF) {
            return User::role(Role::KEPALA_STAF_DESAINER)->first()?->id;
        }

        return null;
    }
}
