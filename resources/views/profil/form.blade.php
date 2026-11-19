@extends('layouts.app')
@section('title', 'Ubah Profil')
@section('heading', 'Profil')

@section('content')
<a href="{{ route('profil.show') }}" class="inline-flex items-center gap-1.5 text-sm font-bold underline underline-offset-4">
    <x-icon name="arrow-left" class="size-3.5" /> Kembali ke profil
</a>

<p class="eyebrow mt-3">Pengaturan</p>

<h1 class="page-title mt-1">Ubah Profil</h1>
<p class="page-sub">{{ $user->email }}</p>

<form method="POST" action="{{ route('profil.update') }}" class="card mt-5 max-w-2xl p-5 sm:p-6">
    @csrf

    <label for="name" class="block text-sm font-medium">Nama Lengkap</label>
    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
           class="field mt-1.5 @error('name') border-lost @enderror">
    @error('name') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

    <label for="phone" class="mt-4 block text-sm font-medium">Nomor HP</label>
    <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->customerProfile->phone) }}"
           class="field mt-1.5 @error('phone') border-lost @enderror">
    @error('phone') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

    <label for="institution" class="mt-4 block text-sm font-medium">Instansi <span class="font-normal text-slate-500">(opsional)</span></label>
    <input id="institution" name="institution" type="text" value="{{ old('institution', $user->customerProfile->institution) }}"
           class="field mt-1.5 @error('institution') border-lost @enderror">
    @error('institution') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

    <div class="mt-6 flex flex-col-reverse gap-3 border-t pt-5 sm:flex-row sm:justify-end">
        <a href="{{ route('profil.show') }}" class="btn-outline">Batal</a>
        <button type="submit" class="btn-primary">Simpan</button>
    </div>
</form>
@endsection
