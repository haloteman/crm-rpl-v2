@extends('layouts.guest')
@section('title', 'Reset Password')

@section('content')
<p class="text-xs font-semibold uppercase tracking-wide text-brand-dark">Panel pelanggan</p>
<h1 class="mt-1 text-[28px] font-semibold leading-tight">Buat password baru</h1>

<form method="POST" action="{{ route('password.store') }}" novalidate class="mt-7" x-data="{ lihat: false }">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ old('email', $email) }}">

    @error('email')
        <div class="mb-5 flex items-start gap-2 rounded-lg bg-lost-tint px-4 py-3 text-sm text-lost" role="alert">
            <x-icon name="exclamation-circle" class="mt-0.5 size-5 shrink-0" />
            <p>{{ $message }}</p>
        </div>
    @enderror

    <label for="password" class="block text-sm font-semibold">Password Baru</label>
    <input id="password" name="password" :type="lihat ? 'text' : 'password'" type="password" autocomplete="new-password"
           class="field mt-2 @error('password') border-lost @enderror">
    @error('password') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

    <label for="password_confirmation" class="mt-4 block text-sm font-semibold">Konfirmasi Password Baru</label>
    <input id="password_confirmation" name="password_confirmation" :type="lihat ? 'text' : 'password'" type="password" autocomplete="new-password"
           class="field mt-2">

    <label class="mt-4 flex cursor-pointer items-center gap-2 text-sm text-slate-500">
        <input type="checkbox" x-model="lihat" class="size-4 rounded border-slate-300 accent-brand-dark">
        Tampilkan password
    </label>

    <button type="submit" class="btn-primary mt-6 w-full py-3.5">Simpan Password</button>
</form>
@endsection
