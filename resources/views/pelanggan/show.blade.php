@extends('layouts.app')
@section('title', 'Profil Pelanggan')
@section('heading', 'Data Pelanggan')

@section('content')
<a href="{{ route('profil.show') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-dark underline underline-offset-4 hover:text-ink">
    <x-icon name="arrow-left" class="size-3.5" /> Kembali ke pelanggan
</a>

<div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <p class="eyebrow">Pelanggan</p>
        <h1 class="page-title mt-1">Profil Pelanggan</h1>
        <p class="page-sub">Mendaftar pada {{ $pelanggan->customerProfile->registered_at->format('d/m/Y') }}</p>
    </div>
    @can('customer.manage')
        <a href="{{ route('pelanggan.edit', $pelanggan) }}" class="btn-outline">
            <x-icon name="pencil" class="size-4" /> Ubah Kontak
        </a>
    @endcan
</div>

<section class="card mt-5 max-w-2xl p-5 sm:p-6">
    <h2 class="text-lg font-semibold">{{ $pelanggan->name }}</h2>
    <dl class="mt-5 grid grid-cols-[120px_1fr] gap-y-3 border-t pt-5 text-[13px] sm:grid-cols-[140px_1fr]">
        <dt class="text-slate-500">Email</dt><dd class="break-words font-medium">{{ $pelanggan->email }}</dd>
        <dt class="text-slate-500">Nomor HP</dt><dd class="break-words font-medium">{{ $pelanggan->customerProfile->phone }}</dd>
        <dt class="text-slate-500">Instansi</dt><dd class="font-medium">{{ $pelanggan->customerProfile->institution ?? '—' }}</dd>
        <dt class="text-slate-500">Status</dt><dd class="font-medium">{{ ['lead' => 'Lead', 'new_customer' => 'New Customer', 'returning_customer' => 'Pelanggan Lama'][$pelanggan->customerProfile->status] }}</dd>
        <dt class="text-slate-500">Sumber</dt><dd class="font-medium">{{ ucfirst($pelanggan->customerProfile->lead_source) }}</dd>
    </dl>
</section>
@endsection
