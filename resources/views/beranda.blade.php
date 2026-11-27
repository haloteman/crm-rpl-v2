@extends('layouts.app')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@php
    $me = auth()->user();
    $izin = $me->getAllPermissions()->pluck('name');
@endphp

@section('content')
<div class="flex items-end justify-between gap-4">
    <div>
        <p class="eyebrow">Dashboard</p>
        <h1 class="page-title mt-1">Halo, {{ strtok($me->name, ' ') }}.</h1>
    </div>
    <span class="badge badge-green">{{ $me->getRoleNames()->first() }}</span>
</div>

@if ($me->hasRole('Customer'))
    {{-- ringkasan panel pelanggan --}}
    <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="card bg-ink p-5 text-white">
            <div class="text-sm font-bold">Total nilai pesanan</div>
            <div class="mt-2 font-display text-2xl font-extrabold text-lime">Rp{{ number_format($totalNilai, 0, ',', '.') }}</div>
            <div class="text-xs text-white/70">dari pembayaran yang sudah diterima</div>
        </div>
        <div class="card p-5">
            <div class="text-sm font-bold">Jumlah pesanan</div>
            <div class="mt-2 font-display text-3xl font-extrabold">{{ $jumlahPesanan }}</div>
        </div>
        <div class="card bg-sun p-5">
            <div class="text-sm font-bold">Pesanan aktif</div>
            <div class="mt-2 font-display text-3xl font-extrabold">{{ $pesananAktif }}</div>
            <div class="text-xs">belum selesai atau dibatalkan</div>
        </div>
        <div class="card p-5">
            <div class="text-sm font-bold">Pesanan selesai</div>
            <div class="mt-2 font-display text-3xl font-extrabold">{{ $pesananSelesai }}</div>
            @if ($layananTersering)
                <div class="text-xs text-ink/60">Paling sering: {{ $layananTersering->name }}</div>
            @endif
        </div>
    </div>

    <section class="card mt-5 overflow-x-auto">
        <h2 class="px-5 pt-5 font-display text-lg font-extrabold">Transaksi terakhir</h2>
        <table class="mt-3 w-full min-w-[520px] text-left text-sm">
            <thead class="bg-thead text-[11px] font-bold uppercase tracking-[0.1em]">
                <tr>
                    <th class="px-5 py-3">Kode</th>
                    <th class="px-5 py-3">Layanan</th>
                    <th class="px-5 py-3">Nilai</th>
                    <th class="px-5 py-3">Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksiTerakhir as $order)
                    <tr class="border-t">
                        <td class="px-5 py-3 font-mono"><a href="{{ route('pesanan.show', $order) }}" class="underline">{{ $order->order_code }}</a></td>
                        <td class="px-5 py-3">{{ $order->service->name }}</td>
                        <td class="px-5 py-3">Rp{{ number_format($order->pricing->final_price, 0, ',', '.') }}</td>
                        <td class="px-5 py-3"><span class="badge badge-green">{{ ['unpaid' => 'Belum dibayar', 'dp_received' => 'DP diterima', 'paid' => 'Lunas'][$order->payment_status] }}</span></td>
                    </tr>
                @empty
                    <tr class="border-t"><td colspan="4" class="px-5 py-8 text-center text-ink/60">Belum ada transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
@else
<div class="mt-6 grid gap-5 lg:grid-cols-[1.6fr_1fr]">
    <div class="card relative overflow-hidden p-6 sm:p-8">
        <div class="absolute -right-10 -top-10 size-36 rounded-full border-2 border-ink bg-lime"></div>
        <span class="badge bg-ink text-white">Dashboard</span>
        <h2 class="relative mt-4 text-3xl font-extrabold">Dashboard sedang disiapkan.</h2>
        <p class="relative mt-2 max-w-md text-slate-600">Ringkasan sesuai role Anda akan tampil di sini.</p>
    </div>

    <div class="card p-6">
        <h2 class="flex items-center gap-2 text-xl font-extrabold"><x-icon name="shield-check" class="size-6" /> Akses Anda</h2>
        <p class="mt-2 text-sm text-slate-600">Role {{ $me->getRoleNames()->first() }} memiliki {{ $izin->count() }} permission aktif.</p>
        <div class="mt-4 flex flex-wrap gap-2">
            @foreach ($izin as $nama)
                <span class="rounded-md border-[1.5px] border-ink px-1.5 py-0.5 font-mono text-xs">{{ $nama }}</span>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
