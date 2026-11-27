@extends('layouts.app')
@section('title', 'Pendapatan '.$project->order->order_code)
@section('heading', 'Riwayat Pendapatan')

@php $order = $project->order; @endphp

@section('content')
<div x-data="{ modal: '' }">

<p class="text-sm">
    <a href="{{ route('pendapatan.index') }}" class="font-bold text-violet underline underline-offset-4">Riwayat Pendapatan</a>
    / <span class="font-mono">{{ $order->order_code }}</span>
</p>

<p class="eyebrow mt-4">Pendapatan per proyek</p>
<h1 class="page-title">{{ $order->title }}</h1>
<p class="page-sub">{{ $order->service->name }} · {{ $order->contact_name }} · Desainer {{ $project->designer?->name ?? '—' }}</p>

<div class="mt-5 grid gap-4 sm:grid-cols-4">
    <div class="card p-4">
        <div class="text-xs font-bold">Harga akhir</div>
        <div class="mt-1 font-display text-xl font-extrabold">Rp{{ number_format($order->pricing?->final_price ?? 0, 0, ',', '.') }}</div>
    </div>
    <div class="card p-4">
        <div class="text-xs font-bold">Pendapatan kotor</div>
        <div class="mt-1 font-display text-xl font-extrabold">Rp{{ number_format($pendapatanKotor, 0, ',', '.') }}</div>
    </div>
    <div class="card p-4">
        <div class="text-xs font-bold">Biaya langsung</div>
        <div class="mt-1 font-display text-xl font-extrabold">Rp{{ number_format($totalBiaya, 0, ',', '.') }}</div>
    </div>
    <div class="card bg-lime p-4">
        <div class="text-xs font-bold">Pendapatan bersih</div>
        <div class="mt-1 font-display text-xl font-extrabold">Rp{{ number_format($pendapatanBersih, 0, ',', '.') }}</div>
    </div>
</div>

<div class="mt-5 grid items-start gap-5 lg:grid-cols-2">
    <section class="card p-5">
        <h2 class="font-display text-lg font-extrabold">Pembayaran</h2>
        <ul class="mt-3 space-y-2 text-sm">
            @forelse ($order->payments->sortBy('paid_at') as $transaksi)
                <li class="flex justify-between gap-2 rounded-xl border-2 border-ink px-3 py-2 {{ $transaksi->status == 'cancelled' ? 'bg-paper text-ink/50 line-through' : '' }}">
                    <span>{{ $transaksi->payment_type == 'dp' ? 'DP' : 'Pelunasan' }} · {{ $transaksi->paid_at->translatedFormat('d M Y') }}</span>
                    <span class="font-bold">Rp{{ number_format($transaksi->amount, 0, ',', '.') }}</span>
                </li>
            @empty
                <li class="text-ink/60">Belum ada pembayaran.</li>
            @endforelse
        </ul>
        <a href="{{ route('pesanan-masuk.show', $order) }}" class="btn-outline mt-3">Buka pesanan</a>
    </section>

    <section class="card p-5">
        <h2 class="font-display text-lg font-extrabold">Biaya langsung</h2>
        <ul class="mt-3 space-y-2 text-sm">
            @forelse ($project->costs as $biaya)
                <li class="rounded-xl border-2 border-ink px-3 py-2">
                    <div class="flex justify-between gap-2">
                        <span class="font-bold">{{ $biaya->costType->label }}</span>
                        <span class="font-bold">Rp{{ number_format($biaya->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-xs text-ink/60">{{ $biaya->incurred_at->translatedFormat('d M Y') }}{{ $biaya->note ? ' · '.$biaya->note : '' }}</div>
                    @can('revenue.manage')
                        <div class="mt-1 flex gap-3 text-xs font-bold">
                            <button type="button" @click="modal = 'biaya-{{ $biaya->id }}'" class="text-violet underline">Ubah</button>
                            <form method="POST" action="{{ route('biaya.hapus', $biaya) }}" onsubmit="return confirm('Hapus biaya ini?')">
                                @csrf
                                <button type="submit" class="text-lost underline">Hapus</button>
                            </form>
                        </div>
                    @endcan
                </li>
            @empty
                <li class="text-ink/60">Belum ada biaya langsung.</li>
            @endforelse
        </ul>
        @can('revenue.manage')
            <button type="button" @click="modal = 'biaya-baru'" class="btn-primary mt-3">Tambah biaya</button>
        @endcan
        @if ($errors->any())
            <p class="mt-2 text-sm text-lost">{{ $errors->first() }}</p>
        @endif
    </section>
</div>

<section class="card mt-5 p-5">
    <h2 class="font-display text-lg font-extrabold">Pembagian hasil</h2>
    @if ($project->settlement)
        <p class="text-xs text-ink/60">Dihitung {{ $project->settlement->calculated_at->translatedFormat('d M Y') }} dari pendapatan bersih Rp{{ number_format($project->settlement->net_revenue, 0, ',', '.') }}</p>
        <table class="mt-3 w-full text-left text-sm">
            <tbody>
                @foreach ($project->settlement->shares as $bagian)
                    <tr class="border-t">
                        <td class="py-2">{{ $bagian->party->label }}{{ $bagian->recipient ? ' ('.$bagian->recipient->name.')' : '' }}</td>
                        <td class="py-2">{{ $bagian->percent }}%</td>
                        <td class="py-2 text-right font-bold">Rp{{ number_format($bagian->amount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="mt-2 text-sm text-ink/60">Dihitung otomatis setelah pesanan lunas dan proyek selesai.</p>
    @endif
</section>

@can('revenue.manage')
    {{-- popup tambah dan ubah biaya --}}
    @foreach (collect([null])->merge($project->costs) as $biaya)
        <div x-show="modal === '{{ $biaya ? 'biaya-'.$biaya->id : 'biaya-baru' }}'" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-ink/50 p-4" role="dialog" aria-modal="true">
            <div class="flex min-h-full items-center justify-center">
                <form method="POST" action="{{ $biaya ? route('biaya.ubah', $biaya) : route('biaya.simpan', $project) }}" class="card w-full max-w-md p-5 sm:p-6" @click.outside="modal = ''">
                    @csrf
                    <h2 class="font-display text-xl font-extrabold">{{ $biaya ? 'Ubah' : 'Tambah' }} biaya langsung</h2>

                    <label class="mt-4 block text-sm font-medium">Jenis biaya
                        <select name="cost_type_id" class="field mt-1.5">
                            @foreach ($jenisBiaya as $jenis)
                                <option value="{{ $jenis->id }}" @selected($biaya?->cost_type_id == $jenis->id)>{{ $jenis->label }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="mt-4 block text-sm font-medium">Nominal (Rp)
                        <input type="number" name="amount" min="1" value="{{ $biaya?->amount }}" class="field mt-1.5">
                    </label>

                    <label class="mt-4 block text-sm font-medium">Tanggal
                        <input type="date" name="incurred_at" value="{{ $biaya?->incurred_at->toDateString() ?? now()->toDateString() }}" class="field mt-1.5">
                    </label>

                    <label class="mt-4 block text-sm font-medium">Catatan <span class="font-normal text-ink/60">(opsional)</span>
                        <input type="text" name="note" value="{{ $biaya?->note }}" class="field mt-1.5">
                    </label>

                    <div class="mt-6 flex justify-end gap-3 border-t-2 border-line pt-4">
                        <button type="button" @click="modal = ''" class="btn-outline">Batal</button>
                        <button type="submit" class="btn-dark">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endcan

</div>
@endsection
