@extends('layouts.app')
@section('title', 'Semua Proyek')
@section('heading', 'Semua Proyek')

@section('content')
<p class="eyebrow">Semua Proyek</p>
<h1 class="page-title">Progres seluruh proyek.</h1>
<p class="page-sub max-w-2xl">Pantau status, progres, dan tenggat seluruh proyek. Penanda kuning berarti mendekati tenggat, koral berarti terlambat.</p>

<div class="mt-5 flex flex-wrap items-center gap-2 text-xs">
    <span class="w-16 text-sm font-bold">Status</span>
    <a href="{{ route('pemantauan.index', ['desainer' => request('desainer')]) }}"
       class="rounded-full border-2 border-ink px-3 py-1.5 font-bold {{ request('status') ? 'bg-white' : 'bg-ink text-white' }}">Semua</a>
    @foreach ($semuaStatus as $status)
        <a href="{{ route('pemantauan.index', ['status' => $status->code, 'desainer' => request('desainer')]) }}"
           class="rounded-full border-2 border-ink px-3 py-1.5 font-bold {{ request('status') === $status->code ? 'bg-ink text-white' : 'bg-white' }}">{{ $status->label }}</a>
    @endforeach
</div>
<div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
    <span class="w-16 text-sm font-bold">Desainer</span>
    <a href="{{ route('pemantauan.index', ['status' => request('status')]) }}"
       class="rounded-full border-2 border-ink px-3 py-1.5 font-bold {{ request('desainer') ? 'bg-white' : 'bg-ink text-white' }}">Semua</a>
    @foreach ($desainer as $orang)
        <a href="{{ route('pemantauan.index', ['status' => request('status'), 'desainer' => $orang->id]) }}"
           class="rounded-full border-2 border-ink px-3 py-1.5 font-bold {{ request('desainer') == $orang->id ? 'bg-ink text-white' : 'bg-white' }}">{{ $orang->name }}</a>
    @endforeach
</div>

<section class="card mt-4 overflow-x-auto">
    <table class="w-full min-w-[820px] text-left text-sm">
        <thead class="bg-thead text-[11px] font-bold uppercase tracking-[0.1em]">
            <tr>
                <th class="px-5 py-3">Kode</th>
                <th class="px-5 py-3">Layanan</th>
                <th class="px-5 py-3">Desainer</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3">Progres</th>
                <th class="px-5 py-3">Tenggat</th>
                <th class="px-5 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($proyek as $item)
                @php $sisa = $item->sisaHari(); $selesai = $item->status->code == 'selesai'; @endphp
                <tr class="border-t">
                    <td class="px-5 py-3 font-mono">{{ $item->order->order_code }}</td>
                    <td class="px-5 py-3">
                        <div class="font-bold">{{ $item->order->title }}</div>
                        <div class="text-xs text-ink/60">{{ $item->order->service->name }}</div>
                    </td>
                    <td class="px-5 py-3">
                        @if ($item->designer)
                            <span class="font-bold">{{ $item->designer->name }}</span>
                        @else
                            <span class="badge badge-gray">Belum ditugaskan</span>
                        @endif
                    </td>
                    <td class="px-5 py-3"><span class="badge badge-blue">{{ $item->status->label }}</span></td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <div class="h-2.5 w-24 rounded-full border-2 border-ink bg-white">
                                <div class="h-full rounded-full {{ $sisa < 0 && ! $selesai ? 'bg-coral' : 'bg-violet' }}" style="width: {{ $item->progress_percentage }}%"></div>
                            </div>
                            <span class="font-bold">{{ $item->progress_percentage }}%</span>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        <div>{{ $item->deadline->translatedFormat('d M Y') }}</div>
                        @if ($selesai)
                            <div class="text-xs text-ink/60">Selesai</div>
                        @elseif ($sisa < 0)
                            <span class="badge badge-red">Terlambat {{ abs($sisa) }} hari</span>
                        @elseif ($sisa <= 3)
                            <span class="badge badge-amber">Mendekati tenggat, {{ $sisa }} hari lagi</span>
                        @else
                            <div class="text-xs text-ink/60">{{ $sisa }} hari lagi</div>
                        @endif
                    </td>
                    <td class="px-5 py-3"><a href="{{ route('pemantauan.show', $item) }}" class="btn-outline py-1.5">Detail</a></td>
                </tr>
            @empty
                <tr class="border-t"><td colspan="7" class="px-5 py-8 text-center text-ink/60">Tidak ada proyek yang cocok.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="flex flex-col gap-3 border-t px-5 py-4 text-xs text-ink/60 sm:flex-row sm:items-center sm:justify-between">
        <span>Menampilkan {{ $proyek->firstItem() ?? 0 }}–{{ $proyek->lastItem() ?? 0 }} dari {{ $proyek->total() }} proyek</span>
        <div>{{ $proyek->links() }}</div>
    </div>
</section>
@endsection
