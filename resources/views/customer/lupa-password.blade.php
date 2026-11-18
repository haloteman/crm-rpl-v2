@extends('layouts.guest')
@section('title', 'Lupa Password')

@section('content')
<p class="text-xs font-semibold uppercase tracking-wide text-brand-dark">Panel pelanggan</p>
<h1 class="mt-1 text-[28px] font-semibold leading-tight">Lupa password</h1>
<p class="mt-2 text-sm text-slate-500">Masukkan email yang terdaftar. Kami akan mengirim tautan untuk membuat password baru.</p>

@if (session('status'))
    <div class="mt-5 flex items-start gap-2 rounded-lg bg-won-tint px-4 py-3 text-sm text-won">
        <x-icon name="check-circle" class="mt-0.5 size-5 shrink-0" />
        <p>{{ session('status') }}</p>
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}" novalidate class="mt-7">
    @csrf

    <label for="email" class="block text-sm font-semibold">Email</label>
    <input id="email" name="email" type="email" value="{{ old('email') }}" autofocus
           class="field mt-2 py-3.5 @error('email') border-lost @enderror">
    @error('email') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

    <button type="submit" class="btn-primary mt-6 w-full py-3.5">Kirim Tautan Reset</button>

    <p class="mt-5 text-center text-sm text-slate-500">
        <a href="{{ route('customer.login') }}" class="font-semibold text-brand-dark hover:underline">Kembali ke halaman masuk</a>
    </p>
</form>
@endsection
