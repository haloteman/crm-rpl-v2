<section class="card p-5">
    <h2 class="font-display text-lg font-extrabold">Riwayat status</h2>
    <ol class="mt-3 space-y-3 border-l-2 border-ink pl-4 text-sm">
        @foreach ($order->statusHistories->sortBy('changed_at') as $riwayat)
            <li class="relative">
                <span class="absolute -left-[23px] top-1 size-3 rounded-full border-2 border-ink bg-lime"></span>
                <div class="font-bold">{{ $riwayat->from_status ? $labelStatus[$riwayat->to_status] : 'Pesanan diajukan' }}</div>
                <div class="text-xs text-ink/55">{{ $riwayat->changed_at->translatedFormat('d M Y') }}</div>
            </li>
        @endforeach
    </ol>
</section>