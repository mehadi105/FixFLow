@props(['count' => 0, 'live' => false])

<span
    @if ($live) data-chat-unread-badge @endif
    {{ $attributes->merge(['class' => 'ff-unread-badge'.($count > 0 ? '' : ' hidden')]) }}
    aria-hidden="{{ $count > 0 ? 'false' : 'true' }}"
>{{ $count > 99 ? '99+' : $count }}</span>
