<?php

namespace App\Http\Controllers;

use App\Models\CostType;
use App\Models\PaymentTransaction;
use App\Models\Project;
use App\Models\Service;
use App\Services\RingkasanPendapatan;
use Illuminate\Http\Request;

class PendapatanController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentTransaction::with('order.service', 'order.customer')
            ->where('status', PaymentTransaction::VALID)
            ->latest('paid_at')
            ->latest('id');

        if ($request->filled('cari')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('order_code', 'like', '%'.$request->cari.'%')
                    ->orWhere('contact_name', 'like', '%'.$request->cari.'%');
            });
        }

        if ($request->filled('dari')) {
            $query->where('paid_at', '>=', $request->dari);
        }

        if ($request->filled('sampai')) {
            $query->where('paid_at', '<=', $request->sampai);
        }

        if ($request->filled('layanan')) {
            $query->whereRelation('order', 'service_id', $request->layanan);
        }

        if ($request->filled('status_pembayaran')) {
            $query->whereRelation('order', 'payment_status', $request->status_pembayaran);
        }

        if ($request->filled('jenis')) {
            $query->where('payment_type', $request->jenis);
        }

        // ringkasan per bulan, atau per tahun kalau bulan tidak dipilih
        $tahun = $request->input('tahun', now()->year);
        $bulan = $request->input('bulan');

        return view('pendapatan.index', [
            'ringkasan' => $bulan ? RingkasanPendapatan::bulan($tahun, $bulan) : RingkasanPendapatan::tahun($tahun),
            'perBulan' => RingkasanPendapatan::perBulan($tahun),
            'tahun' => $tahun,
            'bulan' => $bulan,
            'transaksi' => (clone $query)->paginate(10)->withQueryString(),
            'totalFilter' => (clone $query)->sum('amount'),
            'semuaLayanan' => Service::orderBy('sort_order')->get(),
        ]);
    }

    public function proyek(Project $project)
    {
        $project->load('order.service', 'order.customer', 'order.pricing', 'order.payments', 'designer', 'costs.costType', 'settlement.shares.party', 'settlement.shares.recipient');

        $pendapatanKotor = $project->order->totalDibayar();
        $totalBiaya = $project->costs->sum('amount');

        return view('pendapatan.proyek', [
            'project' => $project,
            'pendapatanKotor' => $pendapatanKotor,
            'totalBiaya' => $totalBiaya,
            'pendapatanBersih' => $pendapatanKotor - $totalBiaya,
            'jenisBiaya' => CostType::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }
}
