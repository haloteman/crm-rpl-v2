@extends('layouts.app')
@section('title', 'Skema Bagi Hasil')
@section('heading', 'Skema Bagi Hasil')

@section('content')
<p class="eyebrow">Transaksi dan Pendapatan</p>
<h1 class="page-title">Skema pembagian hasil.</h1>
<p class="page-sub max-w-2xl">Persentase pendapatan bersih untuk setiap pihak. Setiap perubahan disimpan sebagai versi baru, proyek yang sudah dihitung tidak ikut berubah.</p>

<div class="mt-5 grid items-start gap-5 lg:grid-cols-2">
    <form method="POST" action="{{ route('skema.simpan') }}" class="card p-5"
          x-data="{ persen: {{ Js::from($pihak->mapWithKeys(fn ($p) => [$p->id => old('persen.'.$p->id, $skemaAktif?->items->firstWhere('party_id', $p->id)?->percent ?? 0)])) }},
                    get total() { return Object.values(this.persen).reduce((a, b) => a + Number(b), 0) } }">
        @csrf
        <h2 class="font-display text-lg font-extrabold">Atur skema baru</h2>

        <label for="label" class="mt-4 block text-sm font-medium">Nama skema</label>
        <input type="text" id="label" name="label" value="{{ old('label') }}" placeholder="Contoh: Skema 2027" class="field mt-1.5">
        @error('label') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

        <label for="effective_from" class="mt-4 block text-sm font-medium">Berlaku mulai</label>
        <input type="date" id="effective_from" name="effective_from" value="{{ old('effective_from', now()->toDateString()) }}" class="field mt-1.5">
        @error('effective_from') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

        <div class="mt-4 space-y-2">
            @foreach ($pihak as $p)
                <label class="flex items-center gap-3 text-sm">
                    <span class="flex-1 font-medium">{{ $p->label }}</span>
                    <input type="number" name="persen[{{ $p->id }}]" min="0" max="100" step="0.01" x-model="persen[{{ $p->id }}]" class="field w-28">
                    <span>%</span>
                </label>
            @endforeach
        </div>
        <p class="mt-3 text-sm font-bold" :class="total == 100 ? '' : 'text-lost'">Total: <span x-text="total"></span>%</p>
        @error('persen') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror
        @error('persen.*') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

        <button type="submit" class="btn-dark mt-4">Simpan skema</button>
    </form>

    <section class="card p-5">
        <h2 class="font-display text-lg font-extrabold">Riwayat skema</h2>
        <div class="mt-3 space-y-3">
            @forelse ($riwayat as $skema)
                <div class="rounded-xl border-2 border-ink p-3 text-sm {{ $skema->is_active ? 'bg-lime-tint' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="font-bold">{{ $skema->label }}</span>
                        @if ($skema->is_active)
                            <span class="badge badge-green">Aktif</span>
                        @endif
                        <span class="ml-auto text-xs text-ink/60">mulai {{ $skema->effective_from->translatedFormat('d M Y') }}</span>
                    </div>
                    <ul class="mt-2 grid grid-cols-2 gap-x-4 text-xs">
                        @foreach ($skema->items as $item)
                            <li class="flex justify-between"><span>{{ $item->party->label }}</span><b>{{ $item->percent }}%</b></li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <p class="text-sm text-ink/60">Belum ada skema.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
