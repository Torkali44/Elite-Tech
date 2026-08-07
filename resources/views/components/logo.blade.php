{{-- Shared brand logo --}}
@props([
    'class' => 'h-10 w-auto max-w-full object-contain rounded-lg',
    'alt' => 'Elite Tech Community',
])
<img src="{{ asset('images/logo.jpg') }}" alt="{{ $alt }}" {{ $attributes->merge(['class' => $class]) }} loading="eager">
