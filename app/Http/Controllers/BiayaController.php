<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectCost;
use Illuminate\Http\Request;

class BiayaController extends Controller
{
    public function simpan(Request $request, Project $project)
    {
        $project->costs()->create($this->validasi($request) + ['created_by' => auth()->id()]);

        return redirect()->route('pendapatan.proyek', $project)->with('status', 'Biaya langsung berhasil ditambahkan');
    }

    public function ubah(Request $request, ProjectCost $biaya)
    {
        $biaya->update($this->validasi($request));

        return redirect()->route('pendapatan.proyek', $biaya->project_id)->with('status', 'Biaya langsung berhasil diubah');
    }

    public function hapus(ProjectCost $biaya)
    {
        $biaya->delete();

        return redirect()->route('pendapatan.proyek', $biaya->project_id)->with('status', 'Biaya langsung berhasil dihapus');
    }

    private function validasi(Request $request)
    {
        return $request->validate([
            'cost_type_id' => 'required|exists:cost_types,id',
            'amount' => 'required|numeric|min:1',
            'incurred_at' => 'required|date',
            'note' => 'nullable|max:1000',
        ], [
            'cost_type_id.required' => 'Jenis biaya wajib dipilih',
            'cost_type_id.exists' => 'Jenis biaya tidak ditemukan',
            'amount.required' => 'Nominal biaya wajib diisi',
            'amount.min' => 'Nominal biaya harus lebih dari 0',
            'incurred_at.required' => 'Tanggal biaya wajib diisi',
            'note.max' => 'Catatan maksimal 1000 karakter',
        ]);
    }
}
