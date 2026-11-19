@extends('layouts.app')
@section('title', $order->order_code)
@section('heading', 'Pesanan Saya')

@section('content')
<a href="{{ route('pesanan.index') }}" class="inline-flex items-center gap-1.5 text-sm font-bold underline underline-offset-4">
    <x-icon name="arrow-left" class="size-3.5" /> Kembali ke pesanan saya
</a>

<p class="eyebrow mt-3">Detail Pesanan</p>

<h1 class="page-title mt-1">{{ $order->title }}</h1>
<p class="page-sub">
    <span class="font-mono">{{ $order->order_code }}</span> ·
    <span class="badge badge-blue">{{ $order->status->customer_label }}</span> ·
    Diajukan {{ $order->created_at->translatedFormat('d M Y') }}
</p>

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

        @include('pesanan.riwayat')
    </div>
</div>
@endsection
