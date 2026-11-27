<?php

namespace App\Http\Controllers;

use App\Models\ClosureReason;
use App\Models\Order;
use App\Models\OrderClosure;
use App\Models\StatusDefinition;
use App\Services\KapasitasMingguan;
use Illuminate\Http\Request;

class PesananMasukController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('customer.customerProfile', 'service', 'status')->latest();

        // sisa pelunasan hanya untuk Owner
        if (auth()->user()->can('price.set')) {
            $query->with('pricing', 'payments');
        }

        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_code', 'like', '%'.$request->cari.'%')
                    ->orWhere('title', 'like', '%'.$request->cari.'%')
                    ->orWhere('contact_name', 'like', '%'.$request->cari.'%');
            });
        }

        if ($request->filled('status')) {
            $query->whereRelation('status', 'code', $request->status);
        }

        return view('pesanan-masuk.index', [
            'orders' => $query->paginate(10)->withQueryString(),
            'semuaStatus' => StatusDefinition::where('scope', 'order')->withCount('orders')->orderBy('sort_order')->get(),
            'totalPesanan' => Order::count(),
            'kapasitas' => KapasitasMingguan::ringkasan(now()),
        ]);
    }

    public function show(Order $order)
    {
        $order->load('customer.customerProfile', 'createdBy', 'service', 'status', 'references', 'statusHistories', 'consultation', 'project', 'closure.reason');

        // nominal harga hanya untuk Owner, Admin tidak boleh menerima datanya
        if (auth()->user()->can('price.set')) {
            $order->load('pricing', 'payments.proofs', 'pricingHistories.changedBy');
        }

        return view('pesanan-masuk.show', [
            'order' => $order,
            'semuaStatus' => StatusDefinition::where('scope', 'order')->where('code', '!=', 'dibatalkan')->orderBy('sort_order')->get(),
            'labelStatus' => StatusDefinition::where('scope', 'order')->pluck('label', 'code'),
            'kapasitas' => KapasitasMingguan::ringkasan($order->expected_deadline),
            'alasanBatal' => ClosureReason::where('closure_type', OrderClosure::BATAL)->where('is_active', true)->orderBy('sort_order')->get(),
            'alasanTolak' => ClosureReason::where('closure_type', OrderClosure::TOLAK)->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }
}
