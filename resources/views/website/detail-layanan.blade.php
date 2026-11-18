@extends('layouts.website')
@section('title', $layanan->name)

@section('content')
<section class="mx-auto max-w-6xl px-4 py-12">
    <nav class="text-sm text-slate-500">
        <a href="{{ route('website.layanan') }}" class="hover:underline">Layanan</a> / {{ $layanan->name }}
    </nav>

    <p class="eyebrow mt-6">Layanan {{ str_pad($layanan->sort_order, 2, '0', STR_PAD_LEFT) }}</p>
    <h1 class="mt-2 text-3xl font-bold">{{ $layanan->name }}</h1>
    <p class="mt-3 max-w-2xl text-slate-600">{{ $layanan->description ?? $layanan->short_description }}</p>

    @if ($layanan->workflow_steps)
        <h2 class="mt-6 font-semibold">Alur pengerjaan</h2>
        <ol class="mt-2 list-decimal space-y-1 pl-5 text-sm text-slate-600">
            @foreach ($layanan->workflow_steps as $langkah)
                <li>{{ $langkah }}</li>
            @endforeach
        </ol>
    @endif

    <p class="mt-6 rounded-lg bg-warn-tint px-4 py-3 text-sm text-warn">Harga disepakati setelah konsultasi lewat WhatsApp. Ketentuan revisi disepakati saat konsultasi.</p>

    <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ \App\Services\WaLinkBuilder::buildAdminInquiry($layanan) }}" target="_blank" rel="noopener" class="btn-outline">Tanya via WhatsApp</a>
    </div>
</section>

@if ($layananLain->isNotEmpty())
    <section class="mx-auto max-w-6xl px-4 pb-12">
        <p class="eyebrow">Layanan Lainnya</p>
        <h2 class="mt-2 text-2xl font-bold">Mungkin Anda juga butuh.</h2>
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($layananLain as $layanan)
                @include('website.kartu-layanan')
            @endforeach
        </div>
    </section>
@endif
@endsection
