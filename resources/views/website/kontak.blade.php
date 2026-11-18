@extends('layouts.website')
@section('title', 'Kontak')

@section('content')
<section class="mx-auto max-w-6xl px-4 py-12">
    <p class="eyebrow">Kontak</p>
    <h1 class="mt-2 text-3xl font-bold">Ada yang ingin ditanyakan?</h1>
    <p class="mt-2 text-slate-600">Hubungi kami lewat WhatsApp tanpa perlu membuat akun.</p>

    <div class="mt-8 grid gap-4 md:grid-cols-2">
        <div class="space-y-4">
            <div class="card p-5">
                <p class="eyebrow">WhatsApp Admin</p>
                <div class="mt-1 text-xl font-bold">+{{ $waAdmin }}</div>
                <p class="mt-1 text-sm text-slate-500">Pertanyaan umum dijawab Admin. Setelah Anda mengajukan pesanan, konsultasi dilanjutkan dengan Owner.</p>
                <a href="{{ \App\Services\WaLinkBuilder::buildAdminInquiry() }}" target="_blank" rel="noopener" class="btn-primary mt-4">Chat via WhatsApp</a>
            </div>
            <dl class="card space-y-3 p-5 text-sm">
                <div><dt class="eyebrow">Email</dt><dd>[EMAIL PERUSAHAAN]</dd></div>
                <div><dt class="eyebrow">Alamat</dt><dd>[ALAMAT PERUSAHAAN]</dd></div>
                <div><dt class="eyebrow">Jam Layanan</dt><dd>[JAM OPERASIONAL]</dd></div>
            </dl>
        </div>
        <div class="card flex min-h-60 items-center justify-center text-sm text-slate-500">[PETA LOKASI]</div>
    </div>
</section>
@endsection
