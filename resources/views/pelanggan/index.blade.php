@extends('layouts.app')
@section('title', 'Data Pelanggan')
@section('heading', 'Data Pelanggan')

@section('content')
<h1 class="page-title mt-1">Data pelanggan.</h1>
<p class="page-sub">Pelanggan dan lead yang mendaftar lewat website.</p>

<section class="card mt-5">
    <form method="GET" action="{{ route('pelanggan.index') }}" class="flex flex-col gap-3 px-4 pt-5 sm:flex-row sm:px-5">
        <div class="relative flex-1">
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 size-5 -translate-y-1/2 text-slate-400" />
            <input type="search" name="cari" value="{{ request('cari') }}" placeholder="Cari nama, email, nomor HP, atau instansi" class="field pl-10">
        </div>
        <select name="status" class="field sm:w-48">
            <option value="">Semua Status</option>
            <option value="lead" @selected(request('status') === 'lead')>Lead</option>
            <option value="customer" @selected(request('status') === 'customer')>Customer</option>
        </select>
        <button type="submit" class="btn-outline">Cari</button>
    </form>

    {{-- tabel di layar >= md, tumpuk jadi kartu di HP --}}
    <table class="mt-4 block w-full text-left text-sm md:table">
        <thead class="hidden bg-thead text-[11px] font-bold uppercase tracking-[0.1em] md:table-header-group">
            <tr>
                <th class="px-5 py-3 font-medium">Nama</th>
                <th class="px-5 py-3 font-medium">Kontak</th>
                <th class="px-5 py-3 font-medium">Instansi</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Tanggal Daftar</th>
            </tr>
        </thead>
        <tbody class="block md:table-row-group">
            @forelse ($pelanggans as $pelanggan)
                <tr class="block border-t px-4 py-3 hover:bg-thead md:table-row md:p-0">
                    <td class="block pb-1 md:table-cell md:px-5 md:py-3 md:pb-3">
                        <a href="{{ route('pelanggan.show', $pelanggan) }}" class="font-medium text-brand-dark hover:underline">{{ $pelanggan->name }}</a>
                    </td>
                    <td class="block py-1 md:table-cell md:px-5 md:py-3">
                        <div>{{ $pelanggan->email }}</div>
                        <div class="text-xs text-slate-500">{{ $pelanggan->customerProfile->phone }}</div>
                    </td>
                    <td class="block py-1 text-slate-500 md:table-cell md:px-5 md:py-3">{{ $pelanggan->customerProfile->institution ?? '—' }}</td>
                    <td class="block py-1 md:table-cell md:px-5 md:py-3">
                        <span class="badge {{ $pelanggan->customerProfile->status === 'lead' ? 'badge-amber' : 'badge-green' }}">{{ ucfirst($pelanggan->customerProfile->status) }}</span>
                    </td>
                    <td class="block py-1 text-slate-500 md:table-cell md:px-5 md:py-3">{{ $pelanggan->customerProfile->registered_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr class="block border-t md:table-row">
                    <td colspan="5" class="block px-5 py-8 text-center text-slate-500 md:table-cell">Data pelanggan tidak ditemukan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="flex flex-col gap-3 border-t px-4 py-4 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-5">
        <span>Menampilkan {{ $pelanggans->firstItem() ?? 0 }}–{{ $pelanggans->lastItem() ?? 0 }} dari {{ $pelanggans->total() }} data</span>
        <div>{{ $pelanggans->links() }}</div>
    </div>
</section>
@endsection
