<?php

namespace App\Http\Controllers;

use App\Models\ProfitShareParty;
use App\Models\ProfitShareScheme;
use Illuminate\Http\Request;

class SkemaBagiHasilController extends Controller
{
    public function index()
    {
        return view('bagi-hasil.skema', [
            'skemaAktif' => ProfitShareScheme::aktif(),
            'riwayat' => ProfitShareScheme::with('items.party')->latest('effective_from')->latest('id')->get(),
            'pihak' => ProfitShareParty::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    // setiap perubahan disimpan sebagai versi baru, proyek yang sudah dihitung tidak berubah
    public function simpan(Request $request)
    {
        $pihak = ProfitShareParty::where('is_active', true)->orderBy('sort_order')->get();

        $request->validate([
            'label' => 'required|max:100',
            'effective_from' => 'required|date',
            'persen' => 'required|array',
            'persen.*' => 'required|numeric|min:0|max:100',
        ], [
            'label.required' => 'Nama skema wajib diisi',
            'label.max' => 'Nama skema maksimal 100 karakter',
            'effective_from.required' => 'Tanggal berlaku wajib diisi',
            'persen.*.required' => 'Persentase setiap pihak wajib diisi',
            'persen.*.numeric' => 'Persentase harus berupa angka',
            'persen.*.min' => 'Persentase minimal 0',
            'persen.*.max' => 'Persentase maksimal 100',
        ]);

        $total = 0;

        foreach ($pihak as $p) {
            $total += $request->input('persen.'.$p->id, 0);
        }

        if ($total != 100) {
            return back()->withErrors(['persen' => 'Total persentase harus 100%, sekarang '.$total.'%'])->withInput();
        }

        $skemaLama = ProfitShareScheme::aktif();

        ProfitShareScheme::where('is_active', true)->update(['is_active' => false]);

        $skema = ProfitShareScheme::create([
            'label' => $request->label,
            'effective_from' => $request->effective_from,
            'is_active' => true,
            'operating_cash_percent' => $skemaLama?->operating_cash_percent,
            'created_by' => auth()->id(),
        ]);

        foreach ($pihak as $p) {
            $skema->items()->create([
                'party_id' => $p->id,
                'percent' => $request->input('persen.'.$p->id, 0),
            ]);
        }

        return redirect()->route('skema.index')->with('status', 'Skema pembagian hasil berhasil disimpan');
    }
}
