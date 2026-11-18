@extends('layouts.app')
@section('title', 'Pesanan Saya')
@section('heading', 'Pesanan Saya')

@section('content')
<h1 class="page-title">Pesanan Saya</h1>
<p class="page-sub">Daftar pesanan yang pernah Anda ajukan beserta statusnya.</p>

<section class="card mt-5">
    <table class="block w-full text-left text-sm md:table">
        <thead class="hidden bg-thead text-xs text-slate-500 md:table-header-group">
            <tr>
                <th class="px-5 py-3 font-medium">Kode</th>
                <th class="px-5 py-3 font-medium">Layanan dan Judul</th>
                <th class="px-5 py-3 font-medium">Diajukan</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody class="block md:table-row-group">
            @forelse ($orders as $order)
                <tr class="block border-t px-4 py-3 md:table-row md:p-0">
                    <td class="block font-mono md:table-cell md:px-5 md:py-3">{{ $order->order_code }}</td>
                    <td class="block py-1 md:table-cell md:px-5 md:py-3">
                        <div class="font-medium">{{ $order->title }}</div>
                        <div class="text-xs text-slate-500">{{ $order->service->name }}</div>
                    </td>
                    <td class="block py-1 text-slate-500 md:table-cell md:px-5 md:py-3">{{ $order->created_at->translatedFormat('d M Y') }}</td>
                    <td class="block py-1 md:table-cell md:px-5 md:py-3"><span class="badge badge-blue">{{ $order->status->customer_label }}</span></td>
                    <td class="block py-1 md:table-cell md:px-5 md:py-3">
                        <a href="{{ route('pesanan.show', $order) }}" class="text-xs font-medium text-brand-dark hover:underline">Detail</a>
                    </td>
                </tr>
            @empty
                <tr class="block border-t md:table-row">
                    <td colspan="5" class="block px-5 py-8 text-center text-slate-500 md:table-cell">
                        Belum ada pesanan. <a href="{{ route('website.layanan') }}" class="font-medium text-brand-dark hover:underline">Lihat layanan</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="border-t px-5 py-4">{{ $orders->links() }}</div>
</section>
@endsection
