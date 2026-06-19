@props(['status'])

@php
    $styles = match(strtolower($status)) {
        'pending' => 'bg-amber-50 text-amber-800 ring-amber-500/20',
        'assigned' => 'bg-sky-50 text-sky-800 ring-sky-500/20',
        'diagnosing' => 'bg-violet-50 text-violet-800 ring-violet-500/20',
        'repairing', 'in progress' => 'bg-indigo-50 text-indigo-800 ring-indigo-500/20',
        'completed', 'paid' => 'bg-emerald-50 text-emerald-800 ring-emerald-500/20',
        'waiting for parts' => 'bg-orange-50 text-orange-800 ring-orange-500/20',
        'unpaid', 'overdue' => 'bg-rose-50 text-rose-800 ring-rose-500/20',
        'active' => 'bg-teal-50 text-teal-800 ring-teal-500/20',
        'expired' => 'bg-slate-100 text-slate-600 ring-slate-400/20',
        'high' => 'bg-rose-50 text-rose-800 ring-rose-500/20',
        'medium' => 'bg-amber-50 text-amber-800 ring-amber-500/20',
        'low' => 'bg-slate-100 text-slate-600 ring-slate-400/20',
        default => 'bg-slate-100 text-slate-600 ring-slate-400/20',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide ring-1 ring-inset {$styles}"]) }}>
    {{ ucfirst($status) }}
</span>
