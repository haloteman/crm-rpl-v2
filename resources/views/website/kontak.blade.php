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
                <div class="py-4"><dt class="eyebrow">Email</dt><dd class="mt-1 font-semibold">EMAIL PERUSAHAAN</dd></div>
                <div class="py-4"><dt class="eyebrow">Alamat</dt><dd class="mt-1 font-semibold">ALAMAT PERUSAHAAN</dd></div>
                <div class="py-4"><dt class="eyebrow">Jam Layanan</dt><dd class="mt-1 font-semibold">JAM OPERASIONAL</dd></div>
            </dl>
        </div>

        {{-- peta lokasi, isi $lokasiPeta dengan alamat seperti yang diketik di Google Maps --}}
        @php $lokasiPeta = 'Nama tempat atau alamat lengkap, Kota'; @endphp
        <div class="card relative min-h-72 overflow-hidden">
            <iframe title="Peta lokasi perusahaan"
                    src="https://www.google.com/maps?q={{ urlencode($lokasiPeta) }}&output=embed"
                    class="absolute inset-0 size-full border-0"
                    loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
        </div>
    </div>
</section>

@include('website.cta')
@endsection