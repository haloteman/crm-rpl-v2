@extends('layouts.app')
@section('title', 'Rekap Bagi Hasil')
@section('heading', 'Rekap Bagi Hasil')

@php
    $namaBulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
@endphp

@section('content')
<p class="eyebrow">Transaksi dan Pendapatan</p>
<h1 class="page-title">Rekap pembagian hasil.</h1>
<p class="page-sub">Total bagian setiap pihak dari proyek yang dihitung pada periode ini.</p>

<form method="GET" action="{{ route('rekap.index') }}" class="mt-5 flex flex-wrap items-end gap-3">
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

<div class="mt-5 grid items-start gap-5 lg:grid-cols-2">
    <section class="card p-5">
        <h2 class="font-display text-lg font-extrabold">Per pihak</h2>
        <table class="mt-3 w-full text-sm">
            <tbody>
                @forelse ($perPihak as $baris)
                    <tr class="border-t">
                        <td class="py-2">{{ $baris['pihak']->label }}</td>
                        <td class="py-2 text-right font-bold">Rp{{ number_format($baris['total'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td class="py-4 text-ink/60">Belum ada proyek yang dihitung pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="card p-5">
        <h2 class="font-display text-lg font-extrabold">Kas operasional</h2>
        <p class="mt-1 text-sm text-ink/60">Disisihkan dari bagian perusahaan / Owner.</p>
        <dl class="mt-3 grid grid-cols-[1fr_auto] gap-y-1 text-sm">
            <dt>Kas operasional ({{ $persenKas ?? 0 }}%)</dt><dd class="font-bold">Rp{{ number_format($kasOperasional, 0, ',', '.') }}</dd>
            <dt>Sisa untuk Owner</dt><dd class="font-bold">Rp{{ number_format($sisaOwner, 0, ',', '.') }}</dd>
        </dl>
        @can('revenue.manage')
            <form method="POST" action="{{ route('rekap.kas') }}" class="mt-4 flex items-end gap-2 border-t-2 border-line pt-4">
                @csrf
                <div>
                    <label for="operating_cash_percent" class="block text-sm font-medium">Persentase kas (%)</label>
                    <input type="number" id="operating_cash_percent" name="operating_cash_percent" min="0" max="100" step="0.01" value="{{ old('operating_cash_percent', $persenKas) }}" class="field mt-1.5 w-28">
                </div>
                <button type="submit" class="btn-dark">Simpan</button>
            </form>
            @error('operating_cash_percent') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror
        @endcan
    </section>
</div>

<section class="card mt-5 p-5">
    <h2 class="font-display text-lg font-extrabold">Bagian per desainer</h2>
    <div class="mt-3 space-y-3">
        @forelse ($perDesainer as $baris)
            <details class="rounded-xl border-2 border-ink p-3 text-sm">
                <summary class="flex cursor-pointer justify-between gap-2">
                    <span class="font-bold">{{ $baris['desainer']?->name ?? 'Tanpa desainer' }} · {{ $baris['jumlah_proyek'] }} proyek</span>
                    <span class="font-bold">Rp{{ number_format($baris['total'], 0, ',', '.') }}</span>
                </summary>
                <ul class="mt-2 space-y-1 text-xs">
                    @foreach ($baris['proyek'] as $bagian)
                        <li class="flex justify-between">
                            <span><span class="font-mono">{{ $bagian->settlement->project->order->order_code }}</span> {{ $bagian->settlement->project->order->title }}</span>
                            <span>Rp{{ number_format($bagian->amount, 0, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
            </details>
        @empty
            <p class="text-sm text-ink/60">Belum ada bagian desainer pada periode ini.</p>
        @endforelse
    </div>
</section>
@endsection
