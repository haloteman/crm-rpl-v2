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
<section class="mx-auto max-w-6xl px-4 py-12">
    <p class="eyebrow">Tentang Kami</p>
    <h1 class="mt-2 text-3xl font-bold">PT Media Creative</h1>
    <p class="mt-3 max-w-2xl text-slate-600">[PROFIL PERUSAHAAN: SIAPA KAMI, KAPAN BERDIRI, DAN APA YANG KAMI KERJAKAN]</p>

    <div class="mt-8 grid gap-4 sm:grid-cols-2">
        <div class="card p-5"><p class="eyebrow">Visi</p><p class="mt-1">[VISI PERUSAHAAN]</p></div>
        <div class="card p-5"><p class="eyebrow">Misi</p><p class="mt-1">[MISI PERUSAHAAN]</p></div>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 pb-12">
    <p class="eyebrow">Cara Kami Bekerja</p>
    <h2 class="mt-2 text-2xl font-bold">Tiga hal yang kami pegang.</h2>
    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        @foreach ($prinsip as $nomor => [$judul, $isi])
            <div class="card p-5">
                <div class="font-bold">0{{ $nomor + 1 }} {{ $judul }}</div>
                <p class="mt-1 text-sm text-slate-500">{{ $isi }}</p>
            </div>
        @endforeach
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 pb-12">
    <p class="eyebrow">Tim Kami</p>
    <h2 class="mt-2 text-2xl font-bold">Orang di balik karya.</h2>
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @for ($i = 0; $i < 4; $i++)
            <div class="card p-5">
                <div class="font-semibold">[NAMA]</div>
                <div class="text-sm text-slate-500">[PERAN]</div>
            </div>
        @endfor
    </div>
</section>
@endsection
