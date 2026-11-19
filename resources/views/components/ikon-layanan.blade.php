@props(['layanan'])
@php
    $ikon = [
        'desain-logo' => 'shapes',
        'desain-kaos' => 'shirt',
        'editing-video' => 'play',
        'editing-foto' => 'camera',
        'poster' => 'document',
        'flyer' => 'penawaran',
        'banner' => 'banner',
    ];
    $warna = ['bg-ink text-lime', 'bg-lime text-ink', 'bg-violet text-white', 'bg-sand text-ink'];
@endphp
<span {{ $attributes->merge(['class' => 'flex size-14 shrink-0 items-center justify-center rounded-xl border-2 border-ink shadow-hard-sm ' . $warna[($layanan->sort_order - 1) % 4]]) }}>
    <x-icon :name="$ikon[$layanan->slug] ?? 'folder'" class="size-7" />
</span>
