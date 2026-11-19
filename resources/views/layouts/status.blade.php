@if (session('status'))
    <div class="mb-5 flex items-start gap-3 rounded-xl border-2 border-ink bg-lime-tint px-4 py-3 text-sm font-medium">
        <x-icon name="check-circle" class="mt-0.5 size-5 shrink-0" />
        <p>{{ session('status') }}</p>
    </div>
@endif
