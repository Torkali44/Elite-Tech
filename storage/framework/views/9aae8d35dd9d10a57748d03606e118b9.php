
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'class' => 'h-10 w-10 object-cover rounded-xl shadow-soft',
    'alt' => 'Elite Tech Community',
]));

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

foreach (array_filter(([
    'class' => 'h-10 w-10 object-cover rounded-xl shadow-soft',
    'alt' => 'Elite Tech Community',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>
<img src="<?php echo e(asset('images/logo.jpeg')); ?>" alt="<?php echo e($alt); ?>" <?php echo e($attributes->merge(['class' => $class])); ?> loading="eager">
<?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\components\logo.blade.php ENDPATH**/ ?>