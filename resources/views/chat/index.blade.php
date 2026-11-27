@extends('layouts.app')
@section('title', 'Chat Proyek')
@section('heading', 'Chat Proyek')

@section('content')
<p class="eyebrow">Chat Proyek</p>
<h1 class="page-title">Ngobrol soal <span class="mark">proyek.</span></h1>
<p class="page-sub max-w-2xl">Chat dibuka otomatis setelah proyek ditugaskan ke desainer. Untuk harga dan pesanan, tetap hubungi Owner lewat WhatsApp.</p>

<div class="card mt-5 grid overflow-hidden md:grid-cols-[300px_1fr]">
    @include('chat.daftar')
    <div class="flex min-h-[320px] items-center justify-center p-8 text-center text-sm text-ink/60">
        Pilih chat proyek di sebelah kiri.
    </div>
</div>
@endsection
