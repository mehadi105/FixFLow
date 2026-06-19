@props([
    'href' => '#',
    'variant' => 'primary',
])

@php
    $classes = match($variant) {
        'primary' => 'font-semibold text-indigo-600 hover:text-indigo-800',
        'secondary' => 'font-medium text-slate-500 hover:text-slate-800',
        'danger' => 'font-semibold text-rose-600 hover:text-rose-800',
        default => 'font-semibold text-indigo-600 hover:text-indigo-800',
    };
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => "inline-flex items-center gap-1 text-sm transition-colors {$classes}"]) }}>
    {{ $slot }}
    <svg class="h-3.5 w-3.5 opacity-60" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
</a>
