@extends('layouts.app')
@section('title', 'Ubah Kontak Pelanggan')
@section('heading', 'Data Pelanggan')

@section('content')
<a href="{{ route('pelanggan.show', $pelanggan) }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-brand-dark hover:underline">
    <x-icon name="arrow-left" class="size-3.5" /> Kembali ke profil pelanggan
</a>

<h1 class="page-title mt-3">Ubah Kontak Pelanggan</h1>
<p class="page-sub">{{ $pelanggan->email }}</p>

<form method="POST" action="{{ route('pelanggan.update', $pelanggan) }}" class="card mt-5 max-w-2xl p-5 sm:p-6">
    @csrf

    <label for="name" class="block text-sm font-medium">Nama Lengkap</label>
    <input id="name" name="name" type="text" value="{{ old('name', $pelanggan->name) }}"
           class="field mt-1.5 @error('name') border-lost @enderror">
    @error('name') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

    <label for="phone" class="mt-4 block text-sm font-medium">Nomor HP</label>
    <input id="phone" name="phone" type="tel" value="{{ old('phone', $pelanggan->customerProfile->phone) }}"
           class="field mt-1.5 @error('phone') border-lost @enderror">
    @error('phone') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

    <label for="institution" class="mt-4 block text-sm font-medium">Instansi <span class="font-normal text-slate-500">(opsional)</span></label>
    <input id="institution" name="institution" type="text" value="{{ old('institution', $pelanggan->customerProfile->institution) }}"
           class="field mt-1.5 @error('institution') border-lost @enderror">
    @error('institution') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

    <div class="mt-6 flex flex-col-reverse gap-3 border-t pt-5 sm:flex-row sm:justify-end">
        <a href="{{ route('pelanggan.show', $pelanggan) }}" class="btn-outline">Batal</a>
        <button type="submit" class="btn-primary">Simpan</button>
    </div>
</form>
@endsection
