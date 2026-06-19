@props([
    'eyebrow' => 'Dashboard',
    'title',
    'description',
])

<div {{ $attributes->merge(['class' => 'ff-hero-banner']) }}>
    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-300">{{ $eyebrow }}</p>
    <h1 class="mt-2 text-2xl font-bold tracking-tight text-white sm:text-3xl">{{ $title }}</h1>
    <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-300">{{ $description }}</p>
    @if (isset($actions))
        <div class="mt-6">{{ $actions }}</div>
    @endif
</div>
