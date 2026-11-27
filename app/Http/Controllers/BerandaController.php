<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\Role;

class BerandaController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (! $user->hasRole(Role::CUSTOMER)) {
            return view('beranda');
        }

        $pesanan = Order::with('service', 'status', 'pricing')->where('customer_id', $user->id)->latest()->get();

        // layanan yang paling sering dipesan customer ini
        $layananTersering = $pesanan->groupBy('service_id')->sortByDesc(fn ($grup) => $grup->count())->first()?->first()->service;

        return view('beranda', [
            'totalNilai' => PaymentTransaction::where('customer_id', $user->id)->where('status', PaymentTransaction::VALID)->sum('amount'),
            'jumlahPesanan' => $pesanan->count(),
            'pesananAktif' => $pesanan->filter(fn ($order) => ! $order->status->is_final)->count(),
            'pesananSelesai' => $pesanan->where('status.code', 'selesai')->count(),
            'layananTersering' => $layananTersering,
            'transaksiTerakhir' => $pesanan->whereNotNull('pricing')->take(5),
        ]);
    }
}
