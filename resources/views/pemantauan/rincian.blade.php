{{-- isi detail proyek, dipakai halaman pemantauan dan proyek saya --}}
<div class="mt-5 grid items-start gap-5 lg:grid-cols-[1fr_340px]">
    <section class="card p-5">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-lg font-extrabold">Timeline progres</h2>
            <span class="text-xs text-ink/60">Terbaru di atas</span>
        </div>
        <div class="mt-3 flex justify-between text-sm font-bold">
            <span>Progres keseluruhan</span><span>{{ $project->progress_percentage }}%</span>
        </div>
        <div class="mt-1 h-3 rounded-full border-2 border-ink bg-white">
            <div class="h-full rounded-full bg-violet" style="width: {{ $project->progress_percentage }}%"></div>
        </div>

        <ol class="mt-5 space-y-4 border-l-2 border-ink pl-5 text-sm">
            @forelse ($project->progressUpdates as $pembaruan)
                <li class="relative">
                    <span class="absolute -left-[27px] top-1 size-3 rounded-full border-2 border-ink bg-lime"></span>
                    <div class="font-bold">{{ $pembaruan->percentage }}% · {{ $pembaruan->stage }}</div>
                    <div class="text-xs text-ink/60">{{ $pembaruan->created_at->translatedFormat('d M Y, H:i') }}</div>
                    @if ($pembaruan->note)
                        <p class="mt-1 whitespace-pre-line">{{ $pembaruan->note }}</p>
                    @endif
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($pembaruan->attachments as $lampiran)
                            @if ($lampiran->attachment_type == 'file')
                                <a href="{{ route('lampiran.unduh', $lampiran) }}" class="btn-outline py-1 text-xs">{{ $lampiran->original_name }}</a>
                            @else
                                <a href="{{ $lampiran->url_or_path }}" target="_blank" rel="noopener" class="btn-outline py-1 text-xs">Tautan hasil</a>
                            @endif
                        @endforeach
                    </div>
                </li>
            @empty
                <li class="text-ink/60">Belum ada pembaruan progres.</li>
            @endforelse
        </ol>
    </section>

    <div class="space-y-5">
        <section class="card bg-warn-tint p-5">
            <h2 class="font-display text-lg font-extrabold">Ketentuan revisi yang disepakati</h2>
            <p class="mt-2 whitespace-pre-line text-sm font-medium">{{ $project->order->consultation?->revision_terms ?? 'Belum ada ketentuan revisi.' }}</p>
            <p class="mt-2 text-xs text-ink/60">Dicatat Owner saat konsultasi. Hanya-baca.</p>
        </section>

        <section class="card p-5">
            <h2 class="font-display text-lg font-extrabold">Ringkasan</h2>
            <dl class="mt-3 grid grid-cols-[100px_1fr] gap-y-1.5 text-sm">
                <dt class="text-ink/60">Layanan</dt><dd>{{ $project->order->service->name }}</dd>
                <dt class="text-ink/60">Customer</dt><dd>{{ $project->order->contact_name }}</dd>
                <dt class="text-ink/60">Desainer</dt><dd>{{ $project->designer?->name ?? 'Belum ditugaskan' }}</dd>
                <dt class="text-ink/60">Tenggat</dt>
                <dd>
                    {{ $project->deadline->translatedFormat('d M Y') }}
                    @if ($project->sisaHari() < 0 && $project->status->code != 'selesai')
                        · <span class="font-bold text-lost">terlambat {{ abs($project->sisaHari()) }} hari</span>
                    @endif
                </dd>
            </dl>
        </section>

        <section class="card p-5">
            <h2 class="font-display text-lg font-extrabold">Brief dan referensi</h2>
            <p class="mt-2 whitespace-pre-line text-sm">{{ $project->order->description }}</p>
            @if ($project->order->consultation?->notes)
                <p class="mt-3 text-sm font-bold">Lingkup yang disepakati</p>
                <p class="whitespace-pre-line text-sm">{{ $project->order->consultation->notes }}</p>
            @endif
            @php $order = $project->order; @endphp
            @include('pesanan.referensi')
        </section>

        <section class="card p-5">
            <h2 class="font-display text-lg font-extrabold">Riwayat penugasan</h2>
            <ol class="mt-3 space-y-3 border-l-2 border-ink pl-4 text-sm">
                @forelse ($project->assignments->sortByDesc('assigned_at') as $penugasan)
                    <li class="relative">
                        <span class="absolute -left-[23px] top-1 size-3 rounded-full border-2 border-ink {{ $penugasan->assignment_type == 'initial' ? 'bg-lime' : 'bg-sun' }}"></span>
                        <div class="font-bold">{{ $penugasan->assignment_type == 'initial' ? 'Penugasan awal' : 'Dipindahkan' }}: {{ $penugasan->designer->name }}</div>
                        <div class="text-xs text-ink/60">{{ $penugasan->assigned_at->translatedFormat('d M Y') }}</div>
                        @if ($penugasan->note)
                            <p class="mt-1">{{ $penugasan->note }}</p>
                        @endif
                    </li>
                @empty
                    <li class="text-ink/60">Belum ditugaskan.</li>
                @endforelse
            </ol>
        </section>
    </div>
</div>
