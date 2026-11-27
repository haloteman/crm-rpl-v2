@extends('layouts.app')
@section('title', $project->order->title)
@section('heading', 'Detail Proyek')

@section('content')
<div x-data="{ modal: '{{ $errors->has('note') || $errors->has('designer_id') ? 'pindah' : '' }}' }">

<p class="text-sm">
    <a href="{{ route('pemantauan.index') }}" class="font-bold text-violet underline underline-offset-4">Semua Proyek</a>
    / <span class="font-mono">{{ $project->order->order_code }}</span>
</p>

<div class="mt-4 flex flex-wrap items-start justify-between gap-3">
    <div>
        <div class="flex flex-wrap gap-2">
            <span class="badge badge-blue">{{ $project->status->label }}</span>
            <span class="badge badge-gray">{{ $project->order->service->name }}</span>
        </div>
        <h1 class="page-title mt-2">{{ $project->order->title }}</h1>
        @role('Owner')
            <p class="page-sub">Hanya lihat. Penugasan dan progres diatur Kepala Staf dan Desainer.</p>
        @endrole
    </div>
    @can('project.assign')
        @if (in_array($project->status->code, ['dikerjakan', 'revisi']))
            <button type="button" @click="modal = 'pindah'" class="btn-outline">Pindahkan</button>
        @elseif ($project->status->code == 'menunggu_penugasan')
            <a href="{{ route('penugasan.index') }}" class="btn-primary">Tugaskan</a>
        @endif
    @endcan
</div>

@include('pemantauan.rincian')

@can('project.assign')
    <div x-show="modal === 'pindah'" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-ink/50 p-4" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center">
            <form method="POST" action="{{ route('penugasan.pindah', $project) }}" class="card w-full max-w-lg p-5 sm:p-6" @click.outside="modal = ''">
                @csrf
                <p class="eyebrow">Pindahkan proyek</p>
                <h2 class="font-display text-xl font-extrabold">{{ $project->order->order_code }} · {{ $project->order->title }}</h2>
                <p class="mt-3 rounded-xl border-2 border-ink bg-tint px-3 py-2 text-sm">
                    Riwayat progres tetap tersimpan. Desainer lama tidak bisa membuka proyek ini lagi dan keluar dari chat proyek.
                </p>

                <p class="mt-4 text-sm font-medium">Desainer pengganti</p>
                <div class="mt-2 space-y-2">
                    @foreach ($desainer->where('id', '!=', $project->designer_id) as $orang)
                        <label class="flex cursor-pointer items-center gap-2 rounded-xl border-2 border-ink bg-white px-3 py-2.5 text-sm has-checked:bg-lime">
                            <input type="radio" name="designer_id" value="{{ $orang->id }}" @checked(old('designer_id') == $orang->id)>
                            <span class="font-bold">{{ $orang->name }}</span>
                            @if ($orang->designerSkills->contains('service_id', $project->order->service_id))
                                <span class="badge badge-green">Cocok</span>
                            @endif
                            <span class="ml-auto text-xs font-bold">{{ $orang->proyek_aktif }} proyek aktif</span>
                        </label>
                    @endforeach
                </div>
                @error('designer_id') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

                <label for="note" class="mt-4 block text-sm font-medium">Alasan pemindahan</label>
                <textarea id="note" name="note" rows="3" class="field mt-1.5">{{ old('note') }}</textarea>
                @error('note') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

                <div class="mt-6 flex justify-end gap-3 border-t-2 border-line pt-4">
                    <button type="button" @click="modal = ''" class="btn-outline">Batal</button>
                    <button type="submit" class="btn-dark">Pindahkan</button>
                </div>
            </form>
        </div>
    </div>
@endcan

</div>
@endsection
