<?php

namespace App\Http\Controllers;

use App\Models\OrderReference;
use Illuminate\Support\Facades\Storage;

class ReferensiController extends Controller
{
    // file referensi hanya bisa diunduh pemilik pesanan atau pengguna internal yang boleh lihat pesanan
    public function unduh(OrderReference $referensi)
    {
        $order = $referensi->order;
        $boleh = $order->customer_id === auth()->id() || auth()->user()->can('order.view');

        if (! $boleh || $referensi->reference_type !== OrderReference::FILE) {
            abort(404);
        }

        return Storage::download($referensi->url_or_path, $referensi->original_name);
    }
}
