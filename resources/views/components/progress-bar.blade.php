@props(['label', 'value', 'percent'])

<div>
    <div class="mb-1 flex items-center justify-between text-sm">
        <span class="text-slate-600">{{ $label }}</span>
        <span class="font-medium text-slate-900">{{ $value }}</span>
    </div>
    <div class="ff-progress-track">
        <div class="ff-progress-bar" style="width: {{ $percent }}%"></div>
    </div>
</div>
