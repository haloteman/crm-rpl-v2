<h3 class="mt-5 text-sm font-semibold">Referensi dari customer</h3>
<ul class="mt-2 space-y-2 text-sm">
    @forelse ($order->references as $referensi)
        <li class="rounded-lg border px-3 py-2">
            @if ($referensi->reference_type === 'file')
                <a href="{{ route('referensi.unduh', $referensi) }}" class="font-medium text-brand-dark hover:underline">{{ $referensi->original_name }}</a>
                <span class="text-xs text-slate-500">· {{ number_format($referensi->file_size / 1048576, 1, ',', '.') }} MB</span>
            @else
                Tautan: <a href="{{ $referensi->url_or_path }}" target="_blank" rel="noopener" class="font-medium text-brand-dark hover:underline">{{ $referensi->url_or_path }}</a>
            @endif
        </li>
    @empty
        <li class="text-slate-500">Tidak ada referensi.</li>
    @endforelse
</ul>
