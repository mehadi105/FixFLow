@props([
    'title' => null,
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'ff-card overflow-hidden']) }}>
    @if ($title || $description)
        <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
            @if ($title)
                <h3 class="text-base font-semibold tracking-tight text-slate-900">{{ $title }}</h3>
            @endif
            @if ($description)
                <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
            @endif
        </div>
    @endif
    <div class="p-6">
        {{ $slot }}
    </div>
</div>
