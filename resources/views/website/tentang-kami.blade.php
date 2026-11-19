@extends('layouts.website')
@section('title', 'Tentang Kami')

@php
    $prinsip = [
        ['Sesuai keahlian', 'Proyek dibagikan kepada desainer yang paling sesuai.'],
        ['Transparan', 'Progres pesanan dapat dipantau kapan saja.'],
        ['Disepakati bersama', 'Kebutuhan, harga, dan ketentuan revisi dibahas lebih dulu.'],
    ];
@endphp

@section('content')
<section class="border-b-2 border-ink">
    <div class="mx-auto grid max-w-6xl items-center gap-10 px-4 py-16 lg:grid-cols-[1.4fr_1fr]">
        <div>
            <p class="eyebrow">Tentang Kami</p>
            <h1 class="mt-2 text-5xl font-extrabold sm:text-6xl">PT Media Creative</h1>
            <p class="mt-4 max-w-xl text-slate-700">[PROFIL PERUSAHAAN: SIAPA KAMI, KAPAN BERDIRI, DAN APA YANG KAMI KERJAKAN]</p>
        </div>
        <div aria-hidden="true" class="hidden grid-cols-2 gap-4 lg:grid">
            <div class="aspect-square rounded-2xl border-2 border-ink bg-ink shadow-[4px_4px_0_0_var(--color-violet)]"></div>
            <div class="aspect-square rounded-full border-2 border-ink bg-lime shadow-hard"></div>
            <div class="aspect-square rounded-[50%_50%_0_50%] border-2 border-ink bg-violet shadow-hard"></div>
            <div class="aspect-square rounded-2xl border-2 border-ink bg-sun shadow-hard"></div>
        </div>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-16">
    <div class="grid gap-5 sm:grid-cols-2">
        <div class="card p-5"><p class="eyebrow">Visi</p><p class="mt-2 font-semibold">[VISI PERUSAHAAN]</p></div>
        <div class="card bg-lime p-5"><p class="eyebrow text-ink">Misi</p><p class="mt-2 font-semibold">[MISI PERUSAHAAN]</p></div>
    </div>

    <p class="eyebrow mt-16">Cara Kami Bekerja</p>
    <h2 class="mt-2 text-4xl font-extrabold">Tiga hal yang kami pegang.</h2>
    <div class="mt-8 grid gap-6 sm:grid-cols-3">
        @foreach ($prinsip as $nomor => [$judul, $isi])
            <div class="border-t-2 border-ink pt-4">
                <div class="font-display text-3xl font-extrabold text-violet">0{{ $nomor + 1 }}</div>
                <div class="mt-2 font-bold">{{ $judul }}</div>
                <p class="mt-1 text-sm text-slate-600">{{ $isi }}</p>
            </div>
        @endforeach
    </div>
</section>

<section class="border-t-2 border-ink bg-sand">
    <div class="mx-auto max-w-6xl px-4 py-16">
        <p class="eyebrow">Tim Kami</p>
        <h2 class="mt-2 text-4xl font-extrabold">Orang di balik karya.</h2>
        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach (['bg-ink', 'bg-lime', 'bg-violet', 'bg-sun'] as $warna)
                <div class="card p-3">
                    <div class="aspect-[4/3] rounded-xl border-2 border-ink {{ $warna }}"></div>
                    <div class="mt-3 px-1 font-bold">[NAMA]</div>
                    <div class="px-1 pb-1 text-sm text-slate-600">[PERAN]</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
