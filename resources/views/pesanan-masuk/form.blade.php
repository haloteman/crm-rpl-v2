@extends('layouts.app')
@section('title', 'Buat Pesanan atas Nama')
@section('heading', 'Pesanan')

@php
    $daftarMinggu = collect($kapasitasMinggu)->map(fn ($minggu) => [
        'awal' => $minggu['awal_minggu']->toDateString(),
        'akhir' => $minggu['akhir_minggu']->toDateString(),
        'status' => $minggu['status'],
        'label' => $minggu['awal_minggu']->translatedFormat('d M').'–'.$minggu['akhir_minggu']->translatedFormat('d M'),
    ]);
@endphp

@section('content')
<p class="text-sm">
    <a href="{{ route('pesanan-masuk.index') }}" class="font-bold text-violet underline underline-offset-4">Pesanan</a> / Buat pesanan
</p>

<p class="eyebrow mt-4">Pesanan atas nama pelanggan</p>
<h1 class="page-title">Buat pesanan.</h1>
<p class="page-sub">Untuk pelanggan terdaftar yang memesan lewat WhatsApp atau datang langsung. Pesanan ditandai "Dibuat Admin".</p>

<form method="POST" action="{{ route('pesanan-masuk.store') }}" class="card mt-5 max-w-2xl p-5 sm:p-6">
    @csrf

    <label for="customer_id" class="block text-sm font-medium">Pelanggan</label>
    <select id="customer_id" name="customer_id" class="field mt-1.5">
        <option value="">Pilih pelanggan</option>
        @foreach ($pelanggan as $orang)
            <option value="{{ $orang->id }}" @selected(old('customer_id') == $orang->id)>
                {{ $orang->name }} · {{ $orang->customerProfile->phone }}
            </option>
        @endforeach
    </select>
    @error('customer_id') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

    <label for="service_id" class="mt-4 block text-sm font-medium">Layanan</label>
    <select id="service_id" name="service_id" class="field mt-1.5">
        <option value="">Pilih layanan</option>
        @foreach ($semuaLayanan as $layanan)
            <option value="{{ $layanan->id }}" @selected(old('service_id') == $layanan->id)>{{ $layanan->name }}</option>
        @endforeach
    </select>
    @error('service_id') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

    <label for="title" class="mt-4 block text-sm font-medium">Judul Proyek</label>
    <input id="title" name="title" type="text" value="{{ old('title') }}" class="field mt-1.5">
    @error('title') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

    <label for="description" class="mt-4 block text-sm font-medium">Deskripsi Kebutuhan</label>
    <textarea id="description" name="description" rows="5" class="field mt-1.5">{{ old('description') }}</textarea>
    @error('description') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

    <div class="mt-4 grid gap-4 sm:grid-cols-2"
         x-data="{ tenggat: '{{ old('expected_deadline') }}', minggu: {{ Js::from($daftarMinggu) }},
                   get pilihan() { return this.minggu.find(m => this.tenggat >= m.awal && this.tenggat <= m.akhir) } }">
        <div>
            <label for="expected_deadline" class="block text-sm font-medium">Tenggat yang Diharapkan</label>
            <input id="expected_deadline" name="expected_deadline" type="date" x-model="tenggat" class="field mt-1.5">
            @error('expected_deadline') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror
            <p x-show="pilihan && pilihan.status !== 'available'" x-cloak class="mt-2 rounded-xl border-2 border-ink bg-warn-tint px-3 py-2 text-xs">
                Minggu <span x-text="pilihan?.label"></span> <b x-text="pilihan?.status === 'full' ? 'sudah penuh' : 'hampir penuh'"></b>.
            </p>
        </div>
        <div>
            <label for="budget_estimate" class="block text-sm font-medium">Perkiraan Anggaran <span class="font-normal text-ink/60">(opsional)</span></label>
            <input id="budget_estimate" name="budget_estimate" type="number" min="0" value="{{ old('budget_estimate') }}" class="field mt-1.5">
            @error('budget_estimate') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="mt-6 flex flex-col-reverse gap-3 border-t-2 border-line pt-5 sm:flex-row sm:justify-end">
        <a href="{{ route('pesanan-masuk.index') }}" class="btn-outline">Batal</a>
        <button type="submit" class="btn-primary">Buat Pesanan</button>
    </div>
</form>
@endsection
