@props(['title', 'desc', 'state' => 'pending', 'last' => false])

@php
$icons = [
    'done'    => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>',
    'current' => '<span class="w-2.5 h-2.5 rounded-full bg-secondary"></span>',
    'pending' => '<span class="w-2.5 h-2.5 rounded-full bg-slate-300"></span>',
];
$dotClasses = [
    'done'    => 'bg-primary text-white',
    'current' => 'bg-secondary/10 text-secondary ring-2 ring-secondary',
    'pending' => 'bg-slate-100 text-slate-400',
];
@endphp

<div class="flex gap-3 {{ $last ? '' : 'pb-6 border-r-2 border-slate-100 mr-4 -mb-1' }}">
    <div class="w-8 h-8 -mr-4 rounded-full grid place-items-center shrink-0 {{ $dotClasses[$state] }}">
        {!! $icons[$state] !!}
    </div>
    <div class="pt-0.5">
        <div class="font-bold text-primary text-sm">{{ $title }}</div>
        <div class="text-xs text-tertiary mt-0.5">{{ $desc }}</div>
    </div>
</div>
