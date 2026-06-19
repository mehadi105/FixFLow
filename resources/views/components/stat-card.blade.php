@props([
    'title',
    'value',
    'subtitle' => null,
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'ff-card group p-6']) }}>
    <div class="flex items-start justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $title }}</p>
            <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ $value }}</p>
            @if ($subtitle)
                <p class="mt-1.5 text-xs font-medium text-emerald-600">{{ $subtitle }}</p>
            @endif
        </div>
        @if ($icon)
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-50 to-blue-50 text-indigo-600 ring-1 ring-indigo-100 transition-transform group-hover:scale-105">
                {!! $icon !!}
            </div>
        @else
            <div class="h-11 w-1 rounded-full bg-gradient-to-b from-indigo-500 to-blue-500 opacity-80"></div>
        @endif
    </div>
</div>
