@extends('layouts.website')
@section('title', $layanan->name)

@section('content')
<section class="border-b-2 border-ink">
    <div class="mx-auto max-w-6xl px-4 py-10">
        <nav class="text-sm font-semibold">
            <a href="{{ route('website.layanan') }}" class="underline underline-offset-4">Layanan</a> / {{ $layanan->name }}
        </nav>

        <div class="mt-8 grid items-center gap-10 md:grid-cols-2">
            <x-ikon-layanan :layanan="$layanan" class="size-72! rounded-3xl! shadow-hard! [&>svg]:size-28!" />

            <div>
                <p class="eyebrow">Layanan {{ str_pad($layanan->sort_order, 2, '0', STR_PAD_LEFT) }}</p>
                <h1 class="mt-2 text-5xl font-extrabold">{{ $layanan->name }}</h1>
                <p class="mt-4 text-slate-700">{{ $layanan->description ?? $layanan->short_description }}</p>

                @if ($layanan->workflow_steps)
                    <h2 class="mt-6 text-sm font-bold uppercase tracking-[0.12em]">Alur pengerjaan</h2>
                    <ol class="mt-2 space-y-2 text-sm">
                        @foreach ($layanan->workflow_steps as $langkah)
                            <li class="flex gap-2"><x-icon name="check" class="size-5 shrink-0 text-violet" /> {{ $langkah }}</li>
                        @endforeach
                    </ol>
                @endif

                <p class="mt-6 flex items-start gap-2 rounded-xl border-2 border-ink bg-warn-tint px-4 py-3 text-sm">
                    <x-icon name="info" class="mt-0.5 size-4 shrink-0" />
                    Harga disepakati setelah konsultasi lewat WhatsApp. Ketentuan revisi disepakati saat konsultasi.
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('pesanan.create', $layanan->slug) }}" class="btn-dark">Ajukan Pesanan</a>
                    <a href="{{ \App\Services\WaLinkBuilder::buildAdminInquiry($layanan) }}" target="_blank" rel="noopener" class="btn-outline">Tanya via WhatsApp</a>
                </div>
            </div>
        </div>
    </div>
</section>

@if ($layananLain->isNotEmpty())
    <section class="bg-sand">
        <div class="mx-auto max-w-6xl px-4 py-16">
            <p class="eyebrow">Layanan Lainnya</p>
            <h2 class="mt-2 text-4xl font-extrabold">Mungkin Anda juga butuh.</h2>
            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($layananLain as $layanan)
                    @include('website.kartu-layanan')
                @endforeach
            </div>
        </div>
    </section>
@endif
@endsection
