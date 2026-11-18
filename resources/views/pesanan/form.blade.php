@extends('layouts.app')
@section('title', 'Ajukan Pesanan')
@section('heading', 'Ajukan Pesanan')

@section('content')
<a href="{{ route('website.layanan.show', $layanan->slug) }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-brand-dark hover:underline">
    <x-icon name="arrow-left" class="size-3.5" /> Kembali ke layanan
</a>

<h1 class="page-title mt-3">Ajukan Pesanan {{ $layanan->name }}</h1>
<p class="page-sub">Harga akhir ditetapkan Owner setelah konsultasi lewat WhatsApp.</p>

<form method="POST" action="{{ route('pesanan.store', $layanan->slug) }}" enctype="multipart/form-data" class="card mt-5 max-w-2xl p-5 sm:p-6">
    @csrf

    <div class="grid gap-4 rounded-lg bg-thead p-4 text-sm sm:grid-cols-2">
        <div>
            <div class="text-xs text-slate-500">Nama</div>
            <div class="font-medium">{{ $user->name }}</div>
        </div>
        <div>
            <div class="text-xs text-slate-500">Nomor HP</div>
            <div class="font-medium">{{ $user->customerProfile->phone }}</div>
        </div>
        <a href="{{ route('profil.edit') }}" class="text-xs font-medium text-brand-dark hover:underline sm:col-span-2">Ubah data diri di profil</a>
    </div>

    <label for="title" class="mt-5 block text-sm font-medium">Judul Proyek</label>
    <input id="title" name="title" type="text" value="{{ old('title') }}" class="field mt-1.5 @error('title') border-lost @enderror">
    @error('title') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

    <label for="description" class="mt-4 block text-sm font-medium">Deskripsi Kebutuhan</label>
    <textarea id="description" name="description" rows="5" class="field mt-1.5 @error('description') border-lost @enderror">{{ old('description') }}</textarea>
    @error('description') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

    <label for="referensi_file" class="mt-4 block text-sm font-medium">File Referensi <span class="font-normal text-slate-500">(opsional, JPG/PNG/PDF maks. 10 MB)</span></label>
    <input id="referensi_file" name="referensi_file" type="file" accept=".jpg,.jpeg,.png,.pdf" class="field mt-1.5 @error('referensi_file') border-lost @enderror">
    @error('referensi_file') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

    <label for="referensi_link" class="mt-4 block text-sm font-medium">Tautan Referensi <span class="font-normal text-slate-500">(opsional)</span></label>
    <input id="referensi_link" name="referensi_link" type="url" value="{{ old('referensi_link') }}" placeholder="https://" class="field mt-1.5 @error('referensi_link') border-lost @enderror">
    @error('referensi_link') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

    <div class="mt-4 grid gap-4 sm:grid-cols-2">
        <div>
            <label for="expected_deadline" class="block text-sm font-medium">Tenggat yang Diharapkan</label>
            <input id="expected_deadline" name="expected_deadline" type="date" value="{{ old('expected_deadline') }}" class="field mt-1.5 @error('expected_deadline') border-lost @enderror">
            @error('expected_deadline') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="budget_estimate" class="block text-sm font-medium">Perkiraan Anggaran <span class="font-normal text-slate-500">(opsional)</span></label>
            <input id="budget_estimate" name="budget_estimate" type="number" min="0" value="{{ old('budget_estimate') }}" class="field mt-1.5 @error('budget_estimate') border-lost @enderror">
            @error('budget_estimate')
                <p class="mt-1 text-sm text-lost">{{ $message }}</p>
            @else
                <p class="mt-1 text-xs text-slate-500">Bukan harga final.</p>
            @enderror
        </div>
    </div>

    <div class="mt-6 flex flex-col-reverse gap-3 border-t pt-5 sm:flex-row sm:justify-end">
        <a href="{{ route('website.layanan.show', $layanan->slug) }}" class="btn-outline">Batal</a>
        <button type="submit" class="btn-primary">Ajukan Pesanan</button>
    </div>
</form>
@endsection
