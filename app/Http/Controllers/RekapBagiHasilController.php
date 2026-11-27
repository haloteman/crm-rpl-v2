<?php

namespace App\Http\Controllers;

use App\Models\ProfitShareParty;
use App\Models\ProfitShareScheme;
use App\Models\ProjectSettlementShare;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RekapBagiHasilController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);
        $bulan = $request->input('bulan');

        if ($bulan) {
            $dari = Carbon::create($tahun, $bulan, 1)->startOfDay();
            $sampai = $dari->copy()->endOfMonth();
        } else {
            $dari = Carbon::create($tahun, 1, 1)->startOfDay();
            $sampai = $dari->copy()->endOfYear();
        }

        $bagian = ProjectSettlementShare::with('party', 'recipient', 'settlement.project.order')
            ->whereHas('settlement', fn ($query) => $query->whereBetween('calculated_at', [$dari, $sampai]))
            ->get();

        $perPihak = $bagian->groupBy('party_id')->map(fn ($grup) => [
            'pihak' => $grup->first()->party,
            'total' => $grup->sum('amount'),
        ])->sortBy('pihak.sort_order')->values();

        // bagian setiap Desainer berdasarkan proyek yang dikerjakannya
        $perDesainer = $bagian->where('party.code', ProfitShareParty::DESAINER)->groupBy('recipient_user_id')->map(fn ($grup) => [
            'desainer' => $grup->first()->recipient,
            'jumlah_proyek' => $grup->count(),
            'total' => $grup->sum('amount'),
            'proyek' => $grup,
        ])->values();

        $skema = ProfitShareScheme::aktif();
        $bagianOwner = $bagian->where('party.code', ProfitShareParty::PERUSAHAAN_OWNER)->sum('amount');
        $kasOperasional = floor($bagianOwner * ($skema?->operating_cash_percent ?? 0) / 100);

        return view('bagi-hasil.rekap', [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'perPihak' => $perPihak,
            'perDesainer' => $perDesainer,
            'persenKas' => $skema?->operating_cash_percent,
            'kasOperasional' => $kasOperasional,
            'sisaOwner' => $bagianOwner - $kasOperasional,
        ]);
    }

    public function simpanKas(Request $request)
    {
        $request->validate([
            'operating_cash_percent' => 'required|numeric|min:0|max:100',
        ], [
            'operating_cash_percent.required' => 'Persentase kas operasional wajib diisi',
            'operating_cash_percent.numeric' => 'Persentase harus berupa angka',
            'operating_cash_percent.min' => 'Persentase minimal 0',
            'operating_cash_percent.max' => 'Persentase maksimal 100',
        ]);

        ProfitShareScheme::aktif()->update(['operating_cash_percent' => $request->operating_cash_percent]);

        return redirect()->route('rekap.index')->with('status', 'Kas operasional berhasil disimpan');
    }
}
