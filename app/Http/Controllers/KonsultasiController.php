<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class KonsultasiController extends Controller
{
    public function simpan(Request $request, Order $order)
    {
        if (! $order->bolehCatatKonsultasi()) {
            abort(403);
        }

        $request->validate([
            'notes' => 'required',
            'revision_terms' => 'nullable',
        ], [
            'notes.required' => 'Hasil konsultasi wajib diisi',
        ]);

        // status pesanan tetap Menunggu Konsultasi sampai harga dan DP ditetapkan
        $order->consultation()->updateOrCreate([], [
            'notes' => $request->notes,
            'revision_terms' => $request->revision_terms,
            'noted_by' => auth()->id(),
        ]);

        return redirect()->route('pesanan-masuk.show', $order)->with('status', 'Catatan konsultasi berhasil disimpan');
    }
}
