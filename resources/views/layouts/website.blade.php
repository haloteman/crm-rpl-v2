@extends('layouts.base')

@php
    $menu = [
        ['route' => 'website.beranda', 'label' => 'Beranda'],
        ['route' => 'website.tentang-kami', 'label' => 'Tentang Kami'],
        ['route' => 'website.layanan', 'label' => 'Layanan', 'match' => 'website.layanan*'],
        ['route' => 'website.kontak', 'label' => 'Kontak'],
    ];
    $layananFooter = \App\Models\Service::where('is_active', true)->orderBy('sort_order')->pluck('name');
@endphp

@section('body')
<div x-data="{ menu: false }" class="flex min-h-screen flex-col">
    <header class="border-b bg-white">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4">
            <a href="{{ route('website.beranda') }}" class="font-bold">Media Creative</a>

            <nav class="hidden items-center gap-1 text-sm md:flex">
                @foreach ($menu as $item)
                    <a href="{{ route($item['route']) }}"
                       class="rounded-lg px-3 py-2 {{ request()->routeIs($item['match'] ?? $item['route']) ? 'bg-tint font-semibold' : 'hover:bg-thead' }}">{{ $item['label'] }}</a>
                @endforeach
            </nav>

            <div class="hidden items-center gap-2 md:flex">
                @auth
                    <a href="{{ route('beranda') }}" class="btn-primary">Panel Saya</a>
                @else
                    <a href="{{ route('customer.login') }}" class="btn-outline">Masuk</a>
                    <a href="{{ route('customer.register') }}" class="btn-primary">Daftar</a>
                @endauth
            </div>

            <button type="button" @click="menu = ! menu" class="md:hidden" aria-label="Buka menu"><x-icon name="menu" class="size-6" /></button>
        </div>

        <nav x-show="menu" x-cloak class="flex flex-col gap-1 border-t px-4 py-3 text-sm md:hidden">
            @foreach ($menu as $item)
                <a href="{{ route($item['route']) }}" class="rounded-lg px-3 py-2">{{ $item['label'] }}</a>
            @endforeach
            @auth
                <a href="{{ route('beranda') }}" class="btn-primary mt-2">Panel Saya</a>
            @else
                <a href="{{ route('customer.login') }}" class="btn-outline mt-2">Masuk</a>
                <a href="{{ route('customer.register') }}" class="btn-primary">Daftar</a>
            @endauth
        </nav>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="bg-ink text-white">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 py-10 text-sm sm:grid-cols-2 md:grid-cols-4">
            <div>
                <div class="font-bold">Media Creative</div>
                <p class="mt-2 text-white/70">PT Media Creative, jasa desain logo, kaos, editing video, foto, poster, flyer, dan banner.</p>
            </div>
            <div>
                <div class="font-bold">Layanan</div>
                <ul class="mt-2 space-y-1 text-white/70">
                    @foreach ($layananFooter as $nama)
                        <li>{{ $nama }}</li>
                    @endforeach
                </ul>
            </div>
            <div>
                <div class="font-bold">Perusahaan</div>
                <ul class="mt-2 space-y-1 text-white/70">
                    <li><a href="{{ route('website.tentang-kami') }}" class="hover:underline">Tentang Kami</a></li>
                    <li><a href="{{ route('website.kontak') }}" class="hover:underline">Kontak</a></li>
                    <li><a href="{{ route('customer.login') }}" class="hover:underline">Masuk</a></li>
                    <li><a href="{{ route('customer.register') }}" class="hover:underline">Daftar</a></li>
                </ul>
            </div>
            <div>
                <div class="font-bold">Kontak</div>
                <ul class="mt-2 space-y-1 text-white/70">
                    <li>WhatsApp: +{{ \App\Models\SystemSetting::get('wa_admin') }}</li>
                    <li>Email: [EMAIL PERUSAHAAN]</li>
                    <li>[ALAMAT PERUSAHAAN]</li>
                </ul>
            </div>
        </div>
        <div class="mx-auto max-w-6xl border-t border-white/10 px-4 py-4 text-xs text-white/60">&copy; PT Media Creative</div>
    </footer>
</div>
@endsection
