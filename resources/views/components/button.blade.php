@props(['variant' => 'primary', 'href' => null, 'type' => 'button'])

@php
$variants = [
    'primary'   => 'btn-primary',
    'secondary' => 'btn-secondary',
    'outline'   => 'btn-outline',
];
$classes = $variants[$variant] ?? $variants['primary'];
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
