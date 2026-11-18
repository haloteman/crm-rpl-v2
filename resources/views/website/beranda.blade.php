@extends('layouts.website')
@section('title', 'Beranda')

@php
    $langkah = [
        ['Daftar dan ajukan', 'Buat akun, pilih layanan, lalu tulis kebutuhan.'],
        ['Konsultasi', 'Bahas kebutuhan lewat WhatsApp hingga harga disepakati.'],
        ['Bayar DP dan pantau', 'Pengerjaan dimulai setelah DP, progres bisa dilacak.'],
        ['Terima dan beri rating', 'Terima hasil akhir, lalu nilai pelayanan kami.'],
    ];
@endphp

@section('content')
<section class="mx-auto max-w-6xl px-4 py-14">
    <p class="eyebrow">PT Media Creative · Jasa Desain dan Media Kreatif</p>
    <h1 class="mt-3 max-w-3xl text-4xl font-bold leading-tight">Mitra desain dan media kreatif untuk usaha, komunitas, dan instansi.</h1>
    <p class="mt-4 max-w-2xl text-slate-600">Kami mengerjakan desain logo, kaos, editing video, foto, poster, flyer, dan banner oleh desainer sesuai keahliannya. Harga disepakati setelah konsultasi lewat WhatsApp.</p>
    <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ route('website.layanan') }}" class="btn-primary">Lihat Layanan →</a>
        <a href="{{ \App\Services\WaLinkBuilder::buildAdminInquiry() }}" target="_blank" rel="noopener" class="btn-outline">Tanya via WhatsApp</a>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-10">
    <p class="eyebrow">Tentang Kami</p>
    <h2 class="mt-2 text-2xl font-bold">Dari ide sederhana menjadi karya yang rapi.</h2>
    <p class="mt-2 text-slate-600">[PROFIL SINGKAT PT MEDIA CREATIVE]</p>
    <a href="{{ route('website.tentang-kami') }}" class="btn-outline mt-4">Selengkapnya →</a>
</section>

<section class="mx-auto max-w-6xl px-4 py-10">
    <p class="eyebrow">Layanan</p>
    <h2 class="mt-2 text-2xl font-bold">Pilih layanan yang Anda butuhkan.</h2>
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($layanans as $layanan)
            @include('website.kartu-layanan')
        @endforeach
    </div>
    <a href="{{ route('website.layanan') }}" class="mt-4 inline-block text-sm font-medium text-brand-dark hover:underline">Lihat semua layanan →</a>
</section>

<section class="mx-auto max-w-6xl px-4 py-10">
    <p class="eyebrow">Cara Kerja</p>
    <h2 class="mt-2 text-2xl font-bold">Empat langkah dari ide sampai hasil akhir.</h2>
    <ol class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($langkah as $nomor => [$judul, $isi])
            <li class="card p-5">
                <div class="font-bold">{{ $nomor + 1 }}. {{ $judul }}</div>
                <p class="mt-1 text-sm text-slate-500">{{ $isi }}</p>
            </li>
        @endforeach
    </ol>
</section>

<section class="mx-auto max-w-6xl px-4 py-10">
    <div class="card flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold">Siap mulai proyek Anda?</h2>
            <p class="text-sm text-slate-500">Pilih layanan lalu ajukan pesanan, atau tanya dulu lewat WhatsApp.</p>
        </div>
        <a href="{{ route('website.layanan') }}" class="btn-primary">Lihat Layanan →</a>
    </div>
</section>
@endsection
