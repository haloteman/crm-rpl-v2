@extends('layouts.app')
@section('title', 'Pesanan Diajukan')
@section('heading', 'Ajukan Pesanan')

@section('content')
<div class="card mx-auto mt-6 max-w-lg p-6 text-center sm:p-8">
    <div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-won-tint text-won">
        <x-icon name="check" class="size-7" />
    </div>
    <h1 class="mt-4 text-xl font-semibold">Pesanan berhasil diajukan</h1>
    <p class="mt-2 text-sm text-slate-500">Simpan kode pesanan Anda. Status saat ini: {{ $order->status->customer_label }}.</p>
    <div class="mt-4 rounded-lg border bg-thead px-4 py-3 font-mono text-2xl font-semibold">{{ $order->order_code }}</div>
    <p class="mt-4 text-sm text-slate-500">{{ $order->service->name }} · {{ $order->title }}</p>
    <a href="{{ route('pesanan.show', $order) }}" class="btn-primary mt-6 w-full">Lihat Detail Pesanan</a>
</div>
@endsection
