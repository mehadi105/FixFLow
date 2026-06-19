@props(['href', 'label' => 'Back'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'ff-link-back']) }}>
    ← {{ $label }}
</a>
