<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\StatusDefinition;
use Illuminate\Http\Request;

class PesananMasukController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('customer.customerProfile', 'service', 'status')->latest();

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
        ]);
    }

    public function show(Order $order)
    {
        $order->load('customer.customerProfile', 'service', 'status', 'references', 'statusHistories');

        return view('pesanan-masuk.show', [
            'order' => $order,
            'semuaStatus' => StatusDefinition::where('scope', 'order')->where('code', '!=', 'dibatalkan')->orderBy('sort_order')->get(),
            'labelStatus' => StatusDefinition::where('scope', 'order')->pluck('label', 'code'),
        ]);
    }
}
