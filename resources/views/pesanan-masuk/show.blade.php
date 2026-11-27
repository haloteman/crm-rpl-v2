@extends('layouts.app')
@section('title', $order->order_code)
@section('heading', 'Pesanan')

@php
    $kode = $order->status->code;
    $ditutup = in_array($kode, ['dibatalkan', 'ditolak']);
    $labelBayar = ['unpaid' => 'Belum bayar', 'dp_received' => 'DP diterima', 'paid' => 'Lunas'];
    $warnaKapasitas = ['available' => 'badge-green', 'almost_full' => 'badge-amber', 'full' => 'badge-red'];
    $teksKapasitas = ['available' => 'masih longgar', 'almost_full' => 'hampir penuh', 'full' => 'penuh'];

    // buka lagi popup yang tadi diisi kalau ada error validasi
    $bukaModal = '';
    if ($errors->has('reason_id') || $errors->has('reason_note')) $bukaModal = old('jenis_tutup', '');
    if ($errors->has('final_price') || $errors->has('dp_value')) $bukaModal = 'harga';
    if ($errors->has('amount') || $errors->has('paid_at') || $errors->has('method') || $errors->has('bukti')) $bukaModal = old('form_bayar', '');
@endphp

@section('content')
<div x-data="{ modal: '{{ $bukaModal }}' }">

<p class="text-sm">
    <a href="{{ route('pesanan-masuk.index') }}" class="font-bold text-violet underline underline-offset-4">Pesanan</a>
    / <span class="font-mono">{{ $order->order_code }}</span>
</p>

<p class="eyebrow mt-4">Detail Pesanan</p>
<h1 class="page-title">{{ $order->title }}</h1>
<p class="page-sub flex flex-wrap items-center gap-2">
    <span class="badge badge-gray font-mono">{{ $order->order_code }}</span>
    <span class="badge {{ $ditutup ? 'badge-red' : 'badge-blue' }}">{{ $order->status->label }}</span>
    @if ($order->created_via == 'admin')
        <span class="badge badge-purple">Dibuat Admin</span>
    @endif
    Diajukan {{ $order->created_at->translatedFormat('d M') }}
</p>

@error('status') <p class="mt-3 rounded-xl border-2 border-ink bg-lost-tint px-4 py-3 text-sm font-medium text-lost">{{ $message }}</p> @enderror
@error('koreksi') <p class="mt-3 rounded-xl border-2 border-ink bg-lost-tint px-4 py-3 text-sm font-medium text-lost">{{ $message }}</p> @enderror

{{-- tahapan status pesanan --}}
@unless ($ditutup)
    <ol class="card mt-5 grid grid-cols-3 gap-3 p-4 text-xs sm:grid-cols-6">
        @foreach ($semuaStatus->where('code', '!=', 'ditolak') as $status)
            @php
                $lewat = $status->sort_order < $order->status->sort_order;
                $sekarang = $status->code == $kode;
            @endphp
            <li>
                <div class="h-2.5 rounded-full border-2 border-ink {{ $sekarang ? 'bg-sun' : ($lewat ? 'bg-lime' : 'bg-white') }}"></div>
                <div class="mt-1.5 {{ $sekarang || $lewat ? 'font-bold' : 'text-ink/60' }}">{{ $status->label }}</div>
            </li>
        @endforeach
    </ol>
@endunless

@if ($order->closure)
    <section class="card mt-5 bg-lost-tint p-5">
        <h2 class="font-display text-lg font-extrabold">Pesanan {{ strtolower($order->status->label) }}</h2>
        <dl class="mt-2 grid grid-cols-[110px_1fr] gap-y-1 text-sm">
            <dt class="text-ink/60">Alasan</dt><dd class="font-bold">{{ $order->closure->reason->label }}</dd>
            <dt class="text-ink/60">Catatan</dt><dd>{{ $order->closure->reason_note }}</dd>
            <dt class="text-ink/60">Tanggal</dt><dd>{{ $order->closure->closed_at->translatedFormat('d M Y, H:i') }}</dd>
        </dl>
    </section>
@endif

<div class="mt-5 grid items-start gap-5 lg:grid-cols-[1fr_340px]">
    <div class="space-y-5">
        <div class="card space-y-6 p-5">
            <section>
                <h2 class="font-display text-lg font-extrabold">Customer</h2>
                <div class="mt-3 flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <div class="font-bold">{{ $order->contact_name }}</div>
                        <div class="text-sm text-ink/60">{{ $order->customer->customerProfile->institution ?? 'Perorangan' }}</div>
                        <span class="badge badge-gray mt-1">{{ ['lead' => 'Lead', 'new_customer' => 'New Customer', 'returning_customer' => 'Pelanggan Lama'][$order->customer->customerProfile->status] }}</span>
                    </div>
                    <a href="{{ route('pelanggan.show', $order->customer) }}" class="btn-outline py-1.5">Lihat profil pelanggan</a>
                </div>
                <dl class="mt-3 grid grid-cols-[100px_1fr] gap-y-1 text-sm">
                    <dt class="text-ink/60">Nomor HP</dt><dd class="font-mono">{{ $order->contact_phone }}</dd>
                    <dt class="text-ink/60">Email</dt><dd>{{ $order->customer->email }}</dd>
                </dl>

                @role('Owner')
                    @php $linkWa = \App\Services\WaLinkBuilder::buildCustomerContact($order, auth()->user()->name); @endphp
                    <a href="{{ $linkWa }}" target="_blank" rel="noopener" class="btn-primary mt-4">Hubungi Customer via WhatsApp</a>
                @endrole
            </section>

            <section class="border-t-2 border-line pt-5">
                <h2 class="font-display text-lg font-extrabold">Kebutuhan</h2>
                <div class="mt-3 font-bold">{{ $order->service->name }}</div>
                <div class="text-sm text-ink/60">Tenggat diharapkan {{ $order->expected_deadline->translatedFormat('d M Y') }}</div>
                <span class="badge {{ $warnaKapasitas[$kapasitas['status']] }} mt-1">
                    Minggu {{ $kapasitas['awal_minggu']->translatedFormat('d M') }}–{{ $kapasitas['akhir_minggu']->translatedFormat('d M') }}:
                    {{ $kapasitas['terisi'] }}/{{ $kapasitas['kapasitas'] }}, {{ $teksKapasitas[$kapasitas['status']] }}
                </span>
                <p class="mt-3 whitespace-pre-line text-sm">{{ $order->description }}</p>
                <dl class="mt-3 grid grid-cols-[140px_1fr] gap-y-1 text-sm">
                    <dt class="text-ink/60">Perkiraan anggaran</dt>
                    <dd class="font-bold">{{ $order->budget_estimate ? 'Rp'.number_format($order->budget_estimate, 0, ',', '.').' (perkiraan)' : '—' }}</dd>
                </dl>
                @include('pesanan.referensi')
            </section>
        </div>

        @include('pesanan.riwayat')
    </div>

    <div class="card p-5">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-lg font-extrabold">Kesepakatan</h2>
            @if ($order->consultation?->locked_at)
                <span class="badge badge-gray">Terkunci</span>
            @endif
        </div>

        {{-- 1. hasil konsultasi --}}
        <h3 class="mt-4 border-t-2 border-line pt-4 font-bold">1 · Hasil konsultasi</h3>
        @if (auth()->user()->can('consult.note') && $order->bolehCatatKonsultasi())
            <form method="POST" action="{{ route('konsultasi.simpan', $order) }}" class="mt-2">
                @csrf
                <label for="notes" class="block text-sm font-medium">Lingkup pekerjaan yang disepakati</label>
                <textarea id="notes" name="notes" rows="4" class="field mt-1.5">{{ old('notes', $order->consultation?->notes) }}</textarea>
                @error('notes') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

                <label for="revision_terms" class="mt-3 block text-sm font-medium">Ketentuan revisi</label>
                <textarea id="revision_terms" name="revision_terms" rows="3" class="field mt-1.5">{{ old('revision_terms', $order->consultation?->revision_terms) }}</textarea>

                <button type="submit" class="btn-outline mt-3">Simpan Catatan</button>
                <p class="mt-2 text-xs text-ink/60">Boleh disimpan dulu. Status tetap Menunggu Konsultasi.</p>
            </form>
        @else
            <p class="mt-2 text-sm font-medium">Lingkup pekerjaan</p>
            <p class="mt-1 whitespace-pre-line rounded-xl border-2 border-ink bg-paper px-3 py-2 text-sm">{{ $order->consultation?->notes ?? 'Belum diisi Owner.' }}</p>
            <p class="mt-3 text-sm font-medium">Ketentuan revisi</p>
            <p class="mt-1 whitespace-pre-line rounded-xl border-2 border-ink bg-paper px-3 py-2 text-sm">{{ $order->consultation?->revision_terms ?? 'Belum diisi Owner.' }}</p>
        @endif

        {{-- 2. harga akhir dan DP --}}
        <h3 class="mt-5 border-t-2 border-line pt-4 font-bold">2 · Harga akhir dan DP</h3>
        @can('price.set')
            @if ($order->pricing)
                <dl class="mt-2 grid grid-cols-[110px_1fr] gap-y-1 rounded-xl border-2 border-ink bg-paper px-3 py-2 text-sm">
                    <dt class="text-ink/60">Harga akhir</dt><dd class="font-bold">Rp{{ number_format($order->pricing->final_price, 0, ',', '.') }}</dd>
                    <dt class="text-ink/60">DP</dt><dd class="font-bold">Rp{{ number_format($order->pricing->dp_amount, 0, ',', '.') }}</dd>
                    <dt class="text-ink/60">Sisa tagihan</dt><dd class="font-bold">Rp{{ number_format($order->sisaTagihan(), 0, ',', '.') }}</dd>
                </dl>
                @if ($kode == 'menunggu_dp')
                    <button type="button" @click="modal = 'harga'" class="btn-outline mt-3">Ubah Harga dan DP</button>
                @endif
                @if ($order->pricingHistories->isNotEmpty())
                    <details class="mt-3 text-xs">
                        <summary class="cursor-pointer font-bold">Riwayat perubahan harga</summary>
                        <ul class="mt-2 space-y-1">
                            @foreach ($order->pricingHistories as $riwayatHarga)
                                <li>
                                    {{ $riwayatHarga->changed_at->translatedFormat('d M Y') }}:
                                    Rp{{ number_format($riwayatHarga->old_final_price, 0, ',', '.') }} → Rp{{ number_format($riwayatHarga->new_final_price, 0, ',', '.') }}
                                    ({{ $riwayatHarga->changedBy->name }})
                                </li>
                            @endforeach
                        </ul>
                    </details>
                @endif
            @elseif ($kode == 'menunggu_konsultasi')
                <p class="mt-2 text-sm text-ink/60">Tetapkan setelah konsultasi selesai. Catatan konsultasi akan dikunci.</p>
                <button type="button" @click="modal = 'harga'" class="btn-primary mt-3">Tetapkan Harga dan DP</button>
            @else
                <p class="mt-2 text-sm text-ink/60">Harga belum ditetapkan.</p>
            @endif
        @else
            <p class="mt-2 text-sm">Status pembayaran: <span class="badge badge-blue">{{ $labelBayar[$order->payment_status] }}</span></p>
            <p class="mt-1 text-xs text-ink/60">Nominal harga hanya bisa dilihat Owner.</p>
        @endcan

        {{-- 3. pembayaran --}}
        @can('revenue.manage')
            @if ($order->pricing)
                <h3 class="mt-5 border-t-2 border-line pt-4 font-bold">3 · Pembayaran</h3>
                <p class="mt-1 text-sm">Status: <span class="badge badge-blue">{{ $labelBayar[$order->payment_status] }}</span></p>

                @if ($kode == 'menunggu_dp' && $order->payment_status == 'unpaid')
                    <p class="mt-2 text-sm text-ink/60">Setelah memeriksa dana masuk (transfer atau QRIS), catat DP agar proyek otomatis dibuat.</p>
                    <button type="button" @click="modal = 'dp'" class="btn-primary mt-3">Catat DP Diterima</button>
                @elseif ($order->payment_status == 'dp_received')
                    <button type="button" @click="modal = 'pelunasan'" class="btn-primary mt-3">Catat Pelunasan</button>
                @endif

                @if ($order->payments->isNotEmpty())
                    <ul class="mt-3 space-y-2 text-sm">
                        @foreach ($order->payments->sortBy('paid_at') as $transaksi)
                            <li class="rounded-xl border-2 border-ink px-3 py-2 {{ $transaksi->status == 'cancelled' ? 'bg-paper text-ink/50' : 'bg-white' }}">
                                <div class="flex justify-between gap-2 {{ $transaksi->status == 'cancelled' ? 'line-through' : '' }}">
                                    <span class="font-bold">{{ $transaksi->payment_type == 'dp' ? 'DP' : 'Pelunasan' }}</span>
                                    <span class="font-bold">Rp{{ number_format($transaksi->amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="text-xs">
                                    {{ $transaksi->paid_at->translatedFormat('d M Y') }} · {{ $transaksi->method == 'qris' ? 'QRIS' : 'Transfer' }}
                                    @foreach ($transaksi->proofs as $bukti)
                                        · <a href="{{ route('bukti.unduh', $bukti) }}" class="underline">Bukti</a>
                                    @endforeach
                                </div>
                                @if ($transaksi->status == 'cancelled')
                                    <div class="text-xs">Dibatalkan: {{ $transaksi->cancel_reason }}</div>
                                @else
                                    <button type="button" @click="modal = 'koreksi-{{ $transaksi->id }}'" class="mt-1 text-xs font-bold text-violet underline">Perbaiki pencatatan</button>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            @endif
        @endcan

        {{-- batal dan tolak, hanya sebelum DP diterima --}}
        @if ($order->bolehPindahKe('dibatalkan') || $order->bolehPindahKe('ditolak'))
            <div class="mt-5 flex flex-wrap gap-2 border-t-2 border-line pt-4">
                @can('order.cancel')
                    @if ($order->bolehPindahKe('dibatalkan'))
                        <button type="button" @click="modal = 'batal'" class="btn-outline py-1.5 text-lost">Batalkan Pesanan</button>
                    @endif
                @endcan
                @role('Owner')
                    @if ($order->bolehPindahKe('ditolak'))
                        <button type="button" @click="modal = 'tolak'" class="btn-outline py-1.5 text-lost">Tolak Pesanan</button>
                    @endif
                @endrole
            </div>
            <p class="mt-2 text-xs text-ink/60">Batalkan: atas permintaan customer. Tolak: keputusan Owner karena kapasitas penuh atau tenggat terlalu singkat. Keduanya hanya sebelum DP diterima, alasan wajib diisi.</p>
        @endif
    </div>
</div>

{{-- popup tetapkan / ubah harga --}}
@can('price.set')
    <div x-show="modal === 'harga'" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-ink/50 p-4" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center">
            <form method="POST" action="{{ route($order->pricing ? 'harga.ubah' : 'harga.simpan', $order) }}" class="card w-full max-w-md p-5 sm:p-6" @click.outside="modal = ''">
                @csrf
                <p class="eyebrow">Modul 3 · Harga</p>
                <h2 class="font-display text-xl font-extrabold">{{ $order->pricing ? 'Ubah' : 'Tetapkan' }} harga akhir dan DP</h2>

                <label for="final_price" class="mt-4 block text-sm font-medium">Harga akhir (Rp)</label>
                <input type="number" id="final_price" name="final_price" min="1" value="{{ old('final_price', $order->pricing?->final_price) }}" class="field mt-1.5">
                @error('final_price') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

                <p class="mt-4 text-sm font-medium">Jenis DP</p>
                <div class="mt-1.5 flex gap-4 text-sm">
                    <label><input type="radio" name="dp_type" value="percent" @checked(old('dp_type', $order->pricing?->dp_type ?? 'percent') == 'percent')> Persen</label>
                    <label><input type="radio" name="dp_type" value="nominal" @checked(old('dp_type', $order->pricing?->dp_type) == 'nominal')> Nominal</label>
                </div>

                <label for="dp_value" class="mt-4 block text-sm font-medium">Besaran DP (persen atau rupiah)</label>
                <input type="number" id="dp_value" name="dp_value" min="1" value="{{ old('dp_value', $order->pricing?->dp_value ?? 50) }}" class="field mt-1.5">
                @error('dp_value') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

                <div class="mt-6 flex justify-end gap-3 border-t-2 border-line pt-4">
                    <button type="button" @click="modal = ''" class="btn-outline">Batal</button>
                    <button type="submit" class="btn-dark">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endcan

{{-- popup catat DP dan pelunasan --}}
@can('revenue.manage')
    @if ($order->pricing)
        @foreach (['dp' => 'Catat DP diterima', 'pelunasan' => 'Catat pelunasan'] as $jenis => $judul)
            <div x-show="modal === '{{ $jenis }}'" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-ink/50 p-4" role="dialog" aria-modal="true">
                <div class="flex min-h-full items-center justify-center">
                    <form method="POST" action="{{ route('pembayaran.'.$jenis, $order) }}" enctype="multipart/form-data" class="card w-full max-w-md p-5 sm:p-6" @click.outside="modal = ''">
                        @csrf
                        <input type="hidden" name="form_bayar" value="{{ $jenis }}">
                        <p class="eyebrow">Modul 3 · Pembayaran</p>
                        <h2 class="font-display text-xl font-extrabold">{{ $judul }}</h2>
                        @if ($jenis == 'dp')
                            <p class="mt-3 rounded-xl border-2 border-ink bg-tint px-3 py-2 text-sm">Periksa dulu dana masuk di rekening atau QRIS. Setelah dicatat, proyek dibuat otomatis dan status pelanggan diperbarui.</p>
                        @endif

                        <label for="paid_at_{{ $jenis }}" class="mt-4 block text-sm font-medium">Tanggal diterima</label>
                        <input type="date" id="paid_at_{{ $jenis }}" name="paid_at" max="{{ now()->toDateString() }}" value="{{ old('paid_at', now()->toDateString()) }}" class="field mt-1.5">
                        @error('paid_at') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

                        <label for="amount_{{ $jenis }}" class="mt-4 block text-sm font-medium">Nominal diterima (Rp)</label>
                        <input type="number" id="amount_{{ $jenis }}" name="amount" min="1"
                               value="{{ old('amount', $jenis == 'dp' ? $order->pricing->dp_amount : $order->sisaTagihan()) }}" class="field mt-1.5">
                        <p class="mt-1 text-xs text-ink/60">
                            {{ $jenis == 'dp' ? 'Besaran DP yang ditetapkan' : 'Sisa tagihan' }}:
                            Rp{{ number_format($jenis == 'dp' ? $order->pricing->dp_amount : $order->sisaTagihan(), 0, ',', '.') }}
                        </p>
                        @error('amount') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

                        <p class="mt-4 text-sm font-medium">Metode</p>
                        <div class="mt-1.5 flex gap-4 text-sm">
                            <label><input type="radio" name="method" value="transfer" @checked(old('method', 'transfer') == 'transfer')> Transfer bank</label>
                            <label><input type="radio" name="method" value="qris" @checked(old('method') == 'qris')> QRIS</label>
                        </div>
                        @error('method') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

                        <label for="bukti_{{ $jenis }}" class="mt-4 block text-sm font-medium">Bukti pembayaran (opsional)</label>
                        <input type="file" id="bukti_{{ $jenis }}" name="bukti" accept=".jpg,.jpeg,.png,.pdf" class="field mt-1.5">
                        <p class="mt-1 text-xs text-ink/60">JPG, PNG, atau PDF, maksimal 10 MB.</p>
                        @error('bukti') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

                        <div class="mt-6 flex justify-end gap-3 border-t-2 border-line pt-4">
                            <button type="button" @click="modal = ''" class="btn-outline">Batal</button>
                            <button type="submit" class="btn-dark">{{ $judul }}</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach

        {{-- popup perbaiki pencatatan pembayaran --}}
        @foreach ($order->payments->where('status', 'valid') as $transaksi)
            <div x-show="modal === 'koreksi-{{ $transaksi->id }}'" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-ink/50 p-4" role="dialog" aria-modal="true">
                <div class="flex min-h-full items-center justify-center">
                    <form method="POST" action="{{ route('pembayaran.koreksi', $transaksi) }}" enctype="multipart/form-data" x-data="{ aksi: 'koreksi' }"
                          class="card w-full max-w-md p-5 sm:p-6" @click.outside="modal = ''">
                        @csrf
                        <p class="eyebrow">Modul 3 · Pembayaran</p>
                        <h2 class="font-display text-xl font-extrabold">Perbaiki pencatatan {{ $transaksi->payment_type == 'dp' ? 'DP' : 'pelunasan' }}</h2>
                        <p class="mt-2 text-sm text-ink/60">Transaksi lama tidak dihapus, hanya ditandai dibatalkan.</p>

                        <div class="mt-4 flex gap-4 text-sm">
                            <label><input type="radio" name="aksi" value="koreksi" x-model="aksi"> Koreksi nominal</label>
                            @if ($transaksi->payment_type != 'dp')
                                <label><input type="radio" name="aksi" value="batal" x-model="aksi"> Batalkan</label>
                            @endif
                        </div>

                        <label for="cancel_reason_{{ $transaksi->id }}" class="mt-4 block text-sm font-medium">Alasan</label>
                        <input type="text" id="cancel_reason_{{ $transaksi->id }}" name="cancel_reason" required class="field mt-1.5">

                        <div x-show="aksi === 'koreksi'">
                            <label for="amount_k{{ $transaksi->id }}" class="mt-4 block text-sm font-medium">Nominal benar (Rp)</label>
                            <input type="number" id="amount_k{{ $transaksi->id }}" name="amount" min="1" value="{{ $transaksi->amount }}" class="field mt-1.5">

                            <label for="paid_at_k{{ $transaksi->id }}" class="mt-4 block text-sm font-medium">Tanggal diterima</label>
                            <input type="date" id="paid_at_k{{ $transaksi->id }}" name="paid_at" max="{{ now()->toDateString() }}" value="{{ $transaksi->paid_at->toDateString() }}" class="field mt-1.5">

                            <p class="mt-4 text-sm font-medium">Metode</p>
                            <div class="mt-1.5 flex gap-4 text-sm">
                                <label><input type="radio" name="method" value="transfer" @checked($transaksi->method == 'transfer')> Transfer bank</label>
                                <label><input type="radio" name="method" value="qris" @checked($transaksi->method == 'qris')> QRIS</label>
                            </div>

                            <label for="bukti_k{{ $transaksi->id }}" class="mt-4 block text-sm font-medium">Bukti pembayaran (opsional)</label>
                            <input type="file" id="bukti_k{{ $transaksi->id }}" name="bukti" accept=".jpg,.jpeg,.png,.pdf" class="field mt-1.5">
                        </div>

                        <div class="mt-6 flex justify-end gap-3 border-t-2 border-line pt-4">
                            <button type="button" @click="modal = ''" class="btn-outline">Batal</button>
                            <button type="submit" class="btn-dark">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endif
@endcan

{{-- popup batalkan dan tolak pesanan --}}
@foreach (['batal' => $alasanBatal, 'tolak' => $alasanTolak] as $jenis => $daftarAlasan)
    <div x-show="modal === '{{ $jenis }}'" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-ink/50 p-4" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center">
            <form method="POST" action="{{ route('pesanan-masuk.'.$jenis, $order) }}" class="card w-full max-w-lg p-5 sm:p-6" @click.outside="modal = ''">
                @csrf
                <input type="hidden" name="jenis_tutup" value="{{ $jenis }}">
                <p class="eyebrow text-lost">{{ $jenis == 'batal' ? 'Batalkan' : 'Tolak' }} pesanan</p>
                <h2 class="font-display text-xl font-extrabold">{{ $order->order_code }} · {{ $order->title }}</h2>

                <p class="mt-3 rounded-xl border-2 border-ink bg-warn-tint px-3 py-2 text-sm">
                    @if ($jenis == 'batal')
                        Pesanan yang dibatalkan tidak dapat diaktifkan lagi. Gunakan ini kalau customer sendiri yang meminta berhenti.
                    @else
                        Pesanan yang ditolak tidak dapat diaktifkan lagi. Customer melihat alasan dan catatan ini, lalu dapat memesan lagi dengan tenggat lain.
                    @endif
                </p>

                @if ($jenis == 'tolak')
                    <div class="mt-4 rounded-xl border-2 border-ink bg-paper px-3 py-2 text-sm">
                        <div class="font-bold">Kapasitas minggu tenggat</div>
                        <div class="font-display text-2xl font-extrabold">{{ $kapasitas['terisi'] }}<span class="text-sm">/{{ $kapasitas['kapasitas'] }}</span></div>
                        <div class="text-xs text-ink/60">Minggu {{ $kapasitas['awal_minggu']->translatedFormat('d M') }}–{{ $kapasitas['akhir_minggu']->translatedFormat('d M Y') }}</div>
                    </div>
                @endif

                <p class="mt-4 text-sm font-medium">Alasan</p>
                <div class="mt-1.5 space-y-2 text-sm">
                    @foreach ($daftarAlasan as $alasan)
                        <label class="flex items-center gap-3 rounded-xl border-2 border-ink bg-white px-3 py-2.5 has-checked:bg-lime-tint">
                            <input type="radio" name="reason_id" value="{{ $alasan->id }}" @checked(old('reason_id') == $alasan->id)> {{ $alasan->label }}
                        </label>
                    @endforeach
                </div>
                @error('reason_id') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

                <label for="reason_note_{{ $jenis }}" class="mt-4 block text-sm font-medium">Catatan</label>
                <textarea id="reason_note_{{ $jenis }}" name="reason_note" rows="3" class="field mt-1.5">{{ old('reason_note') }}</textarea>
                @error('reason_note') <p class="mt-1 text-sm text-lost">{{ $message }}</p> @enderror

                <div class="mt-6 flex justify-end gap-3 border-t-2 border-line pt-4">
                    <button type="button" @click="modal = ''" class="btn-outline">Batal</button>
                    <button type="submit" class="btn-danger">{{ $jenis == 'batal' ? 'Batalkan' : 'Tolak' }} Pesanan</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

</div>
@endsection
