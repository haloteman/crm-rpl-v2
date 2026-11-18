@extends('layouts.app')
@section('title', 'Profil')
@section('heading', 'Profil')

@section('content')
<div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h1 class="page-title">Profil Saya</h1>
        <p class="page-sub">Pastikan data Anda selalu benar agar kami mudah menghubungi Anda.</p>
    </div>
    <a href="{{ route('profil.edit') }}" class="btn-outline">
        <x-icon name="pencil" class="size-4" /> Ubah Profil
    </a>
</div>

<section class="card mt-5 max-w-2xl p-5 sm:p-6">
    <h2 class="text-lg font-semibold">{{ $user->name }}</h2>
    <dl class="mt-5 grid grid-cols-[120px_1fr] gap-y-3 border-t pt-5 text-[13px] sm:grid-cols-[140px_1fr]">
        <dt class="text-slate-500">Email</dt><dd class="break-words font-medium">{{ $user->email }}</dd>
        <dt class="text-slate-500">Nomor HP</dt><dd class="break-words font-medium">{{ $user->customerProfile->phone }}</dd>
        <dt class="text-slate-500">Instansi</dt><dd class="font-medium">{{ $user->customerProfile->institution ?? '—' }}</dd>
    </dl>
</section>
@endsection
