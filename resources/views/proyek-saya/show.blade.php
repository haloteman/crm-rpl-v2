@extends('layouts.app')
@section('title', $project->order->title)
@section('heading', 'Proyek Saya')

@php
    $aktif = ! $project->status->is_final && $project->status->code != 'menunggu_penugasan';
    $daftarTahap = ['Konsep dan sketsa awal', 'Pengembangan visual', 'Penataan layout', 'Finalisasi desain', 'Hasil akhir'];
@endphp

@section('content')
<div x-data="{ modal: '{{ $errors->hasAny(['stage', 'percentage', 'lampiran_file', 'lampiran_link']) ? 'progres' : '' }}' }">

<p class="text-sm">
    <a href="{{ route('proyek-saya.index') }}" class="font-bold text-violet underline underline-offset-4">Proyek Saya</a>
    / <span class="font-mono">{{ $project->order->order_code }}</span>
</p>

@error('status') <p class="mt-3 rounded-xl border-2 border-ink bg-lost-tint px-4 py-3 text-sm font-medium text-lost">{{ $message }}</p> @enderror

<div class="mt-4 flex flex-wrap items-start justify-between gap-3">
    <div>
        <div class="flex flex-wrap gap-2">
            <span class="badge badge-blue">{{ $project->status->label }}</span>
            <span class="badge badge-gray">{{ $project->order->service->name }}</span>
        </div>
        <h1 class="page-title mt-2">{{ $project->order->title }}</h1>
    </div>
    <div class="flex flex-wrap gap-2">
        @can('progress.update')
            @if ($aktif)
                <button type="button" @click="modal = 'progres'" class="btn-primary">Perbarui Progres</button>
            @endif
            @if ($project->bolehPindahKe('revisi'))
                <button type="button" @click="modal = 'revisi'" class="btn-outline">Tandai Revisi</button>
            @endif
            @if ($project->bolehPindahKe('selesai'))
                <button type="button" @click="modal = 'selesai'" class="btn-dark">Tandai Selesai</button>
            @endif
        @endcan
        @if ($project->chat)
            <a href="{{ route('chat.show', $project->chat) }}" class="btn-outline">Buka Chat</a>
        @endif
    </div>
</div>

@include('pemantauan.rincian')

<p class="mt-5 rounded-xl border-2 border-ink bg-tint px-4 py-3 text-sm">Harga dan pembayaran tidak ditampilkan untuk desainer.</p>

@can('progress.update')
    {{-- popup perbarui progres --}}
    <div x-show="modal === 'progres'" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-ink/50 p-4" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center">
            <form method="POST" action="{{ route('proyek-saya.progres', $project) }}" enctype="multipart/form-data"
                  x-data="{ tahap: '{{ old('stage') }}', persen: {{ old('percentage', $project->progress_percentage) }} }"
                  class="card w-full max-w-lg p-5 sm:p-6" @click.outside="modal = ''">
                @csrf
                <p class="eyebrow">Perbarui progres</p>
                <h2 class="font-display text-xl font-extrabold">{{ $project->order->order_code }} · {{ $project->order->title }}</h2>
                <p class="mt-3 rounded-xl border-2 border-ink bg-tint px-3 py-2 text-sm">Pembaruan ini langsung terlihat di tracking customer.</p>

                <label for="stage" class="mt-4 block text-sm font-medium">Tahap pengerjaan</label>
                <input type="text" id="stage" name="stage" x-model="tahap" class="field mt-1.5">
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach ($daftarTahap as $tahap)
                        <button type="button" @click="tahap = '{{ $tahap }}'" :class="tahap === '{{ $tahap }}' ? 'bg-ink text-white' : 'bg-white'"
                                class="rounded-full border-2 border-ink px-3 py-1 text-xs font-bold">{{ $tahap }}</button>
                    @endforeach
                </div>
                @error('stage') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

                <label for="percentage" class="mt-4 block text-sm font-medium">Persentase progres</label>
                <div class="mt-1.5 flex items-center gap-3">
                    <input type="range" min="0" max="100" x-model="persen" class="flex-1 accent-violet">
                    <input type="number" id="percentage" name="percentage" min="0" max="100" x-model="persen" class="field w-24">
                    <span>%</span>
                </div>
                @error('percentage') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

                <label for="note_progres" class="mt-4 block text-sm font-medium">Catatan <span class="font-normal text-ink/60">(opsional)</span></label>
                <textarea id="note_progres" name="note" rows="3" class="field mt-1.5">{{ old('note') }}</textarea>

                <label for="lampiran_file" class="mt-4 block text-sm font-medium">Lampiran <span class="font-normal text-ink/60">(opsional, JPG/PNG/PDF maks. 10 MB)</span></label>
                <input type="file" id="lampiran_file" name="lampiran_file" accept=".jpg,.jpeg,.png,.pdf" class="field mt-1.5">
                @error('lampiran_file') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

                <label for="lampiran_link" class="mt-4 block text-sm font-medium">Atau tempel tautan hasil</label>
                <input type="url" id="lampiran_link" name="lampiran_link" value="{{ old('lampiran_link') }}" placeholder="https://drive.google.com/..." class="field mt-1.5">
                <p class="mt-1 text-xs text-ink/60">Untuk file besar seperti video, unggah ke penyimpanan lain lalu tempel tautannya di sini.</p>
                @error('lampiran_link') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

                <div class="mt-6 flex justify-end gap-3 border-t-2 border-line pt-4">
                    <button type="button" @click="modal = ''" class="btn-outline">Batal</button>
                    <button type="submit" class="btn-dark">Simpan pembaruan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- popup tandai revisi --}}
    <div x-show="modal === 'revisi'" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-ink/50 p-4" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center">
            <form method="POST" action="{{ route('proyek-saya.revisi', $project) }}" class="card w-full max-w-md p-5 sm:p-6" @click.outside="modal = ''">
                @csrf
                <p class="eyebrow">Tandai revisi</p>
                <h2 class="font-display text-xl font-extrabold">{{ $project->order->title }}</h2>
                <p class="mt-3 text-sm">Tandai kalau customer meminta perbaikan. Cek lagi ketentuan revisi yang disepakati.</p>

                <label for="note_revisi" class="mt-4 block text-sm font-medium">Permintaan revisi <span class="font-normal text-ink/60">(opsional)</span></label>
                <textarea id="note_revisi" name="note" rows="3" class="field mt-1.5"></textarea>

                <div class="mt-6 flex justify-end gap-3 border-t-2 border-line pt-4">
                    <button type="button" @click="modal = ''" class="btn-outline">Batal</button>
                    <button type="submit" class="btn-dark">Tandai Revisi</button>
                </div>
            </form>
        </div>
    </div>

    {{-- popup tandai selesai --}}
    <div x-show="modal === 'selesai'" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-ink/50 p-4" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center">
            <form method="POST" action="{{ route('proyek-saya.selesai', $project) }}" class="card w-full max-w-md p-5 sm:p-6" @click.outside="modal = ''">
                @csrf
                <p class="eyebrow">Tandai selesai</p>
                <h2 class="font-display text-xl font-extrabold">{{ $project->order->title }}</h2>
                <p class="mt-3 rounded-xl border-2 border-ink bg-warn-tint px-3 py-2 text-sm">
                    Lampiran pada pembaruan progres terakhir akan jadi hasil akhir untuk customer. Setelah selesai, progres tidak bisa diperbarui lagi.
                </p>

                <div class="mt-6 flex justify-end gap-3 border-t-2 border-line pt-4">
                    <button type="button" @click="modal = ''" class="btn-outline">Batal</button>
                    <button type="submit" class="btn-dark">Tandai Selesai</button>
                </div>
            </form>
        </div>
    </div>
@endcan

</div>
@endsection
