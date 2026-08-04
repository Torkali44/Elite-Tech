@props(['color' => 'neutral'])

@php
$colors = [
    'neutral'   => 'bg-neutral text-tertiary',
    'primary'   => 'bg-primary/10 text-primary',
    'secondary' => 'bg-secondary/10 text-secondary',
    'success'   => 'bg-emerald-50 text-emerald-600',
    'danger'    => 'bg-rose-50 text-rose-600',
    'solid-secondary' => 'bg-secondary text-white',
    'solid-primary'   => 'bg-primary text-white',
];
@endphp

<span {{ $attributes->merge(['class' => 'badge '.($colors[$color] ?? $colors['neutral'])]) }}>
    {{ $slot }}
</span>
