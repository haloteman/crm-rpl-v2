<?php

namespace App\Http\Controllers;

use App\Models\CustomerProfile;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\StatusDefinition;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function simpanDp(Request $request, Order $order)
    {
        // DP hanya dicatat sekali, saat pesanan Menunggu DP
        if ($order->status->code != 'menunggu_dp' || $order->payment_status != Order::BELUM_BAYAR) {
            abort(403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'paid_at' => 'required|date|before_or_equal:today',
            'method' => 'required|in:transfer,qris',
        ], [
            'amount.required' => 'Nominal diterima wajib diisi',
            'amount.min' => 'Nominal diterima harus lebih dari 0',
            'paid_at.required' => 'Tanggal diterima wajib diisi',
            'paid_at.before_or_equal' => 'Tanggal diterima tidak boleh lebih dari hari ini',
            'method.required' => 'Metode pembayaran wajib dipilih',
        ]);

        $order->payments()->create([
            'customer_id' => $order->customer_id,
            'payment_type' => PaymentTransaction::DP,
            'amount' => $request->amount,
            'paid_at' => $request->paid_at,
            'method' => $request->method,
            'recorded_by' => auth()->id(),
            'status' => PaymentTransaction::VALID,
        ]);

        $order->payment_status = Order::DP_DITERIMA;
        $order->ubahStatus('dp_diterima');

        // proyek otomatis muncul untuk Kepala Staf Desainer
        $proyek = $order->project()->create([
            'status_id' => StatusDefinition::proyek('menunggu_penugasan')->id,
            'deadline' => $order->expected_deadline,
        ]);

        $proyek->statusHistories()->create([
            'to_status' => 'menunggu_penugasan',
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);

        // lead jadi new customer saat DP pertama, berikutnya jadi pelanggan lama
        $profil = $order->customer->customerProfile;

        if ($profil->status == CustomerProfile::LEAD) {
            $profil->update(['status' => CustomerProfile::NEW_CUSTOMER, 'converted_at' => now()]);
        } elseif ($profil->status == CustomerProfile::NEW_CUSTOMER) {
            $profil->update(['status' => CustomerProfile::RETURNING_CUSTOMER, 'returning_at' => now()]);
        }

        return redirect()->route('pesanan-masuk.show', $order)->with('status', 'DP berhasil dicatat, proyek sudah dibuat');
    }
}
