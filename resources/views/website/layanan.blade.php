@extends('layouts.website')
@section('title', 'Layanan')

@php
    $caraMemesan = [
        ['Ajukan Pesanan', 'Isi form kebutuhan setelah login.'],
        ['Konsultasi', 'Lanjutkan konsultasi dengan Owner lewat WhatsApp.'],
        ['Harga dan DP', 'Harga akhir dan DP tampil di akun Anda.'],
        ['Bayar DP', 'Proyek dikerjakan setelah DP diterima.'],
    ];
@endphp

@section('content')
<section class="mx-auto max-w-6xl px-4 py-12">
    <h1 class="mt-2 max-w-3xl text-5xl font-extrabold leading-[1.05]">Pilih layanan, lalu <span class="mark">ajukan pesanan</span> atau tanya dulu.</h1>
    <p class="mt-3 text-slate-600">Harga tidak dipasang tetap. Harga disepakati setelah Anda berkonsultasi lewat WhatsApp.</p>

    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($layanans as $layanan)
            @include('website.kartu-layanan')
        @endforeach

        <div class="card flex flex-col bg-lime p-5">
            <p class="eyebrow text-ink">Belum yakin?</p>
            <h3 class="mt-2 text-2xl font-extrabold">Tanya dulu lewat WhatsApp.</h3>
            <p class="mt-2 flex-1 text-sm">Konsultasikan kebutuhan Anda tanpa perlu membuat akun.</p>
            <a href="{{ \App\Services\WaLinkBuilder::buildAdminInquiry() }}" target="_blank" rel="noopener" class="btn-dark mt-4">Tanya via WhatsApp</a>
        </div>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 pb-12">
    <p class="eyebrow">Cara Memesan</p>
    <h2 class="mt-2 text-4xl font-extrabold">Dari klik sampai pengerjaan.</h2>
    <ol class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($caraMemesan as $nomor => [$judul, $isi])
            <li class="card p-5 {{ $loop->last ? 'bg-sun' : '' }}">
                <div class="font-display text-3xl font-extrabold {{ $loop->last ? '' : 'text-violet' }}">{{ $nomor + 1 }}</div>
                <div class="mt-3 font-bold">{{ $judul }}</div>
                <p class="mt-1 text-sm {{ $loop->last ? '' : 'text-slate-600' }}">{{ $isi }}</p>
            </li>
        @endforeach
    </ol>
</section>
@endsection
