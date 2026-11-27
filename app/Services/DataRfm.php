<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;

class DataRfm
{
    // Recency = transaksi terakhir, Frequency = jumlah pesanan berbayar, Monetary = total nilai transaksi
    public static function semuaPelanggan()
    {
        return PaymentTransaction::query()
            ->selectRaw('customer_id, MAX(paid_at) as recency, COUNT(DISTINCT order_id) as frequency, SUM(amount) as monetary')
            ->where('status', PaymentTransaction::VALID)
            ->whereRelation('order', fn ($query) => $query->whereIn('payment_status', [Order::DP_DITERIMA, Order::LUNAS]))
            ->groupBy('customer_id')
            ->get();
    }

    public static function untukPelanggan($customerId)
    {
        return self::semuaPelanggan()->firstWhere('customer_id', $customerId);
    }
}
