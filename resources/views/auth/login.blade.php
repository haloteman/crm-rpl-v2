@extends('layouts.guest')
@section('title', 'Login')
@section('kiri-tema', 'lavender')
@section('kiri-label', 'CRM')
@section('kiri-judul')Masuk dengan <span class="sorot">NIP</span> Anda.@endsection
@section('kiri-daftar')
<ul class="mt-6 space-y-2.5 text-[15px]">
    <li class="flex items-center gap-2.5"><x-icon name="check" class="size-4 shrink-0 text-brand-dark" /> NIP dan password awal diberikan oleh Super Admin</li>
    <li class="flex items-center gap-2.5"><x-icon name="check" class="size-4 shrink-0 text-brand-dark" /> Password awal wajib diganti saat login pertama</li>
    <li class="flex items-center gap-2.5"><x-icon name="check" class="size-4 shrink-0 text-brand-dark" /> Sesi berakhir otomatis setelah 12 jam tanpa aktivitas</li>
</ul>
@endsection

@section('content')
<h1 class="text-3xl font-extrabold leading-tight">Login</h1>
<p class="mt-1 text-sm text-ink/60">Owner, Super Admin, Admin, Kepala Staf Desainer, dan Desainer.</p>

<form method="POST" action="{{ route('login') }}" novalidate class="mt-6" x-data="{ lihat: false }">
    @csrf

    <label for="nip" class="block text-[13px] font-bold">NIP</label>
    <input id="nip" name="nip" type="text" autocomplete="username" autofocus
           value="{{ old('nip') }}" placeholder="Contoh: MC-26-0001"
           class="field mt-2 py-3">

    <label for="password" class="mt-5 block text-[13px] font-bold">Password</label>
    <div class="relative mt-2">
        <input id="password" name="password" :type="lihat ? 'text' : 'password'" type="password"
               autocomplete="current-password" placeholder="Masukkan password Anda"
               class="field py-3 pr-12">
        <button type="button" @click="lihat = ! lihat"
                :aria-label="lihat ? 'Sembunyikan password' : 'Tampilkan password'"
                class="absolute inset-y-0 right-0 flex w-12 items-center justify-center text-ink/60 hover:text-ink">
            <x-icon name="eye" x-show="! lihat" class="size-5" />
            <x-icon name="eye-slash" x-show="lihat" x-cloak class="size-5" />
        </button>
    </div>

    @if ($errors->any())
        <div class="mt-4 flex items-start gap-2 rounded-xl border-2 border-ink bg-lost-tint px-4 py-3 text-sm text-lost" role="alert">
            <x-icon name="exclamation-circle" class="mt-0.5 size-5 shrink-0" />
            <p>{{ $errors->first() }}</p>
        </div>
    @endif

    <button type="submit" class="btn-primary mt-6 w-full py-3.5">
        Login <x-icon name="arrow-right" class="size-4" />
    </button>

    <p class="mt-4 flex items-start gap-2 rounded-xl border-[1.5px] border-ink bg-[#dbeafe] px-4 py-3 text-[13px]">
        <x-icon name="question-circle" class="mt-0.5 size-5 shrink-0" />
        <span><strong>Lupa password?</strong> Minta Super Admin untuk mereset password Anda.</span>
    </p>

    <p class="mt-4 text-center text-[13px] text-ink/70">
        Pelanggan? <a href="{{ route('customer.login') }}" class="font-bold text-brand-dark underline underline-offset-4">Masuk dengan email</a>
    </p>
</form>
@endsection