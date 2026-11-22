@extends('layouts.app')
@section('title', $layanan->exists ? 'Ubah Layanan' : 'Tambah Layanan')
@section('heading', 'Katalog Layanan')

@section('content')
<a href="{{ route('profil.show') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-dark underline underline-offset-4 hover:text-ink">
    <x-icon name="arrow-left" class="size-3.5" /> Kembali ke katalog layanan
</a>

<h1 class="page-title mt-1">{{ $layanan->exists ? 'Ubah Layanan' : 'Tambah Layanan' }}</h1>

<form method="POST" action="{{ $layanan->exists ? route('katalog.update', $layanan) : route('katalog.store') }}" class="card mt-5 max-w-2xl p-5 sm:p-6">
    @csrf

    <label for="name" class="block text-sm font-medium">Nama Layanan</label>
    <input id="name" name="name" type="text" value="{{ old('name', $layanan->name) }}"
           class="field mt-1.5 @error('name') border-lost @enderror">
    @error('name') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

    <label for="short_description" class="mt-4 block text-sm font-medium">Deskripsi Singkat</label>
    <input id="short_description" name="short_description" type="text" value="{{ old('short_description', $layanan->short_description) }}"
           class="field mt-1.5 @error('short_description') border-lost @enderror">
    @error('short_description') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

    <label for="description" class="mt-4 block text-sm font-medium">Deskripsi Lengkap <span class="font-normal text-slate-500">(opsional)</span></label>
    <textarea id="description" name="description" rows="4" class="field mt-1.5">{{ old('description', $layanan->description) }}</textarea>

    <label for="workflow_steps" class="mt-4 block text-sm font-medium">Alur Pengerjaan <span class="font-normal text-slate-500">(satu langkah per baris)</span></label>
    <textarea id="workflow_steps" name="workflow_steps" rows="5" class="field mt-1.5">{{ old('workflow_steps', implode("\n", $layanan->workflow_steps ?? [])) }}</textarea>

    <label class="mt-4 flex cursor-pointer items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $layanan->is_active))
               class="size-4 rounded border-slate-300 accent-brand-dark">
        Tampilkan di website
    </label>

    <div class="mt-6 flex flex-col-reverse gap-3 border-t pt-5 sm:flex-row sm:justify-end">
        <a href="{{ route('katalog.index') }}" class="btn-outline">Batal</a>
        <button type="submit" class="btn-primary">Simpan</button>
    </div>
</form>
@endsection
