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

<a href="{{ $href }}" {{ $attributes->merge(['class' => "text-sm font-semibold transition-colors {$classes}"]) }}>
    {{ $slot }}
</a>
