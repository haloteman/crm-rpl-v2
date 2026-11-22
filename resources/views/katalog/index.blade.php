@extends('layouts.app')
@section('title', 'Katalog Layanan')
@section('heading', 'Katalog Layanan')

@section('content')
<div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h1 class="page-title mt-1">Katalog layanan.</h1>
        <p class="page-sub">Layanan yang tampil di website. Urutan di sini sama dengan urutan di website.</p>
    </div>
    <a href="{{ route('katalog.create') }}" class="btn-primary w-full sm:w-auto">
        <x-icon name="plus" class="size-5" /> Tambah Layanan
    </a>
</div>

<section class="card mt-5 overflow-hidden">
    <table class="block w-full text-left text-sm md:table">
        <thead class="hidden bg-thead text-[11px] font-bold uppercase tracking-[0.1em] md:table-header-group">
            <tr>
                <th class="px-5 py-3 font-medium">Nama Layanan</th>
                <th class="px-5 py-3 font-medium">Deskripsi Singkat</th>
                <th class="px-5 py-3 font-medium">Tampil di Website</th>
                <th class="px-5 py-3 font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody class="block md:table-row-group">
            @forelse ($layanans as $layanan)
                <tr class="block border-t px-4 py-3 hover:bg-thead md:table-row md:p-0">
                    <td class="block pb-1 md:table-cell md:px-5 md:py-3">
                        <div class="flex items-center gap-3">
                            <x-ikon-layanan :layanan="$layanan" class="size-10! [&>svg]:size-5!" />
                            <div>
                                <div class="font-bold">{{ $layanan->name }}</div>
                                <div class="text-xs text-slate-500">Urutan {{ $layanan->sort_order }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="block py-1 text-slate-600 md:table-cell md:px-5 md:py-3">{{ $layanan->short_description }}</td>
                    <td class="block py-1 md:table-cell md:px-5 md:py-3">
                        <span class="badge {{ $layanan->is_active ? 'badge-green' : 'badge-gray' }}">{{ $layanan->is_active ? 'Tampil' : 'Disembunyikan' }}</span>
                    </td>
                    <td class="block py-1 md:table-cell md:px-5 md:py-3">
                        <a href="{{ route('katalog.edit', $layanan) }}" class="btn-outline px-3 py-1.5 text-xs">Ubah</a>
                    </td>
                </tr>
            @empty
                <tr class="block border-t md:table-row">
                    <td colspan="4" class="block px-5 py-8 text-center text-slate-500 md:table-cell">Belum ada layanan</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</section>
@endsection
