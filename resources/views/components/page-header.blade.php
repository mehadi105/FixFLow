@props([
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $title }}</h1>
        @if ($description)
            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-500">{{ $description }}</p>
        @endif
    </div>
    @if (isset($actions))
        <div class="flex flex-wrap items-center gap-3">
            {{ $actions }}
        </div>
    @endif
</div>
