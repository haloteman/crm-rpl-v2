@extends('layouts.base')
@section('title', 'Ubah Password')

@section('body')
@php $wajibGanti = (bool) auth()->user()->internalProfile?->must_change_password; @endphp
<div class="flex min-h-screen flex-col bg-canvas bg-[radial-gradient(rgb(18_24_38/.12)_1px,transparent_1px)] bg-size-[20px_20px]">
    {{-- bar atas --}}
    <header class="border-b-2 border-ink bg-canvas">
        <div class="mx-auto flex h-[70px] max-w-6xl items-center justify-between gap-3 px-4">
            <div class="flex items-center gap-3">
                <x-logo gelap class="[&>span:last-child>span:last-child]:hidden" />
                <span class="hidden rounded-full bg-ink px-3 py-1 text-[11px] font-bold text-white sm:inline">CRM Internal</span>
            </div>
            <div class="flex items-center gap-3 text-[13px]">
                <span class="hidden font-semibold sm:inline">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-outline px-3.5! py-2! text-xs!">Keluar</button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto grid w-full max-w-6xl flex-1 items-start gap-10 px-4 py-10 lg:grid-cols-[1fr_520px] lg:py-14">
        {{-- teks kiri --}}
        <div class="lg:pt-6">
            <p class="eyebrow">{{ $wajibGanti ? 'Login pertama' : 'Keamanan akun' }}</p>
            <h1 class="mt-3 font-display text-4xl font-extrabold leading-[1.05] tracking-tight sm:text-5xl">
                @if ($wajibGanti)
                    Buat password <span class="sorot">milik Anda</span> sendiri.
                @else
                    Ubah <span class="sorot">password</span> Anda.
                @endif
            </h1>

            @if ($wajibGanti)
                <p class="mt-5 max-w-md text-[15px] text-ink/70">Password awal dibuat otomatis oleh sistem dan diketahui Super Admin. Ganti sekarang agar hanya Anda yang tahu.</p>
                <ol class="mt-6 hidden space-y-4 sm:block">
                    <li class="flex items-start gap-3">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-full border-2 border-ink bg-lime text-sm font-extrabold">1</span>
                        <div><div class="text-sm font-bold">Masuk dengan NIP</div><p class="text-[13px] text-ink/60">Sudah berhasil.</p></div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-full border-2 border-ink bg-kuning text-sm font-extrabold">2</span>
                        <div><div class="text-sm font-bold">Ganti password awal</div><p class="text-[13px] text-ink/60">Wajib sebelum membuka fitur lain.</p></div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-full border-2 border-ink bg-white text-sm font-extrabold">3</span>
                        <div><div class="text-sm font-bold">Mulai bekerja</div><p class="text-[13px] text-ink/60">Anda diarahkan ke dashboard sesuai role.</p></div>
                    </li>
                </ol>
            @else
                <p class="mt-5 max-w-md text-[15px] text-ink/70">Masukkan password lama, lalu buat password baru yang hanya Anda yang tahu.</p>
            @endif
        </div>

        {{-- kartu form --}}
        <div class="rounded-3xl border-2 border-ink bg-white p-6 shadow-keras-lg sm:p-8">
            <h2 class="font-display text-2xl font-extrabold">{{ $wajibGanti ? 'Ganti password awal' : 'Ubah Password' }}</h2>

            @if ($wajibGanti)
                <div class="mt-4 flex items-start gap-3 rounded-xl border-2 border-ink bg-kuning px-4 py-3 text-[13px] font-medium">
                    <x-icon name="info" class="mt-0.5 size-5 shrink-0" />
                    <p>Kamu masih pakai password sementara. Ganti dulu sebelum lanjut.</p>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" novalidate class="mt-5" x-data="{ lihat: false }">
                @csrf

                <label for="password_lama" class="block text-[13px] font-bold">Password Lama</label>
                <input id="password_lama" name="password_lama" :type="lihat ? 'text' : 'password'" type="password" autocomplete="current-password"
                       placeholder="Masukkan password lama"
                       class="field mt-2 @error('password_lama') border-lost @enderror">
                @error('password_lama') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

                <label for="password_baru" class="mt-5 block text-[13px] font-bold">Password Baru</label>
                <input id="password_baru" name="password_baru" :type="lihat ? 'text' : 'password'" type="password" autocomplete="new-password"
                       placeholder="Masukkan password baru"
                       class="field mt-2 @error('password_baru') border-lost @enderror">
                @error('password_baru') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

                <label for="password_baru_confirmation" class="mt-5 block text-[13px] font-bold">Konfirmasi Password Baru</label>
                <input id="password_baru_confirmation" name="password_baru_confirmation" :type="lihat ? 'text' : 'password'" type="password" autocomplete="new-password"
                       placeholder="Ulangi password baru"
                       class="field mt-2">

                <label class="mt-4 flex cursor-pointer items-center gap-2 text-[13px] text-ink/70">
                    <input type="checkbox" x-model="lihat" class="size-4 rounded border-ink accent-brand-dark">
                    Tampilkan password
                </label>

                <button type="submit" class="btn-primary mt-6 w-full py-3.5">{{ $wajibGanti ? 'Simpan dan masuk ke dashboard' : 'Simpan' }}</button>
            </form>

            @unless ($wajibGanti)
                <a href="{{ route('beranda') }}" class="mt-4 block text-center text-[13px] font-bold underline underline-offset-4">Kembali</a>
            @endunless
        </div>
    </main>
</div>
@endsection