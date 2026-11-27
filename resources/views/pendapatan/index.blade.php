@extends('layouts.app')
@section('title', 'Riwayat Pendapatan')
@section('heading', 'Riwayat Pendapatan')

@php
    $namaBulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
@endphp

@section('content')
<p class="eyebrow">Transaksi dan Pendapatan</p>
<h1 class="page-title">Riwayat pendapatan.</h1>
<p class="page-sub max-w-2xl">Kotor = pembayaran yang masuk. Bersih = kotor dikurangi biaya langsung proyek. Pesanan dibatalkan atau ditolak tidak dihitung.</p>

{{-- ringkasan periode --}}
<form method="GET" action="{{ route('pendapatan.index') }}" class="mt-5 flex flex-wrap items-end gap-3">
    <div>
        <label for="tahun" class="block text-sm font-medium">Tahun</label>
        <input type="number" id="tahun" name="tahun" value="{{ $tahun }}" class="field mt-1.5 w-28">
    </div>
    <div>
        <label for="bulan" class="block text-sm font-medium">Bulan</label>
        <select id="bulan" name="bulan" class="field mt-1.5">
            <option value="">Setahun penuh</option>
            @foreach ($namaBulan as $angka => $nama)
                <option value="{{ $angka }}" @selected($bulan == $angka)>{{ $nama }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn-outline">Tampilkan</button>
</form>

<div class="mt-4 grid gap-4 sm:grid-cols-3">
    @foreach (['kotor' => 'Pendapatan kotor', 'biaya' => 'Biaya langsung', 'bersih' => 'Pendapatan bersih'] as $kunci => $judul)
        <div class="card p-5 {{ $kunci == 'bersih' ? 'bg-lime' : '' }}">
            <div class="text-sm font-bold">{{ $judul }}</div>
            <div class="text-xs text-ink/60">{{ $bulan ? $namaBulan[$bulan].' ' : '' }}{{ $tahun }}</div>
            <div class="mt-2 font-display text-2xl font-extrabold">Rp{{ number_format($ringkasan[$kunci], 0, ',', '.') }}</div>
        </div>
    @endforeach
</div>

<section class="card mt-5 overflow-x-auto">
    <h2 class="px-5 pt-5 font-display text-lg font-extrabold">Kotor dan bersih per bulan, {{ $tahun }}</h2>
    <table class="mt-3 w-full min-w-[560px] text-left text-sm">
        <thead class="bg-thead text-[11px] font-bold uppercase tracking-[0.1em]">
            <tr>
                <th class="px-5 py-3">Bulan</th>
                <th class="px-5 py-3 text-right">Kotor</th>
                <th class="px-5 py-3 text-right">Biaya</th>
                <th class="px-5 py-3 text-right">Bersih</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($perBulan as $angka => $data)
                <tr class="border-t">
                    <td class="px-5 py-2">{{ $namaBulan[$angka] }}</td>
                    <td class="px-5 py-2 text-right">Rp{{ number_format($data['kotor'], 0, ',', '.') }}</td>
                    <td class="px-5 py-2 text-right">Rp{{ number_format($data['biaya'], 0, ',', '.') }}</td>
                    <td class="px-5 py-2 text-right font-bold">Rp{{ number_format($data['bersih'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</section>

{{-- daftar transaksi --}}
<h2 class="mt-8 font-display text-xl font-extrabold">Transaksi pembayaran</h2>
<form method="GET" action="{{ route('pendapatan.index') }}" class="mt-3 grid gap-3 sm:grid-cols-3 lg:grid-cols-6">
    <input type="hidden" name="tahun" value="{{ $tahun }}">
    <input type="hidden" name="bulan" value="{{ $bulan }}">
    <input type="search" name="cari" value="{{ request('cari') }}" placeholder="Kode atau customer" class="field">
    <input type="date" name="dari" value="{{ request('dari') }}" class="field" aria-label="Dari tanggal">
    <input type="date" name="sampai" value="{{ request('sampai') }}" class="field" aria-label="Sampai tanggal">
    <select name="layanan" class="field" aria-label="Layanan">
        <option value="">Semua layanan</option>
        @foreach ($semuaLayanan as $layanan)
            <option value="{{ $layanan->id }}" @selected(request('layanan') == $layanan->id)>{{ $layanan->name }}</option>
        @endforeach
    </select>
    <select name="status_pembayaran" class="field" aria-label="Status pembayaran">
        <option value="">Semua status</option>
        <option value="dp_received" @selected(request('status_pembayaran') == 'dp_received')>DP diterima</option>
        <option value="paid" @selected(request('status_pembayaran') == 'paid')>Lunas</option>
    </select>
    <select name="jenis" class="field" aria-label="Jenis pembayaran">
        <option value="">DP dan pelunasan</option>
        <option value="dp" @selected(request('jenis') == 'dp')>DP</option>
        <option value="settlement" @selected(request('jenis') == 'settlement')>Pelunasan</option>
    </select>
    <button type="submit" class="btn-outline sm:col-span-3 lg:col-span-6 lg:justify-self-start">Terapkan filter</button>
</form>

<section class="card mt-4 overflow-x-auto">
    <table class="w-full min-w-[720px] text-left text-sm">
        <thead class="bg-thead text-[11px] font-bold uppercase tracking-[0.1em]">
            <tr>
                <th class="px-5 py-3">Tanggal</th>
                <th class="px-5 py-3">Pesanan</th>
                <th class="px-5 py-3">Customer</th>
                <th class="px-5 py-3">Jenis</th>
                <th class="px-5 py-3 text-right">Nominal</th>
                <th class="px-5 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaksi as $item)
                <tr class="border-t">
                    <td class="px-5 py-3">{{ $item->paid_at->translatedFormat('d M Y') }}</td>
                    <td class="px-5 py-3">
                        <div class="font-mono text-xs">{{ $item->order->order_code }}</div>
                        <div class="font-bold">{{ $item->order->title }}</div>
                        <div class="text-xs text-ink/60">{{ $item->order->service->name }}</div>
                    </td>
                    <td class="px-5 py-3">{{ $item->order->contact_name }}</td>
                    <td class="px-5 py-3"><span class="badge badge-gray">{{ $item->payment_type == 'dp' ? 'DP' : 'Pelunasan' }}</span> {{ $item->method == 'qris' ? 'QRIS' : 'Transfer' }}</td>
                    <td class="px-5 py-3 text-right font-bold">Rp{{ number_format($item->amount, 0, ',', '.') }}</td>
                    <td class="px-5 py-3">
                        @if ($item->order->project)
                            <a href="{{ route('pendapatan.proyek', $item->order->project) }}" class="btn-outline py-1.5">Detail proyek</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr class="border-t"><td colspan="6" class="px-5 py-8 text-center text-ink/60">Tidak ada transaksi yang cocok.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="flex flex-col gap-3 border-t px-5 py-4 text-xs text-ink/60 sm:flex-row sm:items-center sm:justify-between">
        <span>Total sesuai filter: <b class="text-ink">Rp{{ number_format($totalFilter, 0, ',', '.') }}</b></span>
        <div>{{ $transaksi->links() }}</div>
    </div>
</section>
@endsection
