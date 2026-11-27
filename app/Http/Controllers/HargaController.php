<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderPricing;
use Illuminate\Http\Request;

class HargaController extends Controller
{
    public function simpan(Request $request, Order $order)
    {
        if ($order->status->code != 'menunggu_konsultasi') {
            abort(403);
        }

        $request->validate([
            'final_price' => 'required|numeric|min:1',
            'dp_type' => 'required|in:'.OrderPricing::PERSEN.','.OrderPricing::NOMINAL,
            'dp_value' => 'required|numeric|min:1',
        ], [
            'final_price.required' => 'Harga akhir wajib diisi',
            'final_price.min' => 'Harga akhir harus lebih dari 0',
            'dp_value.required' => 'Besaran DP wajib diisi',
            'dp_value.min' => 'Besaran DP harus lebih dari 0',
        ]);

        if ($request->dp_type == OrderPricing::PERSEN) {
            $dp = $request->final_price * $request->dp_value / 100;
        } else {
            $dp = $request->dp_value;
        }

        if ($dp > $request->final_price) {
            return back()->withErrors(['dp_value' => 'DP tidak boleh melebihi harga akhir'])->withInput();
        }

        $order->pricing()->create([
            'final_price' => $request->final_price,
            'dp_type' => $request->dp_type,
            'dp_value' => $request->dp_value,
            'dp_amount' => $dp,
            'set_by' => auth()->id(),
            'set_at' => now(),
        ]);

        // catatan konsultasi dikunci setelah harga ditetapkan
        $order->consultation()->updateOrCreate([], ['locked_at' => now()]);

        $order->ubahStatus('menunggu_dp');

        return redirect()->route('pesanan-masuk.show', $order)->with('status', 'Harga akhir dan DP berhasil ditetapkan');
    }
}
