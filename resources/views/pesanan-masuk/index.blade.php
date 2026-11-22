@extends('layouts.app')
@section('title', 'Pesanan')
@section('heading', 'Pesanan')

@section('content')
<h1 class="page-title mt-1">Pesanan masuk.</h1>
<p class="page-sub">Pantau pesanan yang masuk dan tindak lanjuti.</p>

<form method="GET" action="{{ route('pesanan-masuk.index') }}" class="mt-5 flex flex-col gap-3 sm:flex-row">
    @if (request('status'))
        <input type="hidden" name="status" value="{{ request('status') }}">
    @endif
    <div class="relative flex-1 sm:max-w-sm">
        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 size-5 -translate-y-1/2 text-slate-400" />
        <input type="search" name="cari" value="{{ request('cari') }}" placeholder="Cari kode, customer, atau judul" class="field pl-10">
    </div>
    <button type="submit" class="btn-outline">Cari</button>
</form>

<div class="mt-4 flex flex-wrap gap-2 text-xs">
    <a href="{{ route('pesanan-masuk.index', ['cari' => request('cari')]) }}"
       class="rounded-full border px-3 py-1.5 {{ request('status') ? 'bg-white' : 'bg-ink text-white' }}">Semua {{ $totalPesanan }}</a>
    @foreach ($semuaStatus as $status)
        <a href="{{ route('pesanan-masuk.index', ['status' => $status->code, 'cari' => request('cari')]) }}"
           class="rounded-full border px-3 py-1.5 {{ request('status') === $status->code ? 'bg-ink text-white' : 'bg-white' }}">{{ $status->label }} {{ $status->orders_count }}</a>
    @endforeach
</div>

<section class="card mt-4">
    <table class="block w-full text-left text-sm md:table">
        <thead class="hidden bg-thead text-[11px] font-bold uppercase tracking-[0.1em] md:table-header-group">
            <tr>
                <th class="px-5 py-3 font-medium">Kode</th>
                <th class="px-5 py-3 font-medium">Customer</th>
                <th class="px-5 py-3 font-medium">Layanan dan Judul</th>
                <th class="px-5 py-3 font-medium">Tenggat</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody class="block md:table-row-group">
            @forelse ($orders as $order)
                <tr class="block border-t px-4 py-3 md:table-row md:p-0">
                    <td class="block font-mono md:table-cell md:px-5 md:py-3">{{ $order->order_code }}</td>
                    <td class="block py-1 md:table-cell md:px-5 md:py-3">
                        <div class="font-medium">{{ $order->contact_name }}</div>
                        <div class="text-xs text-slate-500">
                            {{ $order->customer->customerProfile->institution ?? 'Perorangan' }} · {{ ucfirst($order->customer->customerProfile->status) }}
                        </div>
                    </td>
                    <td class="block py-1 md:table-cell md:px-5 md:py-3">
                        <div class="font-medium">{{ $order->title }}</div>
                        <div class="text-xs text-slate-500">{{ $order->service->name }}</div>
                    </td>
                    <td class="block py-1 text-slate-500 md:table-cell md:px-5 md:py-3">{{ $order->expected_deadline->translatedFormat('d M') }}</td>
                    <td class="block py-1 md:table-cell md:px-5 md:py-3"><span class="badge badge-blue">{{ $order->status->label }}</span></td>
                    <td class="block py-1 md:table-cell md:px-5 md:py-3">
                        <a href="{{ route('pesanan-masuk.show', $order) }}" class="btn-outline py-1.5">Detail</a>
                    </td>
                </tr>
            @empty
                <tr class="block border-t md:table-row">
                    <td colspan="6" class="block px-5 py-8 text-center text-slate-500 md:table-cell">
                        Tidak ada pesanan yang cocok.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="flex flex-col gap-3 border-t px-5 py-4 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
        <span>Menampilkan {{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }} pesanan</span>
        <div>{{ $orders->links() }}</div>
    </div>
</section>
@endsection
