@extends('layouts.app')
@section('title', 'Tracking '.$order->order_code)
@section('heading', 'Tracking Proyek')

@section('content')
<a href="{{ route('pesanan.show', $order) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-dark underline underline-offset-4 hover:text-ink">
    <x-icon name="arrow-left" class="size-3.5" /> Kembali ke detail pesanan
</a>

<p class="eyebrow mt-3">Tracking Proyek</p>
<h1 class="page-title">Posisi <span class="mark">pengerjaan.</span></h1>
<p class="page-sub max-w-xl">Pantau tahap demi tahap. Pembaruan dari desainer tampil di sini begitu dikirim.</p>

@if (! $order->project)
    <p class="card mt-5 p-8 text-center text-ink/60">Proyek dibuat setelah DP diterima. Status pesanan sekarang: {{ $order->status->customer_label }}.</p>
@else
    @php $proyek = $order->project; $terbaru = $proyek->progressUpdates->first(); @endphp

    <section class="card mt-5 p-5">
        <div class="flex flex-wrap items-start justify-between gap-2">
            <div>
                <div class="font-mono text-sm">{{ $order->order_code }}</div>
                <h2 class="font-display text-2xl font-extrabold">{{ $order->title }}</h2>
                <div class="text-sm text-ink/60">{{ $order->service->name }}</div>
            </div>
            <span class="badge badge-blue">{{ $order->status->customer_label }}</span>
        </div>
        <div class="mt-4 flex justify-between text-sm font-bold">
            <span>Progres</span><span>{{ $proyek->progress_percentage }}%</span>
        </div>
        <div class="mt-1 h-3 rounded-full border-2 border-ink bg-white">
            <div class="h-full rounded-full bg-violet" style="width: {{ $proyek->progress_percentage }}%"></div>
        </div>
        <p class="mt-3 text-sm">
            Tenggat <b>{{ $proyek->deadline->translatedFormat('d M Y') }}</b>
            @if ($terbaru)
                · Pembaruan terbaru <b>{{ $terbaru->created_at->translatedFormat('d M Y') }}, {{ $terbaru->stage }}</b>
            @endif
        </p>
        @if ($proyek->chat)
            <a href="{{ route('chat.show', $proyek->chat) }}" class="btn-outline mt-4">Chat dengan Desainer</a>
        @endif
    </section>

    @if ($hasilAkhir->isNotEmpty())
        <section class="card mt-5 bg-lime-tint p-5">
            <h2 class="font-display text-lg font-extrabold">Hasil akhir</h2>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($hasilAkhir as $lampiran)
                    @if ($lampiran->attachment_type == 'file')
                        <a href="{{ route('lampiran.unduh', $lampiran) }}" class="btn-primary">Unduh {{ $lampiran->original_name }}</a>
                    @else
                        <a href="{{ $lampiran->url_or_path }}" target="_blank" rel="noopener" class="btn-primary">Buka tautan hasil</a>
                    @endif
                @endforeach
            </div>
        </section>
    @endif

    <div class="mt-5 grid items-start gap-5 lg:grid-cols-[1fr_1.4fr]">
        <section class="card p-5">
            <h2 class="font-display text-lg font-extrabold">Tahap proyek</h2>
            <ol class="mt-3 space-y-3 border-l-2 border-ink pl-4 text-sm">
                @foreach ($timeline as $kejadian)
                    @php $belum = $kejadian['jenis'] == 'deadline' && $kejadian['waktu']->isFuture(); @endphp
                    <li class="relative">
                        <span class="absolute -left-[23px] top-1 size-3 rounded-full border-2 border-ink {{ $belum ? 'bg-white' : 'bg-lime' }}"></span>
                        <div class="font-bold">{{ $kejadian['judul'] }}</div>
                        <div class="text-xs text-ink/60">{{ $kejadian['waktu']->translatedFormat('d M Y') }}</div>
                    </li>
                @endforeach
            </ol>
        </section>

        <section class="card p-5">
            <h2 class="font-display text-lg font-extrabold">Riwayat pembaruan</h2>
            <div class="mt-3 space-y-3">
                @forelse ($proyek->progressUpdates as $pembaruan)
                    <div class="flex gap-4 rounded-xl border-2 border-ink p-3">
                        <div class="w-16 shrink-0">
                            <div class="font-display text-2xl font-extrabold">{{ $pembaruan->percentage }}%</div>
                            <div class="text-xs text-ink/60">{{ $pembaruan->created_at->translatedFormat('d M') }}</div>
                        </div>
                        <div class="min-w-0 text-sm">
                            <div class="font-bold">{{ $pembaruan->stage }}</div>
                            @if ($pembaruan->note)
                                <p class="text-ink/70">{{ $pembaruan->note }}</p>
                            @endif
                            <div class="mt-1 flex flex-wrap gap-2">
                                @foreach ($pembaruan->attachments as $lampiran)
                                    @if ($lampiran->attachment_type == 'file')
                                        <a href="{{ route('lampiran.unduh', $lampiran) }}" class="badge badge-gray">{{ $lampiran->original_name }}</a>
                                    @else
                                        <a href="{{ $lampiran->url_or_path }}" target="_blank" rel="noopener" class="badge badge-gray">Tautan pratinjau</a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-ink/60">Belum ada pembaruan dari desainer.</p>
                @endforelse
            </div>
        </section>
    </div>
@endif
@endsection
