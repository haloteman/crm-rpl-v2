@extends('layouts.app')
@section('title', $order->order_code)
@section('heading', 'Pesanan Saya')

@section('content')
<a href="{{ route('pesanan.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-dark underline underline-offset-4 hover:text-ink">
    <x-icon name="arrow-left" class="size-3.5" /> Kembali ke pesanan saya
</a>

<p class="eyebrow mt-3">Detail Pesanan</p>

<h1 class="page-title mt-1">{{ $order->title }}</h1>
<p class="page-sub">
    <span class="font-mono">{{ $order->order_code }}</span> ·
    <span class="badge badge-blue">{{ $order->status->customer_label }}</span> ·
    Diajukan {{ $order->created_at->translatedFormat('d M Y') }}
</p>

@if ($order->closure)
    <section class="card mt-5 bg-lost-tint p-5">
        <h2 class="font-display text-lg font-extrabold">Pesanan {{ strtolower($order->status->customer_label) }}</h2>
        <p class="mt-1 text-sm"><b>{{ $order->closure->reason->label }}.</b> {{ $order->closure->reason_note }}</p>
    </section>
@endif

<div class="mt-5 grid items-start gap-5 lg:grid-cols-[1fr_320px]">
    <section class="card p-5">
        <h2 class="font-semibold">Kebutuhan</h2>
        <dl class="mt-3 grid grid-cols-[140px_1fr] gap-y-2 text-sm">
            <dt class="text-slate-500">Layanan</dt><dd>{{ $order->service->name }}</dd>
            <dt class="text-slate-500">Tenggat diharapkan</dt><dd>{{ $order->expected_deadline->translatedFormat('d M Y') }}</dd>
            <dt class="text-slate-500">Perkiraan anggaran</dt>
            <dd>{{ $order->budget_estimate ? 'Rp'.number_format($order->budget_estimate, 0, ',', '.').' (perkiraan, bukan harga final)' : '—' }}</dd>
        </dl>
        <p class="mt-3 whitespace-pre-line text-sm">{{ $order->description }}</p>
        @include('pesanan.referensi')
    </section>

    <div class="space-y-5">
        @if (! $order->status->is_final)
            <section class="card p-5">
                <h2 class="font-semibold">Konsultasi</h2>
                <p class="mt-1 text-sm text-slate-500">Bahas kebutuhan dan harga dengan Owner lewat WhatsApp.</p>
                <a href="{{ \App\Services\WaLinkBuilder::buildOwnerConsultation($order) }}" target="_blank" rel="noopener" class="btn-primary mt-4 w-full">Lanjut Konsultasi via WhatsApp</a>
            </section>
        @endif

        @if ($order->pricing)
            <section class="card p-5">
                <h2 class="font-display text-lg font-extrabold">Tagihan</h2>
                <dl class="mt-3 grid grid-cols-[110px_1fr] gap-y-1 text-sm">
                    <dt class="text-ink/60">Harga akhir</dt><dd class="font-bold">Rp{{ number_format($order->pricing->final_price, 0, ',', '.') }}</dd>
                    <dt class="text-ink/60">DP</dt><dd class="font-bold">Rp{{ number_format($order->pricing->dp_amount, 0, ',', '.') }}</dd>
                    <dt class="text-ink/60">Sudah dibayar</dt><dd class="font-bold">Rp{{ number_format($order->totalDibayar(), 0, ',', '.') }}</dd>
                    <dt class="text-ink/60">Sisa tagihan</dt><dd class="font-bold">Rp{{ number_format($order->sisaTagihan(), 0, ',', '.') }}</dd>
                </dl>
                @if ($order->status->code == 'menunggu_dp')
                    <p class="mt-3 rounded-xl border-2 border-ink bg-warn-tint px-3 py-2 text-xs">Bayar DP lewat transfer atau QRIS sesuai arahan Owner di WhatsApp. Pengerjaan dimulai setelah DP dicatat.</p>
                @endif
                @if ($order->payments->isNotEmpty())
                    <ul class="mt-3 space-y-1 border-t-2 border-line pt-3 text-xs">
                        @foreach ($order->payments as $transaksi)
                            <li class="flex justify-between">
                                <span>{{ $transaksi->payment_type == 'dp' ? 'DP' : 'Pelunasan' }} · {{ $transaksi->paid_at->translatedFormat('d M Y') }}</span>
                                <span class="font-bold">Rp{{ number_format($transaksi->amount, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endif

        @if ($order->project)
            <section class="card p-5">
                <h2 class="font-display text-lg font-extrabold">Proyek</h2>
                <p class="mt-1 text-sm text-ink/60">Pantau progres pengerjaan dan ngobrol dengan desainer.</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    <a href="{{ route('pesanan.tracking', $order) }}" class="btn-primary">Tracking Proyek</a>
                    @if ($order->project->chat)
                        <a href="{{ route('chat.show', $order->project->chat) }}" class="btn-outline">Chat Desainer</a>
                    @endif
                </div>
            </section>

            @if ($order->status->code == 'selesai')
                <section class="card p-5">
                    <h2 class="font-display text-lg font-extrabold">Rating dan ulasan</h2>
                    @if ($order->project->rating)
                        <div class="mt-2 text-xl text-sun">{{ str_repeat('★', $order->project->rating->rating) }}<span class="text-ink/20">{{ str_repeat('★', 5 - $order->project->rating->rating) }}</span></div>
                        <p class="mt-1 text-sm">{{ $order->project->rating->review }}</p>
                    @else
                        <form method="POST" action="{{ route('pesanan.rating', $order) }}" class="mt-2">
                            @csrf
                            <div x-data="{ nilai: {{ old('rating', 0) }} }" class="flex gap-1 text-3xl">
                                @for ($bintang = 1; $bintang <= 5; $bintang++)
                                    <label class="cursor-pointer" :class="nilai >= {{ $bintang }} ? 'text-sun' : 'text-ink/20'" title="{{ $bintang }} bintang">
                                        <input type="radio" name="rating" value="{{ $bintang }}" x-model.number="nilai" class="sr-only">★
                                    </label>
                                @endfor
                            </div>
                            @error('rating') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

                            <label for="review" class="mt-3 block text-sm font-medium">Ulasan <span class="font-normal text-ink/60">(opsional)</span></label>
                            <textarea id="review" name="review" rows="3" class="field mt-1.5">{{ old('review') }}</textarea>
                            @error('review') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

                            <button type="submit" class="btn-primary mt-3 w-full">Kirim Rating</button>
                        </form>
                    @endif
                </section>
            @endif
        @endif

        @if ($order->status->is_final)
            <a href="{{ route('pesanan.ulang', $order) }}" class="btn-outline w-full">Pesan Lagi</a>
        @endif

        @include('pesanan.riwayat')
    </div>
</div>
@endsection
