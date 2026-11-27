@extends('layouts.app')
@section('title', 'Notifikasi')
@section('heading', 'Notifikasi')

@section('content')
<div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <p class="eyebrow">Portal Customer</p>
        <h1 class="page-title">Notifikasi.</h1>
        <p class="page-sub">Pemberitahuan setiap status pesanan Anda berubah.</p>
    </div>
    @if (auth()->user()->unreadNotifications->isNotEmpty())
        <form method="POST" action="{{ route('notifikasi.baca-semua') }}">
            @csrf
            <button type="submit" class="btn-outline">Tandai semua sudah dibaca</button>
        </form>
    @endif
</div>

<div class="card mt-5">
    @forelse ($notifikasi as $item)
        <form method="POST" action="{{ route('notifikasi.baca', $item->id) }}" class="border-b border-line last:border-b-0">
            @csrf
            <button type="submit" class="flex w-full items-start gap-3 px-5 py-4 text-left text-sm {{ $item->read_at ? '' : 'bg-lime-tint' }} hover:bg-paper">
                <span class="mt-1.5 size-2.5 shrink-0 rounded-full {{ $item->read_at ? 'bg-transparent' : 'bg-coral' }}"></span>
                <span class="flex-1">
                    <span class="font-mono text-xs">{{ $item->data['order_code'] }}</span>
                    <span class="block font-bold">{{ $item->data['judul'] }}: {{ $item->data['status'] }}</span>
                    <span class="text-xs text-ink/60">{{ $item->created_at->diffForHumans() }}</span>
                </span>
            </button>
        </form>
    @empty
        <p class="px-5 py-8 text-center text-sm text-ink/60">Belum ada notifikasi.</p>
    @endforelse
</div>
@if ($notifikasi->hasPages())
    <div class="mt-4">{{ $notifikasi->links() }}</div>
@endif
@endsection
