@extends('layouts.guest')
@section('title', 'Daftar')

@section('content')
<p class="text-xs font-semibold uppercase tracking-wide text-brand-dark">Pelanggan baru</p>
<h1 class="mt-1 text-[28px] font-semibold leading-tight">Daftar akun</h1>
<p class="mt-2 text-sm text-slate-500">Buat akun untuk memesan layanan PT Media Creative.</p>

<form method="POST" action="{{ route('customer.register') }}" novalidate class="mt-7" x-data="{ lihat: false }">
    @csrf

    <label for="name" class="block text-sm font-semibold">Nama Lengkap</label>
    <input id="name" name="name" type="text" value="{{ old('name') }}" autofocus
           class="field mt-2 @error('name') border-lost @enderror">
    @error('name') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

    <label for="email" class="mt-4 block text-sm font-semibold">Email</label>
    <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email"
           class="field mt-2 @error('email') border-lost @enderror">
    @error('email') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

    <label for="phone" class="mt-4 block text-sm font-semibold">Nomor HP</label>
    <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
           class="field mt-2 @error('phone') border-lost @enderror">
    @error('phone')
        <p class="mt-1 text-sm text-lost">{{ $message }}</p>
    @else
        <p class="mt-1 text-xs text-slate-500"></p>
    @enderror

    <label for="institution" class="mt-4 block text-sm font-semibold">Instansi <span class="font-normal text-slate-500">(opsional)</span></label>
    <input id="institution" name="institution" type="text" value="{{ old('institution') }}"
           class="field mt-2 @error('institution') border-lost @enderror">
    @error('institution') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

    <label for="password" class="mt-4 block text-sm font-semibold">Password</label>
    <input id="password" name="password" :type="lihat ? 'text' : 'password'" type="password" autocomplete="new-password"
           class="field mt-2 @error('password') border-lost @enderror">
    @error('password') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

    <label for="password_confirmation" class="mt-4 block text-sm font-semibold">Konfirmasi Password</label>
    <input id="password_confirmation" name="password_confirmation" :type="lihat ? 'text' : 'password'" type="password" autocomplete="new-password"
           class="field mt-2">

    <label class="mt-4 flex cursor-pointer items-center gap-2 text-sm text-slate-500">
        <input type="checkbox" x-model="lihat" class="size-4 rounded border-slate-300 accent-brand-dark">
        Tampilkan password
    </label>

    <button type="submit" class="btn-primary mt-6 w-full py-3.5">Daftar</button>

    <p class="mt-5 text-center text-sm text-slate-500">
        Sudah punya akun? <a href="{{ route('customer.login') }}" class="font-semibold text-brand-dark hover:underline">Masuk</a>
    </p>
</form>
@endsection
