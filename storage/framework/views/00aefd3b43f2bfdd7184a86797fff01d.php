<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['icon' => '💡', 'label', 'value']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['icon' => '💡', 'label', 'value']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div <?php echo e($attributes->merge(['class' => 'card p-5'])); ?>>
    <div class="text-2xl mb-1"><?php echo e($icon); ?></div>
    <div class="text-2xl font-black text-primary"><?php echo e($value); ?></div>
    <div class="text-xs text-tertiary"><?php echo e($label); ?></div>
</div>
<?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\components\stat-card.blade.php ENDPATH**/ ?>