@extends('layouts.app')
@section('title', 'Proyek Belum Ditugaskan')
@section('heading', 'Proyek Belum Ditugaskan')

@section('content')
<div x-data="{ modal: '' }">

<p class="eyebrow">Penugasan Proyek</p>
<h1 class="page-title">Proyek belum ditugaskan.</h1>
<p class="page-sub max-w-2xl">Hanya proyek yang DP-nya sudah diterima Owner yang muncul di sini. Pilih desainer berdasarkan keahlian dan beban kerja.</p>

@error('designer_id') <p class="mt-3 rounded-xl border-2 border-ink bg-lost-tint px-4 py-3 text-sm font-medium text-lost">{{ $message }}</p> @enderror

<div class="mt-5 grid gap-5 md:grid-cols-2">
    @forelse ($proyek as $item)
        <section class="card p-5">
            <div class="flex flex-wrap items-center gap-2">
                <span class="font-mono text-sm">{{ $item->order->order_code }}</span>
                <span class="badge badge-green">DP diterima</span>
            </div>
            <h2 class="mt-1 font-display text-xl font-extrabold">{{ $item->order->title }}</h2>
            <p class="text-sm text-ink/60">{{ $item->order->service->name }} · {{ $item->order->contact_name }}</p>
            <p class="mt-3 line-clamp-3 text-sm">{{ $item->order->description }}</p>
            <div class="mt-3 flex flex-wrap gap-2 text-xs">
                <span class="badge badge-gray">{{ $item->order->references->count() }} referensi</span>
                <span class="badge badge-gray">Tenggat {{ $item->deadline->translatedFormat('d M Y') }}</span>
                <span class="badge {{ $item->sisaHari() < 0 ? 'badge-red' : 'badge-gray' }}">
                    {{ $item->sisaHari() < 0 ? 'terlambat '.abs($item->sisaHari()).' hari' : $item->sisaHari().' hari lagi' }}
                </span>
            </div>
            <div class="mt-4 flex gap-2">
                <button type="button" @click="modal = 'tugaskan-{{ $item->id }}'" class="btn-primary">Tugaskan</button>
                <a href="{{ route('pemantauan.show', $item) }}" class="btn-outline">Detail</a>
            </div>
        </section>

        {{-- popup pilih desainer, yang keahliannya cocok di atas --}}
        @php
            $urutDesainer = $desainer->sortByDesc(fn ($orang) => $orang->designerSkills->contains('service_id', $item->order->service_id));
        @endphp
        <div x-show="modal === 'tugaskan-{{ $item->id }}'" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-ink/50 p-4" role="dialog" aria-modal="true">
            <div class="flex min-h-full items-center justify-center">
                <form method="POST" action="{{ route('penugasan.simpan', $item) }}" class="card w-full max-w-lg p-5 sm:p-6" @click.outside="modal = ''">
                    @csrf
                    <p class="eyebrow">Penugasan Proyek</p>
                    <h2 class="font-display text-xl font-extrabold">{{ $item->order->order_code }} · {{ $item->order->title }}</h2>

                    <p class="mt-4 text-sm font-medium">Pilih desainer <span class="font-normal text-ink/60">urut berdasarkan kecocokan keahlian</span></p>
                    <div class="mt-2 space-y-2">
                        @foreach ($urutDesainer as $orang)
                            <label class="block cursor-pointer rounded-xl border-2 border-ink bg-white px-3 py-2.5 text-sm has-checked:bg-lime">
                                <div class="flex items-center gap-2">
                                    <input type="radio" name="designer_id" value="{{ $orang->id }}" required>
                                    <span class="font-bold">{{ $orang->id == auth()->id() ? 'Kerjakan sendiri' : $orang->name }}</span>
                                    @if ($orang->designerSkills->contains('service_id', $item->order->service_id))
                                        <span class="badge badge-green">Cocok</span>
                                    @endif
                                    <span class="ml-auto text-xs font-bold">{{ $orang->proyek_aktif }} proyek aktif</span>
                                </div>
                                <div class="mt-1.5 flex flex-wrap gap-1">
                                    @foreach ($orang->designerSkills as $keahlian)
                                        <span class="badge badge-gray">{{ $keahlian->service->name }}</span>
                                    @endforeach
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div class="mt-6 flex justify-end gap-3 border-t-2 border-line pt-4">
                        <button type="button" @click="modal = ''" class="btn-outline">Batal</button>
                        <button type="submit" class="btn-dark">Tugaskan</button>
                    </div>
                </form>
            </div>
        </div>
    @empty
        <p class="card p-8 text-center text-ink/60 md:col-span-2">Semua proyek sudah ditugaskan.</p>
    @endforelse
</div>

</div>
@endsection
