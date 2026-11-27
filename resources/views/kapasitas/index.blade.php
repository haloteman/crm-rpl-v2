@extends('layouts.app')
@section('title', 'Kapasitas Mingguan')
@section('heading', 'Kapasitas Mingguan')

@php
    $warna = ['available' => 'badge-green', 'almost_full' => 'badge-amber', 'full' => 'badge-red'];
    $teks = ['available' => 'Masih longgar', 'almost_full' => 'Hampir penuh', 'full' => 'Penuh'];
    $sekarang = $riwayat->first();
@endphp

@section('content')
<p class="eyebrow">Layanan dan Proyek</p>
<h1 class="page-title">Kapasitas layanan per minggu.</h1>
<p class="page-sub max-w-2xl">Batas jumlah pesanan aktif yang dapat dikerjakan dalam satu minggu (Senin sampai Minggu). Angka ini dipakai di daftar pesanan, di website, dan saat memutuskan menolak pesanan.</p>

<div class="mt-5 grid items-start gap-5 lg:grid-cols-2">
    <form method="POST" action="{{ route('kapasitas.simpan') }}" class="card p-5">
        @csrf
        <h2 class="font-display text-lg font-extrabold">Atur kapasitas</h2>

        <div class="mt-3 grid gap-4 sm:grid-cols-2">
            <div>
                <label for="capacity_per_week" class="block text-sm font-medium">Pesanan aktif per minggu</label>
                <input type="number" id="capacity_per_week" name="capacity_per_week" min="1" value="{{ old('capacity_per_week', $sekarang?->capacity_per_week) }}" class="field mt-1.5">
                @error('capacity_per_week') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="warning_threshold_percent" class="block text-sm font-medium">Batas "hampir penuh" (%)</label>
                <input type="number" id="warning_threshold_percent" name="warning_threshold_percent" min="1" max="100" value="{{ old('warning_threshold_percent', $sekarang?->warning_threshold_percent ?? 90) }}" class="field mt-1.5">
                @error('warning_threshold_percent') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror
            </div>
        </div>

        <label for="effective_from" class="mt-4 block text-sm font-medium">Berlaku mulai</label>
        <input type="date" id="effective_from" name="effective_from" value="{{ old('effective_from', now()->toDateString()) }}" class="field mt-1.5">
        @error('effective_from') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

        <label for="note" class="mt-4 block text-sm font-medium">Catatan <span class="font-normal text-ink/60">(opsional)</span></label>
        <input type="text" id="note" name="note" value="{{ old('note') }}" class="field mt-1.5">
        @error('note') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

        <button type="submit" class="btn-dark mt-4">Simpan</button>
    </form>

    <section class="card p-5">
        <h2 class="font-display text-lg font-extrabold">Minggu ini</h2>
        <p class="text-sm text-ink/60">{{ $mingguIni['awal_minggu']->translatedFormat('d M') }}–{{ $mingguIni['akhir_minggu']->translatedFormat('d M Y') }}</p>
        <div class="mt-3 font-display text-5xl font-extrabold">{{ $mingguIni['terisi'] }}<span class="text-lg">/{{ $mingguIni['kapasitas'] }}</span></div>
        <span class="badge {{ $warna[$mingguIni['status']] }} mt-2">{{ $teks[$mingguIni['status']] }}, sisa {{ $mingguIni['sisa'] }} slot</span>
        <p class="mt-4 rounded-xl border-2 border-ink bg-tint px-3 py-2 text-xs">
            <b>Cara menghitung terisi.</b> Pesanan dengan tenggat di minggu itu. Pesanan Ditolak dan Dibatalkan tidak dihitung.
        </p>
    </section>
</div>

<section class="card mt-5">
    <h2 class="px-5 pt-5 font-display text-lg font-extrabold">Riwayat perubahan kapasitas</h2>
    <table class="mt-3 w-full text-left text-sm">
        <thead class="bg-thead text-[11px] font-bold uppercase tracking-[0.1em]">
            <tr>
                <th class="px-5 py-3">Berlaku mulai</th>
                <th class="px-5 py-3">Kapasitas</th>
                <th class="px-5 py-3">Batas peringatan</th>
                <th class="px-5 py-3">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($riwayat as $item)
                <tr class="border-t">
                    <td class="px-5 py-3 font-mono">{{ $item->effective_from->translatedFormat('d M Y') }}</td>
                    <td class="px-5 py-3 font-bold">{{ $item->capacity_per_week }} pesanan</td>
                    <td class="px-5 py-3">{{ $item->warning_threshold_percent }}%</td>
                    <td class="px-5 py-3 text-ink/60">{{ $item->note ?? '—' }}</td>
                </tr>
            @empty
                <tr class="border-t"><td colspan="4" class="px-5 py-8 text-center text-ink/60">Kapasitas belum pernah diatur.</td></tr>
            @endforelse
        </tbody>
    </table>
</section>
@endsection
