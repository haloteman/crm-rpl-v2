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
    <p class="eyebrow">Layanan</p>
    <h1 class="mt-2 text-3xl font-bold">Pilih layanan, lalu ajukan pesanan atau tanya dulu.</h1>
    <p class="mt-2 text-slate-600">Harga tidak dipasang tetap. Harga disepakati setelah Anda berkonsultasi lewat WhatsApp.</p>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($layanans as $layanan)
            @include('website.kartu-layanan')
        @endforeach

        <div class="card flex flex-col bg-tint p-5">
            <p class="eyebrow">Belum yakin?</p>
            <h3 class="mt-1 font-semibold">Tanya dulu lewat WhatsApp.</h3>
            <p class="mt-1 flex-1 text-sm text-slate-500">Konsultasikan kebutuhan Anda tanpa perlu membuat akun.</p>
            <a href="{{ \App\Services\WaLinkBuilder::buildAdminInquiry() }}" target="_blank" rel="noopener" class="btn-primary mt-4">Tanya via WhatsApp</a>
        </div>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 pb-12">
    <p class="eyebrow">Cara Memesan</p>
    <h2 class="mt-2 text-2xl font-bold">Dari klik sampai pengerjaan.</h2>
    <ol class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($caraMemesan as $nomor => [$judul, $isi])
            <li class="card p-5">
                <div class="font-bold">{{ $nomor + 1 }}. {{ $judul }}</div>
                <p class="mt-1 text-sm text-slate-500">{{ $isi }}</p>
            </li>
        @endforeach
    </ol>
</section>
@endsection
