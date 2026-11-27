@extends('layouts.app')
@section('title', 'Proyek Saya')
@section('heading', 'Proyek Saya')

@section('content')
<p class="eyebrow">Proyek Saya</p>
<h1 class="page-title">Proyek yang kamu kerjakan.</h1>
<p class="page-sub">Urut dari tenggat terdekat. Perbarui progres supaya customer bisa memantau.</p>

<div class="mt-5 grid gap-5 md:grid-cols-2">
    @forelse ($proyek as $item)
        @php $sisa = $item->sisaHari(); $selesai = $item->status->code == 'selesai'; @endphp
        <section class="card p-5">
            <div class="flex flex-wrap items-center gap-2">
                <span class="font-mono text-sm">{{ $item->order->order_code }}</span>
                <span class="badge badge-blue">{{ $item->status->label }}</span>
                @if (! $selesai && $sisa < 0)
                    <span class="badge badge-red">Terlambat {{ abs($sisa) }} hari</span>
                @elseif (! $selesai && $sisa <= 3)
                    <span class="badge badge-amber">Mendekati tenggat</span>
                @endif
            </div>
            <h2 class="mt-1 font-display text-xl font-extrabold">{{ $item->order->title }}</h2>
            <p class="text-sm text-ink/60">{{ $item->order->service->name }} · Tenggat {{ $item->deadline->translatedFormat('d M Y') }}</p>

            <div class="mt-3 flex items-center gap-2 text-sm">
                <div class="h-2.5 flex-1 rounded-full border-2 border-ink bg-white">
                    <div class="h-full rounded-full bg-violet" style="width: {{ $item->progress_percentage }}%"></div>
                </div>
                <span class="font-bold">{{ $item->progress_percentage }}%</span>
            </div>

            <a href="{{ route('proyek-saya.show', $item) }}" class="btn-outline mt-4">Buka proyek</a>
        </section>
    @empty
        <p class="card p-8 text-center text-ink/60 md:col-span-2">Belum ada proyek yang ditugaskan ke kamu.</p>
    @endforelse
</div>
@endsection
