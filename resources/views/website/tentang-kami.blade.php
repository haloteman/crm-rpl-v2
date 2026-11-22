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
            <p class="mt-4 max-w-xl text-slate-700">KAMI SIAP MELAYANI, BERDIRI SEJAK TAHUN 2000, BEBERAPA LAYANAN TELAH KAMI SEDIAKAN UNTUK ANDA</p>
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
        <div class="card p-5"><p class="eyebrow">Visi</p><p class="mt-2 font-semibold"></p>Maju lebih baik dan melangkah lebih depan</div>
        <div class="card bg-lime p-5"><p class="eyebrow text-ink">Misi</p><p class="mt-2 font-semibold"></p>Kuat dan bersinergi</div>
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
            @foreach ([
                ['nama' => 'Afif',    'peran' => 'Frontend Developer', 'warna' => 'bg-ink',    'foto' => 'afif.jpg'],
                ['nama' => 'Ibrahim', 'peran' => 'Team Leader',        'warna' => 'bg-lime',   'foto' => 'ibrahim.jpg'],
                ['nama' => 'Rendi',   'peran' => 'System Analyst',     'warna' => 'bg-violet', 'foto' => 'rendi.jpg'],
                ['nama' => 'Kiki',    'peran' => 'Backend Developer',  'warna' => 'bg-sun',    'foto' => 'kiki.jpg'],
            ] as $anggota)
                <div class="card p-3">
                    {{-- foto anggota, kalau file belum ada tampil kotak warna --}}
                    @if (file_exists(public_path('images/tim/'.$anggota['foto'])))
                        <img src="{{ asset('images/tim/'.$anggota['foto']) }}" alt="{{ $anggota['nama'] }}"
                             class="aspect-[4/3] w-full rounded-xl border-2 border-ink object-cover {{ $anggota['warna'] }}">
                    @else
                        <div class="aspect-[4/3] rounded-xl border-2 border-ink {{ $anggota['warna'] }}"></div>
                    @endif
                    <div class="mt-3 px-1 font-bold">{{ $anggota['nama'] }}</div>
                    <div class="px-1 pb-1 text-sm text-slate-600">{{ $anggota['peran'] }}</div>
                </div>
            @endforeach
        </div>
        </div>
        </div>
    </div>
</section>
@endsection
