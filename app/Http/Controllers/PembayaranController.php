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
            'amount' => 'required|numeric|min:1|max:'.$order->pricing->final_price,
            'paid_at' => 'required|date|before_or_equal:today',
            'method' => 'required|in:transfer,qris',
        ], [
            'amount.required' => 'Nominal diterima wajib diisi',
            'amount.min' => 'Nominal diterima harus lebih dari 0',
            'amount.max' => 'Nominal melebihi sisa tagihan',
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

    public function simpanPelunasan(Request $request, Order $order)
    {
        if ($order->payment_status != Order::DP_DITERIMA) {
            abort(403);
        }

        $sisa = $order->sisaTagihan();

        $request->validate([
            'amount' => 'required|numeric|min:1|max:'.$sisa,
            'paid_at' => 'required|date|before_or_equal:today',
            'method' => 'required|in:transfer,qris',
        ], [
            'amount.required' => 'Nominal pelunasan wajib diisi',
            'amount.min' => 'Nominal pelunasan harus lebih dari 0',
            'amount.max' => 'Nominal melebihi sisa tagihan',
            'paid_at.required' => 'Tanggal pembayaran wajib diisi',
            'paid_at.before_or_equal' => 'Tanggal pembayaran tidak boleh lebih dari hari ini',
            'method.required' => 'Metode pembayaran wajib dipilih',
        ]);

        $order->payments()->create([
            'customer_id' => $order->customer_id,
            'payment_type' => PaymentTransaction::PELUNASAN,
            'amount' => $request->amount,
            'paid_at' => $request->paid_at,
            'method' => $request->method,
            'recorded_by' => auth()->id(),
            'status' => PaymentTransaction::VALID,
        ]);

        // pelunasan boleh bertahap, status Lunas kalau sisa tagihan sudah nol
        if ($request->amount == $sisa) {
            $order->update(['payment_status' => Order::LUNAS]);
        }

        return redirect()->route('pesanan-masuk.show', $order)->with('status', 'Pelunasan berhasil dicatat');
    }

    // pencatatan yang salah tidak ditimpa: transaksi lama dibatalkan, koreksinya dicatat sebagai transaksi baru
    public function koreksi(Request $request, PaymentTransaction $transaksi)
    {
        $order = $transaksi->order;

        if ($transaksi->status != PaymentTransaction::VALID) {
            abort(403);
        }

        // DP hanya bisa dikoreksi selama proyek belum ditugaskan
        if ($transaksi->payment_type == PaymentTransaction::DP && $order->project->status->code != 'menunggu_penugasan') {
            return back()->withErrors(['koreksi' => 'DP tidak bisa dikoreksi karena proyek sudah ditugaskan']);
        }

        if ($transaksi->payment_type == PaymentTransaction::DP && $request->aksi == 'batal') {
            return back()->withErrors(['koreksi' => 'DP hanya bisa dikoreksi, tidak bisa dibatalkan']);
        }

        $request->validate([
            'aksi' => 'required|in:batal,koreksi',
            'cancel_reason' => 'required',
        ], [
            'aksi.required' => 'Pilih batalkan atau koreksi',
            'cancel_reason.required' => 'Alasan wajib diisi',
        ]);

        if ($request->aksi == 'koreksi') {
            $sisa = $order->sisaTagihan() + $transaksi->amount;

            $request->validate([
                'amount' => 'required|numeric|min:1|max:'.$sisa,
                'paid_at' => 'required|date|before_or_equal:today',
                'method' => 'required|in:transfer,qris',
            ], [
                'amount.required' => 'Nominal wajib diisi',
                'amount.min' => 'Nominal harus lebih dari 0',
                'amount.max' => 'Nominal melebihi sisa tagihan',
                'paid_at.required' => 'Tanggal pembayaran wajib diisi',
                'paid_at.before_or_equal' => 'Tanggal pembayaran tidak boleh lebih dari hari ini',
                'method.required' => 'Metode pembayaran wajib dipilih',
            ]);
        }

        $transaksi->update([
            'status' => PaymentTransaction::DIBATALKAN,
            'cancel_reason' => $request->cancel_reason,
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id(),
        ]);

        if ($request->aksi == 'koreksi') {
            $order->payments()->create([
                'customer_id' => $order->customer_id,
                'payment_type' => $transaksi->payment_type,
                'amount' => $request->amount,
                'paid_at' => $request->paid_at,
                'method' => $request->method,
                'recorded_by' => auth()->id(),
                'status' => PaymentTransaction::VALID,
                'replaces_id' => $transaksi->id,
            ]);
        }

        // status pembayaran dihitung ulang dari transaksi yang masih valid
        $order->load('payments');

        if ($order->sisaTagihan() <= 0) {
            $order->update(['payment_status' => Order::LUNAS]);
        } else {
            $order->update(['payment_status' => Order::DP_DITERIMA]);
        }

        return redirect()->route('pesanan-masuk.show', $order)->with('status', 'Pencatatan pembayaran berhasil diperbaiki');
    }
}
