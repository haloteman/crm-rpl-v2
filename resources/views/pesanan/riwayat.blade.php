<section class="card p-5">
    <h2 class="font-semibold">Riwayat status</h2>
    <ol class="mt-3 space-y-3 text-sm">
        @foreach ($order->statusHistories->sortBy('changed_at') as $riwayat)
            <li>
                <div class="font-medium">{{ $riwayat->from_status ? $labelStatus[$riwayat->to_status] : 'Pesanan diajukan' }}</div>
                <div class="text-xs text-slate-500">{{ $riwayat->changed_at->translatedFormat('d M Y') }}</div>
            </li>
        @endforeach
    </ol>
</section>
