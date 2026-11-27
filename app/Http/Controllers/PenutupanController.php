<?php

namespace App\Http\Controllers;

use App\Models\ClosureReason;
use App\Models\Order;
use App\Models\OrderClosure;
use Illuminate\Http\Request;

class PenutupanController extends Controller
{
    // dibatalkan atas permintaan customer
    public function batal(Request $request, Order $order)
    {
        return $this->tutup($request, $order, OrderClosure::BATAL, 'dibatalkan');
    }

    // ditolak Owner karena kapasitas penuh atau tenggat terlalu singkat
    public function tolak(Request $request, Order $order)
    {
        return $this->tutup($request, $order, OrderClosure::TOLAK, 'ditolak');
    }

    private function tutup(Request $request, Order $order, $jenis, $statusBaru)
    {
        // hanya bisa sebelum DP diterima
        if (! $order->bolehPindahKe($statusBaru)) {
            return back()->withErrors(['status' => 'Pesanan yang DP-nya sudah diterima tidak bisa dibatalkan atau ditolak']);
        }

        $request->validate([
            'reason_id' => 'required',
            'reason_note' => 'required',
        ], [
            'reason_id.required' => 'Alasan wajib dipilih',
            'reason_note.required' => 'Keterangan alasan wajib diisi',
        ]);

        $alasan = ClosureReason::where('closure_type', $jenis)->where('is_active', true)->find($request->reason_id);

        if (! $alasan) {
            return back()->withErrors(['reason_id' => 'Alasan tidak ditemukan'])->withInput();
        }

        $order->closure()->create([
            'closure_type' => $jenis,
            'reason_id' => $alasan->id,
            'reason_note' => $request->reason_note,
            'closed_by' => auth()->id(),
            'closed_at' => now(),
        ]);

        $order->ubahStatus($statusBaru);

        return redirect()->route('pesanan-masuk.show', $order)->with('status', 'Pesanan '.$statusBaru);
    }
}
