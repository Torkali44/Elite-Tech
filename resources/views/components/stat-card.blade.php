@props(['icon' => '💡', 'label', 'value'])

<div {{ $attributes->merge(['class' => 'card p-5']) }}>
    <div class="text-2xl mb-1">{{ $icon }}</div>
    <div class="text-2xl font-black text-primary">{{ $value }}</div>
    <div class="text-xs text-tertiary">{{ $label }}</div>
</div>
