{{-- Shared brand logo --}}
@props([
    'class' => 'h-10 w-10 object-cover rounded-xl shadow-soft',
    'alt' => 'Elite Tech Community',
])
<img src="{{ asset('images/logo.jpeg') }}" alt="{{ $alt }}" {{ $attributes->merge(['class' => $class]) }} loading="eager">
