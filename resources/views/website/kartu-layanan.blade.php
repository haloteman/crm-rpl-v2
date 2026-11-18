<div class="card flex flex-col p-5">
    <h3 class="font-semibold">{{ $layanan->name }}</h3>
    <p class="mt-1 flex-1 text-sm text-slate-500">{{ $layanan->short_description }}</p>
    <a href="{{ route('pesanan.create', $layanan->slug) }}" class="btn-primary mt-4">Ajukan Pesanan</a>
    <a href="{{ \App\Services\WaLinkBuilder::buildAdminInquiry($layanan) }}" target="_blank" rel="noopener" class="btn-outline mt-2">Tanya via WhatsApp</a>
    <a href="{{ route('website.layanan.show', $layanan->slug) }}" class="mt-3 text-sm font-medium text-brand-dark hover:underline">Lihat detail →</a>
</div>
