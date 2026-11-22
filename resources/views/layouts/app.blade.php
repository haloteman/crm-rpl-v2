@extends('layouts.base')

@php
    $menu = [
        ['route' => 'beranda', 'label' => 'Dashboard', 'icon' => 'dashboard'],
        ['route' => 'pesanan.index', 'label' => 'Pesanan Saya', 'icon' => 'shopping-bag', 'match' => 'pesanan.*', 'permission' => 'order.own'],
        ['route' => 'profil.show', 'label' => 'Pengaturan', 'icon' => 'settings', 'match' => 'profil.*', 'role' => 'Customer'],
        ['route' => 'pelanggan.index', 'label' => 'Data Pelanggan', 'icon' => 'customer', 'match' => 'pelanggan.*', 'permission' => 'customer.view'],
        ['route' => 'katalog.index', 'label' => 'Katalog Layanan', 'icon' => 'folder', 'match' => 'katalog.*', 'permission' => 'service.manage'],
        ['route' => 'pesanan-masuk.index', 'label' => 'Pesanan Masuk', 'icon' => 'penawaran', 'match' => 'pesanan-masuk.*', 'permission' => 'order.view'],
        ['route' => 'users.index', 'label' => 'User Management', 'icon' => 'user-cog', 'match' => 'users.*', 'permission' => 'user.view'],
    ];

    $menu = array_filter($menu, function ($item) {
        if (isset($item['role'])) {
            return auth()->user()->hasRole($item['role']);
        }

        return ! isset($item['permission']) || auth()->user()->can($item['permission']);
    });

    $me = auth()->user();
    $inisial = collect(preg_split('/\s+/', trim($me->name)))
        ->filter()->take(2)
        ->map(fn ($kata) => mb_strtoupper(mb_substr($kata, 0, 1)))
        ->implode('');
@endphp

@section('body')
@if ($me->hasRole('Customer'))
    {{-- portal customer: navbar di atas --}}
    <div x-data="{ menu: false }" class="min-h-screen">
        <header class="sticky top-0 z-20 border-b-2 border-ink bg-paper">
            <div class="mx-auto flex h-16 max-w-6xl items-center gap-6 px-4">
                <a href="{{ route('website.beranda') }}"><x-logo /></a>

                <nav class="hidden items-center gap-1 text-sm md:flex">
                    @foreach ($menu as $item)
                        @php $active = request()->routeIs($item['match'] ?? $item['route']); @endphp
                        <a href="{{ route($item['route']) }}" @if ($active) aria-current="page" @endif
                           class="rounded-lg border-2 px-3 py-1.5 font-semibold {{ $active ? 'border-ink bg-lime shadow-hard-sm' : 'border-transparent hover:border-ink' }}">{{ $item['label'] }}</a>
                    @endforeach
                </nav>

                <div class="ml-auto hidden items-center gap-3 md:flex">
                    <span class="text-sm font-semibold">{{ $me->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-outline py-1.5">Keluar</button>
                    </form>
                </div>

                <button type="button" class="ml-auto md:hidden" @click="menu = ! menu" aria-label="Buka menu"><x-icon name="menu" class="size-6" /></button>
            </div>

            <nav x-show="menu" x-cloak class="flex flex-col gap-1 border-t-2 border-ink px-4 py-3 text-sm md:hidden">
                @foreach ($menu as $item)
                    <a href="{{ route($item['route']) }}" class="rounded-lg px-3 py-2 font-semibold">{{ $item['label'] }}</a>
                @endforeach
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-outline mt-2 w-full">Keluar</button>
                </form>
            </nav>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-8">
            @include('layouts.status')
            @yield('content')
        </main>
    </div>
@else
    {{-- CRM internal: sidebar di kiri --}}
    <div x-data="{ sidebar: false }" class="min-h-screen">
        <div x-show="sidebar" x-cloak @click="sidebar = false" class="fixed inset-0 z-30 bg-ink/50 lg:hidden"></div>

        <aside :class="sidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-40 flex w-[256px] flex-col border-r-2 border-ink bg-paper transition-transform">
            <div class="px-6 pt-6">
                <a href="{{ route('beranda') }}"><x-logo /></a>
            </div>

            <nav class="mt-6 flex-1 overflow-y-auto px-4 pb-6">
                <div class="mb-2 px-2 text-[11px] font-bold uppercase tracking-[0.12em] text-slate-600">Menu</div>
                @foreach ($menu as $item)
                    @php $active = request()->routeIs($item['match'] ?? $item['route']); @endphp
                    <a href="{{ route($item['route']) }}" @if ($active) aria-current="page" @endif
                       class="mb-1 flex h-11 items-center gap-3 rounded-xl border-2 px-3 text-sm {{ $active ? 'border-ink bg-lime font-bold shadow-hard-sm' : 'border-transparent font-medium hover:border-ink' }}">
                        <x-icon :name="$item['icon']" class="size-5 shrink-0" />
                        <span class="flex-1">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            {{-- kartu pengguna --}}
            <div class="m-4 rounded-2xl border-2 border-ink bg-white p-4">
                <div class="flex items-center gap-3">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-full border-2 border-ink bg-lime text-xs font-bold">{{ $inisial }}</span>
                    <div class="min-w-0">
                        <div class="truncate text-sm font-bold">{{ $me->name }}</div>
                        <div class="font-mono text-xs text-slate-600">{{ $me->internalProfile?->nip }}</div>
                    </div>
                </div>
                <span class="badge badge-green mt-3">{{ $me->getRoleNames()->first() }}</span>
                <a href="{{ route('password.edit') }}" class="btn-outline mt-3 w-full py-2 text-[13px]">
                    <x-icon name="key" class="size-4" /> Ganti password
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-outline mt-2 w-full py-2 text-[13px]">
                        <x-icon name="logout" class="size-4" /> Keluar
                    </button>
                </form>
            </div>
        </aside>

        <div class="lg:pl-[256px]">

            <main class="p-4 sm:p-8">
                @include('layouts.status')
                @yield('content')
            </main>
        </div>
    </div>
@endif
@endsection
