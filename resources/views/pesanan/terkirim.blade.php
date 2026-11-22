@extends('layouts.app')
@section('title', 'Pesanan Diajukan')
@section('heading', 'Ajukan Pesanan')

@section('content')
<div class="card relative mx-auto mt-6 max-w-lg overflow-hidden p-6 text-center sm:p-8">
    <div aria-hidden="true" class="absolute -left-8 -top-8 size-24 rounded-full border-2 border-ink bg-kuning"></div>
    <div class="relative">
        <div class="mx-auto flex size-14 items-center justify-center rounded-2xl border-2 border-ink bg-lime shadow-keras-sm">
            <x-icon name="check" class="size-7" />
        </div>
        <h1 class="mt-4 font-display text-2xl font-extrabold">Pesanan berhasil diajukan</h1>
        <p class="mt-2 text-sm text-ink/65">Simpan kode pesanan Anda. Status saat ini: {{ $order->status->customer_label }}.</p>
        <div class="mt-4 rounded-xl border-2 border-ink bg-canvas px-4 py-3 font-mono text-2xl font-semibold">{{ $order->order_code }}</div>
        <p class="mt-4 text-sm text-ink/65">{{ $order->service->name }} · {{ $order->title }}</p>
        <a href="{{ route('pesanan.show', $order) }}" class="btn-primary mt-6 w-full">Lihat Detail Pesanan</a>
    </div>
</div>
@endsection