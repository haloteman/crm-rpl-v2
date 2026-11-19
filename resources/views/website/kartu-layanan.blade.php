<div class="card flex flex-col p-5">
    <x-ikon-layanan :layanan="$layanan" />
    <h3 class="mt-4 text-xl font-extrabold">{{ $layanan->name }}</h3>
    <p class="mt-1 flex-1 text-sm text-slate-600">{{ $layanan->short_description }}</p>
    <a href="{{ route('pesanan.create', $layanan->slug) }}" class="btn-dark mt-4 py-2">Ajukan Pesanan</a>
    <a href="{{ \App\Services\WaLinkBuilder::buildAdminInquiry($layanan) }}" target="_blank" rel="noopener" class="btn-outline mt-2 py-2">Tanya via WhatsApp</a>
    <a href="{{ route('website.layanan.show', $layanan->slug) }}" class="mt-3 text-sm font-bold underline underline-offset-4">Lihat detail →</a>
</div>
