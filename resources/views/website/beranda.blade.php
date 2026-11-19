@extends('layouts.website')
@section('title', 'Beranda')

@php
    $langkah = [
        ['Daftar dan ajukan', 'Buat akun, pilih layanan, lalu tulis kebutuhan.'],
        ['Konsultasi', 'Bahas kebutuhan lewat WhatsApp hingga harga disepakati.'],
        ['Bayar DP dan pantau', 'Pengerjaan dimulai setelah DP, progres bisa dilacak.'],
        ['Terima dan beri rating', 'Terima hasil akhir, lalu nilai pelayanan kami.'],
    ];

    $keunggulan = [
        ['Dikerjakan sesuai keahlian', 'Setiap proyek dibagikan kepada desainer yang paling sesuai.'],
        ['Progres bisa dipantau', 'Lihat tahap pengerjaan langsung dari akun Anda.'],
        ['Konsultasi dulu lewat WhatsApp', 'Kebutuhan dan harga dibahas sebelum pengerjaan dimulai.'],
    ];
@endphp

@section('content')
<section class="mx-auto grid max-w-6xl items-center gap-10 px-4 py-16 lg:grid-cols-[1.3fr_1fr]">
    <div>
        <span class="inline-block rounded-full border-2 border-ink bg-white px-3 py-1 text-xs font-bold">PT Media Creative · Jasa Desain dan Media Kreatif</span>
        <h1 class="mt-6 text-5xl font-extrabold leading-[1.02] sm:text-6xl">Mitra desain dan media kreatif untuk <span class="mark">usaha, komunitas</span>, dan instansi.</h1>
        <p class="mt-6 max-w-xl text-slate-700">Kami mengerjakan desain logo, kaos, editing video, foto, poster, flyer, dan banner oleh desainer sesuai keahliannya. Harga disepakati setelah konsultasi lewat WhatsApp.</p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('website.layanan') }}" class="btn-dark">Lihat Layanan →</a>
            <a href="{{ \App\Services\WaLinkBuilder::buildAdminInquiry() }}" target="_blank" rel="noopener" class="btn-outline">Tanya via WhatsApp</a>
        </div>
    </div>

    {{-- dekorasi --}}
    <div aria-hidden="true" class="relative hidden h-80 lg:block">
        <div class="absolute right-0 top-0 size-60 rounded-full border-2 border-ink bg-lime"></div>
        <div class="absolute right-28 top-4 flex size-20 -rotate-6 items-center justify-center rounded-xl border-2 border-ink bg-sand shadow-hard"><x-icon name="camera" class="size-9" /></div>
        <div class="absolute right-14 top-24 flex size-24 rotate-3 items-center justify-center rounded-xl border-2 border-ink bg-ink text-lime shadow-[4px_4px_0_0_var(--color-sun)]"><x-icon name="shapes" class="size-10" /></div>
        <div class="absolute bottom-6 left-6 size-28 -rotate-6 rounded-xl border-2 border-ink bg-violet shadow-hard"></div>
        <div class="absolute bottom-0 right-0 flex size-20 rotate-6 items-center justify-center rounded-xl border-2 border-ink bg-sun shadow-hard"><x-icon name="shirt" class="size-9" /></div>
        <div class="absolute left-0 top-36 -rotate-3 rounded-lg border-2 border-ink bg-white px-3 py-1.5 text-xs font-bold shadow-hard-sm">Logo · Kaos · Video · Foto</div>
    </div>
</section>

<section class="border-y-2 border-ink bg-sand">
    <div class="mx-auto grid max-w-6xl items-center gap-10 px-4 py-16 lg:grid-cols-2">
        <div>
            <p class="eyebrow">Tentang Kami</p>
            <h2 class="mt-2 text-4xl font-extrabold">Dari ide sederhana menjadi karya yang rapi.</h2>
            <p class="mt-3 text-slate-700">[PROFIL SINGKAT PT MEDIA CREATIVE]</p>
            <a href="{{ route('website.tentang-kami') }}" class="btn-outline mt-6">Selengkapnya →</a>
        </div>
        <ol class="space-y-4">
            @foreach ($keunggulan as $nomor => [$judul, $isi])
                <li class="card flex items-center gap-4 p-4 {{ $nomor === 1 ? 'lg:ml-6' : '' }}">
                    <span class="font-display text-3xl font-extrabold text-violet">{{ str_pad($nomor + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <div>
                        <div class="font-bold">{{ $judul }}</div>
                        <p class="text-sm text-slate-600">{{ $isi }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-16">
    <p class="eyebrow">Layanan</p>
    <h2 class="mt-2 text-4xl font-extrabold">Pilih layanan yang Anda butuhkan.</h2>
    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($layanans as $layanan)
            @include('website.kartu-layanan')
        @endforeach
    </div>
    <a href="{{ route('website.layanan') }}" class="mt-6 inline-block text-sm font-bold underline underline-offset-4">Lihat semua layanan →</a>
</section>

<section class="border-y-2 border-ink bg-ink text-white">
    <div class="mx-auto max-w-6xl px-4 py-16">
        <p class="eyebrow text-lime">Cara Kerja</p>
        <h2 class="mt-2 max-w-xl text-4xl font-extrabold">Empat langkah dari ide sampai hasil akhir.</h2>
        <ol class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($langkah as $nomor => [$judul, $isi])
                <li>
                    <span class="flex size-9 items-center justify-center rounded-full border-2 border-ink bg-lime font-bold text-ink">{{ $nomor + 1 }}</span>
                    <div class="mt-4 font-bold">{{ $judul }}</div>
                    <p class="mt-1 text-sm text-white/70">{{ $isi }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>

@include('website.cta')
@endsection
