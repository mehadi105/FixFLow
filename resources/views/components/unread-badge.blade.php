@props(['count' => 0])

@if ($count > 0)
    <span {{ $attributes->merge(['class' => 'ff-unread-badge']) }}>
        {{ $count > 99 ? '99+' : $count }}
    </span>
@endif
