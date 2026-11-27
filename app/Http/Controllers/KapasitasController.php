<?php

namespace App\Http\Controllers;

use App\Models\CapacitySetting;
use App\Services\KapasitasMingguan;
use Illuminate\Http\Request;

class KapasitasController extends Controller
{
    public function index()
    {
        return view('kapasitas.index', [
            'mingguIni' => KapasitasMingguan::ringkasan(now()),
            'riwayat' => CapacitySetting::latest('effective_from')->get(),
        ]);
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'capacity_per_week' => 'required|integer|min:1|max:1000',
            'warning_threshold_percent' => 'required|integer|min:1|max:100',
            'effective_from' => 'required|date',
            'note' => 'nullable|max:255',
        ], [
            'capacity_per_week.required' => 'Kapasitas per minggu wajib diisi',
            'capacity_per_week.integer' => 'Kapasitas harus berupa angka bulat',
            'capacity_per_week.min' => 'Kapasitas minimal 1 layanan',
            'capacity_per_week.max' => 'Kapasitas maksimal 1000 layanan',
            'warning_threshold_percent.required' => 'Batas peringatan wajib diisi',
            'warning_threshold_percent.integer' => 'Batas peringatan harus berupa angka bulat',
            'warning_threshold_percent.min' => 'Batas peringatan minimal 1%',
            'warning_threshold_percent.max' => 'Batas peringatan maksimal 100%',
            'effective_from.required' => 'Tanggal berlaku wajib diisi',
            'note.max' => 'Catatan maksimal 255 karakter',
        ]);

        // tanggal berlaku yang sama cukup diperbarui
        CapacitySetting::updateOrCreate(
            ['effective_from' => $request->effective_from],
            [
                'capacity_per_week' => $request->capacity_per_week,
                'warning_threshold_percent' => $request->warning_threshold_percent,
                'note' => $request->note,
                'created_by' => auth()->id(),
            ]
        );

        return redirect()->route('kapasitas.index')->with('status', 'Kapasitas mingguan berhasil disimpan');
    }
}
