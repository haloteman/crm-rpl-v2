@extends('layouts.guest')
@section('title', 'Masuk')

@section('content')
<p class="text-xs font-semibold uppercase tracking-wide text-brand-dark">Panel pelanggan</p>
<h1 class="mt-1 text-[28px] font-semibold leading-tight">Masuk ke akun Anda</h1>
<p class="mt-2 text-sm text-slate-500">Masukkan email dan password yang Anda buat saat mendaftar.</p>

@if (session('status'))
    <div class="mt-5 flex items-start gap-2 rounded-lg bg-won-tint px-4 py-3 text-sm text-won">
        <x-icon name="check-circle" class="mt-0.5 size-5 shrink-0" />
        <p>{{ session('status') }}</p>
    </div>
@endif

<form method="POST" action="{{ route('customer.login') }}" novalidate class="mt-7" x-data="{ lihat: false }">
    @csrf

    <label for="email" class="block text-sm font-semibold">Email</label>
    <input id="email" name="email" type="email" autocomplete="username" autofocus
           value="{{ old('email') }}" placeholder="Masukkan email Anda"
           class="field mt-2 py-3.5">

    <div class="mt-5 flex items-center justify-between">
        <label for="password" class="block text-sm font-semibold">Password</label>
        <a href="{{ route('password.request') }}" class="text-xs font-medium text-brand-dark hover:underline">Lupa password?</a>
    </div>
    <div class="relative mt-2">
        <input id="password" name="password" :type="lihat ? 'text' : 'password'" type="password"
               autocomplete="current-password" placeholder="Masukkan password Anda"
               class="field py-3.5 pr-12">
        <button type="button" @click="lihat = ! lihat"
                :aria-label="lihat ? 'Sembunyikan password' : 'Tampilkan password'"
                class="absolute inset-y-0 right-0 flex w-12 items-center justify-center text-slate-500 hover:text-ink">
            <x-icon name="eye" x-show="! lihat" class="size-5" />
            <x-icon name="eye-slash" x-show="lihat" x-cloak class="size-5" />
        </button>
    </div>

    @if ($errors->any())
        <div class="mt-4 flex items-start gap-2 rounded-lg bg-lost-tint px-4 py-3 text-sm text-lost" role="alert">
            <x-icon name="exclamation-circle" class="mt-0.5 size-5 shrink-0" />
            <p>{{ $errors->first() }}</p>
        </div>
    @endif

    <button type="submit" class="btn-primary mt-6 w-full py-3.5">
        Masuk <x-icon name="arrow-right" class="size-4" />
    </button>

    <p class="mt-5 text-center text-sm text-slate-500">
        Belum punya akun? <a href="{{ route('customer.register') }}" class="font-semibold text-brand-dark hover:underline">Daftar</a>
    </p>
</form>
@endsection
