@extends('layouts.app')
@section('title', 'Chat '.$chat->project->order->order_code)
@section('heading', 'Chat Proyek')

@php
    $proyekChat = $chat->project;
    $sayaCustomer = auth()->user()->hasRole('Customer');
@endphp

@section('content')
<p class="eyebrow">Chat Proyek</p>
<h1 class="page-title">Ngobrol dengan <span class="mark">{{ $sayaCustomer ? 'desainer.' : 'customer.' }}</span></h1>

<div class="card mt-5 grid overflow-hidden md:grid-cols-[300px_1fr]">
    @include('chat.daftar')

    <div class="flex min-w-0 flex-col">
        <div class="flex flex-wrap items-center gap-3 border-b-2 border-ink px-4 py-3">
            <div class="min-w-0 flex-1">
                <div class="font-display text-lg font-extrabold">{{ $proyekChat->order->title }}</div>
                <div class="font-mono text-xs text-ink/60">{{ $proyekChat->order->order_code }} · Desainer: {{ $proyekChat->designer?->name }}</div>
            </div>
            <span class="badge badge-blue">{{ $proyekChat->status->label }}</span>
            @if ($sayaCustomer)
                <a href="{{ route('pesanan.tracking', $proyekChat->order) }}" class="btn-outline py-1.5">Tracking</a>
            @elseif ($proyekChat->designer_id == auth()->id())
                <a href="{{ route('proyek-saya.show', $proyekChat) }}" class="btn-outline py-1.5">Detail proyek</a>
            @endif
        </div>

        <div class="flex-1 space-y-3 bg-paper p-4">
            @if ($proyekChat->order->consultation?->revision_terms)
                <p class="rounded-xl border-2 border-ink bg-warn-tint px-3 py-2 text-sm">
                    <b>Ketentuan revisi.</b> {{ $proyekChat->order->consultation->revision_terms }}
                </p>
            @endif

            @forelse ($chat->messages->sortBy('created_at') as $pesan)
                @php $dariSaya = $pesan->sender_id == auth()->id(); @endphp
                <div class="flex {{ $dariSaya ? 'justify-end' : '' }}">
                    <div class="max-w-[85%] rounded-xl border-2 border-ink px-3 py-2 text-sm shadow-hard-sm
                                {{ $pesan->message_type == 'revision_request' ? 'bg-pink-100' : ($dariSaya ? 'bg-lime' : 'bg-white') }}">
                        <div class="flex flex-wrap items-center gap-2 text-xs font-bold">
                            {{ $dariSaya ? 'Anda' : $pesan->sender->name }}
                            @if ($pesan->message_type == 'revision_request')
                                <span class="badge badge-gray">Permintaan revisi</span>
                            @endif
                        </div>
                        <p class="mt-1 whitespace-pre-line">{{ $pesan->body }}</p>
                        @foreach ($pesan->attachments as $lampiran)
                            <a href="{{ route('chat.unduh', $lampiran) }}" class="mt-2 inline-block rounded-lg border-2 border-ink bg-white px-2 py-1 text-xs font-bold">{{ $lampiran->original_name }}</a>
                        @endforeach
                        <div class="mt-1 text-[11px] text-ink/60">{{ $pesan->created_at->translatedFormat('d M, H.i') }}</div>
                    </div>
                </div>
            @empty
                <p class="py-8 text-center text-sm text-ink/60">Belum ada pesan. Mulai obrolan di bawah.</p>
            @endforelse
        </div>

        @if ($hanyaBaca)
            <p class="border-t-2 border-ink px-4 py-3 text-sm text-ink/60">Proyek sudah selesai. Chat ini hanya bisa dibaca.</p>
        @else
            <form method="POST" action="{{ route('chat.kirim', $chat) }}" enctype="multipart/form-data" class="border-t-2 border-ink p-4">
                @csrf
                <div class="flex gap-2">
                    <input type="text" name="body" value="{{ old('body') }}" placeholder="Tulis pesan untuk {{ $sayaCustomer ? 'desainer' : 'customer' }}" class="field flex-1">
                    <button type="submit" class="btn-primary">Kirim</button>
                </div>
                @error('body') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror
                <div class="mt-2 flex flex-wrap items-center justify-between gap-2 text-xs">
                    @if ($sayaCustomer)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="permintaan_revisi" value="1"> Tandai sebagai permintaan revisi
                        </label>
                    @endif
                    <label class="flex items-center gap-2">
                        Lampiran <input type="file" name="lampiran" accept=".jpg,.jpeg,.png" class="text-xs">
                    </label>
                    <span class="text-ink/60">Lampiran gambar JPG atau PNG, maksimal 2 MB.</span>
                </div>
                @error('lampiran') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror
            </form>
        @endif
    </div>
</div>
@endsection
