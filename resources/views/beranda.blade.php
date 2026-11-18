@extends('layouts.app')
@section('title', 'Beranda')
@section('heading', 'Beranda')

@section('content')
<div class="card p-6 sm:p-8">
    <p class="eyebrow">Selamat datang</p>
    <h1 class="page-title mt-1">{{ auth()->user()->name }}</h1>
    <p class="page-sub">Anda login sebagai {{ auth()->user()->getRoleNames()->first() }}.</p>
</div>
@endsection
