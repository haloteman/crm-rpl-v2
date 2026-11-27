{{-- daftar chat proyek milik user yang login --}}
<aside class="border-b-2 border-ink md:border-b-0 md:border-r-2">
    <div class="border-b-2 border-ink bg-paper px-4 py-3">
        <h2 class="font-display text-lg font-extrabold">Chat Proyek</h2>
    </div>
    @forelse ($daftarChat as $peserta)
        @php
            $proyekChat = $peserta->chat->project;
            $belumDibaca = $peserta->jumlahBelumDibaca();
            $dipilih = isset($chat) && $chat->id == $peserta->chat->id;
        @endphp
        <a href="{{ route('chat.show', $peserta->chat) }}" class="block border-b border-line px-4 py-3 text-sm {{ $dipilih ? 'bg-lime' : 'hover:bg-paper' }}">
            <div class="flex items-center gap-2">
                <span class="font-mono text-xs">{{ $proyekChat->order->order_code }}</span>
                @if ($proyekChat->status->code == 'selesai')
                    <span class="badge badge-green">Selesai</span>
                @endif
                @if ($belumDibaca > 0)
                    <span class="ml-auto flex size-5 items-center justify-center rounded-full border-2 border-ink bg-coral text-[10px] font-bold">{{ $belumDibaca }}</span>
                @endif
            </div>
            <div class="mt-0.5 font-bold">{{ $proyekChat->order->title }}</div>
            <div class="text-xs text-ink/60">{{ $proyekChat->order->service->name }}</div>
        </a>
    @empty
        <p class="px-4 py-6 text-sm text-ink/60">Belum ada chat. Chat dibuka otomatis setelah proyek ditugaskan ke desainer.</p>
    @endforelse
</aside>
