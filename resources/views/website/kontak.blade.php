@extends('layouts.website')
@section('title', 'Kontak')

@section('content')
<section class="mx-auto max-w-6xl px-4 pt-16">
    <h1 class="mt-2 text-5xl font-extrabold">Ada yang ingin <span class="mark">ditanyakan?</span></h1>
    <p class="mt-3 text-slate-600">Hubungi kami lewat WhatsApp tanpa perlu membuat akun.</p>

    <div class="mt-8 grid gap-5 md:grid-cols-2">
        <div class="space-y-5">
            <div class="card bg-lime p-5">
                <p class="eyebrow text-ink">WhatsApp Admin</p>
                <div class="mt-2 font-display text-2xl font-extrabold">+{{ $waAdmin }}</div>
                <p class="mt-2 text-sm">Pertanyaan umum dijawab Admin. Setelah Anda mengajukan pesanan, konsultasi dilanjutkan dengan Owner.</p>
                <a href="{{ \App\Services\WaLinkBuilder::buildAdminInquiry() }}" target="_blank" rel="noopener" class="btn-dark mt-4">Chat via WhatsApp</a>
            </div>
            <dl class="card divide-y-2 divide-line px-5 text-sm">
                <div class="py-4"><dt class="eyebrow">Email</dt><dd class="mt-1 font-semibold">[EMAIL PERUSAHAAN]</dd></div>
                <div class="py-4"><dt class="eyebrow">Alamat</dt><dd class="mt-1 font-semibold">[ALAMAT PERUSAHAAN]</dd></div>
                <div class="py-4"><dt class="eyebrow">Jam Layanan</dt><dd class="mt-1 font-semibold">[JAM OPERASIONAL]</dd></div>
            </dl>
        </div>
        <div class="card flex min-h-72 items-center justify-center bg-sand bg-[repeating-linear-gradient(45deg,transparent_0_12px,rgb(18_24_38/0.08)_12px_13px)]">
            <span class="rounded-lg border-2 border-ink bg-white px-3 py-1.5 text-xs font-bold shadow-hard-sm">[PETA LOKASI]</span>
        </div>
    </div>
</section>

@include('website.cta')
@endsection
