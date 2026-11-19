@extends('layouts.app')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@php
    $me = auth()->user();
    $izin = $me->getAllPermissions()->pluck('name');
@endphp

@section('content')
<div class="flex items-end justify-between gap-4">
    <div>
        <p class="eyebrow">Dashboard</p>
        <h1 class="page-title mt-1">Halo, {{ strtok($me->name, ' ') }}.</h1>
    </div>
    <span class="badge badge-green">{{ $me->getRoleNames()->first() }}</span>
</div>

<div class="mt-6 grid gap-5 {{ $me->hasRole('Customer') ? '' : 'lg:grid-cols-[1.6fr_1fr]' }}">
    <div class="card relative overflow-hidden p-6 sm:p-8">
        <div class="absolute -right-10 -top-10 size-36 rounded-full border-2 border-ink bg-lime"></div>
        <span class="badge bg-ink text-white">Dashboard</span>
        <h2 class="relative mt-4 text-3xl font-extrabold">Dashboard sedang disiapkan.</h2>
        <p class="relative mt-2 max-w-md text-slate-600">Ringkasan sesuai role Anda akan tampil di sini.</p>
    </div>

    @unless ($me->hasRole('Customer'))
    <div class="card p-6">
        <h2 class="flex items-center gap-2 text-xl font-extrabold"><x-icon name="shield-check" class="size-6" /> Akses Anda</h2>
        <p class="mt-2 text-sm text-slate-600">Role {{ $me->getRoleNames()->first() }} memiliki {{ $izin->count() }} permission aktif.</p>
        <div class="mt-4 flex flex-wrap gap-2">
            @foreach ($izin as $nama)
                <span class="rounded-md border-[1.5px] border-ink px-1.5 py-0.5 font-mono text-xs">{{ $nama }}</span>
            @endforeach
        </div>
    </div>
    @endunless
</div>
@endsection
