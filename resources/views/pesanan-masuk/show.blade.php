@extends('layouts.app')
@section('title', $order->order_code)
@section('heading', 'Pesanan')

@section('content')
<a href="{{ route('pesanan-masuk.index') }}" class="inline-flex items-center gap-1.5 text-sm font-bold underline underline-offset-4">
    <x-icon name="arrow-left" class="size-3.5" /> Kembali ke pesanan
</a>

<p class="eyebrow mt-3">Detail Pesanan</p>
<h1 class="page-title">{{ $order->title }}</h1>
<p class="page-sub">
    <span class="font-mono">{{ $order->order_code }}</span> ·
    <span class="badge badge-blue">{{ $order->status->label }}</span> ·
    Diajukan {{ $order->created_at->translatedFormat('d M Y') }}
</p>

{{-- tahapan status pesanan --}}
<ol class="card mt-5 grid grid-cols-3 gap-2 p-4 text-xs sm:grid-cols-6">
    @foreach ($semuaStatus as $status)
        <li class="border-t-4 pt-2 {{ $status->sort_order <= $order->status->sort_order && $order->status->code !== 'dibatalkan' ? 'border-brand-dark font-semibold' : 'border-line text-slate-500' }}">
            {{ $status->label }}
        </li>
    @endforeach
</ol>

<div class="mt-5 grid items-start gap-5 lg:grid-cols-[1fr_320px]">
    <div class="space-y-5">
        <section class="card p-5">
            <h2 class="font-semibold">Customer</h2>
            <div class="mt-2 font-medium">{{ $order->contact_name }}</div>
            <div class="text-xs text-slate-500">
                {{ $order->customer->customerProfile->institution ?? 'Perorangan' }} · {{ ucfirst($order->customer->customerProfile->status) }}
            </div>
            <dl class="mt-3 grid grid-cols-[100px_1fr] gap-y-1 text-sm">
                <dt class="text-slate-500">Nomor HP</dt><dd>{{ $order->contact_phone }}</dd>
                <dt class="text-slate-500">Email</dt><dd>{{ $order->customer->email }}</dd>
            </dl>

            @role('Owner')
                @php $linkWa = \App\Services\WaLinkBuilder::buildCustomerContact($order, auth()->user()->name); @endphp
                <a href="{{ $linkWa }}" target="_blank" rel="noopener" class="btn-primary mt-4">Hubungi Customer via WhatsApp</a>
            @endrole
        </section>

        <section class="card p-5">
            <h2 class="font-semibold">Kebutuhan</h2>
            <dl class="mt-3 grid grid-cols-[140px_1fr] gap-y-2 text-sm">
                <dt class="text-slate-500">Layanan</dt><dd>{{ $order->service->name }}</dd>
                <dt class="text-slate-500">Tenggat diharapkan</dt><dd>{{ $order->expected_deadline->translatedFormat('d M Y') }}</dd>
                <dt class="text-slate-500">Perkiraan anggaran</dt>
                <dd>{{ $order->budget_estimate ? 'Rp'.number_format($order->budget_estimate, 0, ',', '.').' (perkiraan, bukan harga final)' : '—' }}</dd>
            </dl>
            <p class="mt-3 whitespace-pre-line text-sm">{{ $order->description }}</p>
            @include('pesanan.referensi')
        </section>
    </div>

    <div class="space-y-5">
        <section class="card p-5">
            <h2 class="font-semibold">Kesepakatan konsultasi</h2>

            @if (auth()->user()->can('consult.note') && $order->bolehCatatKonsultasi())
                <form method="POST" action="{{ route('konsultasi.simpan', $order) }}" class="mt-3">
                    @csrf
                    <label for="notes" class="block text-sm font-medium">Hasil konsultasi</label>
                    <textarea id="notes" name="notes" rows="4" placeholder="Lingkup pekerjaan yang disepakati"
                              class="field mt-1.5 @error('notes') border-lost @enderror">{{ old('notes', $order->consultation?->notes) }}</textarea>
                    @error('notes') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

                    <label for="revision_terms" class="mt-4 block text-sm font-medium">Ketentuan revisi</label>
                    <textarea id="revision_terms" name="revision_terms" rows="3" class="field mt-1.5">{{ old('revision_terms', $order->consultation?->revision_terms) }}</textarea>

                    <button type="submit" class="btn-primary mt-4 w-full">Simpan Catatan</button>
                    <p class="mt-2 text-xs text-slate-500">Boleh disimpan dulu. Status tetap Menunggu Konsultasi.</p>
                </form>
            @else
                <p class="mt-1 text-xs text-slate-500">Diisi Owner · hanya-baca</p>
                <h3 class="mt-3 text-sm font-medium">Hasil konsultasi</h3>
                <p class="mt-1 whitespace-pre-line rounded-lg bg-thead px-3 py-2 text-sm">{{ $order->consultation?->notes ?? 'Belum diisi Owner.' }}</p>
                <h3 class="mt-3 text-sm font-medium">Ketentuan revisi</h3>
                <p class="mt-1 whitespace-pre-line rounded-lg bg-thead px-3 py-2 text-sm">{{ $order->consultation?->revision_terms ?? 'Belum diisi Owner.' }}</p>
            @endif
        </section>

        @include('pesanan.riwayat')
    </div>
</div>
@endsection
