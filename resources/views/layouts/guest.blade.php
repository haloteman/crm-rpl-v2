@extends('layouts.base')

@section('body')
<div class="grid min-h-screen lg:grid-cols-2">

    <aside class="relative hidden flex-col overflow-hidden border-r-2 border-ink p-12 lg:flex {{ request()->routeIs('login') ? 'bg-violet/15' : 'bg-ink text-white' }}">
        <a href="{{ route('website.beranda') }}"><x-logo /></a>

        <div class="relative z-10 my-auto max-w-md">
            @if (request()->routeIs('login'))
                <span class="badge bg-ink text-white">CRM</span>
                <h2 class="mt-4 text-6xl font-extrabold leading-[1.05]">Masuk dengan <span class="mark">NIP</span> Anda.</h2>
                <ul class="mt-6 space-y-3">
                    <li class="flex gap-2"><x-icon name="check" class="size-5 shrink-0 text-violet" /> NIP dan password awal diberikan oleh Super Admin</li>
                    <li class="flex gap-2"><x-icon name="check" class="size-5 shrink-0 text-violet" /> Password awal wajib diganti saat login pertama</li>
                </ul>
            @else
                <h2 class="text-6xl font-extrabold leading-[1.05]">Desain rapi, <span class="mark text-ink">proses jelas.</span></h2>
                <p class="mt-5 text-white/75">Konsultasi, pesanan, dan progres proyek PT Media Creative dalam satu tempat.</p>
            @endif
        </div>

        <p class="relative z-10 text-xs opacity-60">&copy; PT Media Creative</p>

        <div aria-hidden="true" class="absolute -bottom-40 -right-24 size-96 rounded-full bg-lime"></div>
        <div aria-hidden="true" class="absolute right-24 top-28 size-24 rotate-[-8deg] rounded-xl border-2 border-ink bg-sun shadow-[5px_5px_0_0_var(--color-violet)]"></div>
    </aside>

    <main class="flex flex-col items-center justify-center px-4 py-10">
        <a href="{{ route('website.beranda') }}" class="mb-8 lg:hidden"><x-logo /></a>
        <div class="w-full max-w-[480px]">
            <div class="card p-6 sm:p-9">
                @yield('content')
            </div>
            @hasSection('luar-kartu')
                <div class="mt-6 text-center text-xs text-slate-600">@yield('luar-kartu')</div>
            @endif
        </div>
    </main>
</div>
@endsection
