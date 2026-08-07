<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['color' => 'neutral']));

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

foreach (array_filter((['color' => 'neutral']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
$colors = [
    'neutral'   => 'bg-neutral text-tertiary',
    'primary'   => 'bg-primary/10 text-primary',
    'secondary' => 'bg-secondary/10 text-secondary',
    'success'   => 'bg-emerald-50 text-emerald-600',
    'danger'    => 'bg-rose-50 text-rose-600',
    'solid-secondary' => 'bg-secondary text-white',
    'solid-primary'   => 'bg-primary text-white',
];
?>

<span <?php echo e($attributes->merge(['class' => 'badge '.($colors[$color] ?? $colors['neutral'])])); ?>>
    <?php echo e($slot); ?>

</span>
<?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\components\badge.blade.php ENDPATH**/ ?>