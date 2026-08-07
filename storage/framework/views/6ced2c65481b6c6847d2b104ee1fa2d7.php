<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'desc', 'state' => 'pending', 'last' => false]));

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

foreach (array_filter((['title', 'desc', 'state' => 'pending', 'last' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
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
?>

<div class="flex gap-3 <?php echo e($last ? '' : 'pb-6 border-r-2 border-slate-100 mr-4 -mb-1'); ?>">
    <div class="w-8 h-8 -mr-4 rounded-full grid place-items-center shrink-0 <?php echo e($dotClasses[$state]); ?>">
        <?php echo $icons[$state]; ?>

    </div>
    <div class="pt-0.5">
        <div class="font-bold text-primary text-sm"><?php echo e($title); ?></div>
        <div class="text-xs text-tertiary mt-0.5"><?php echo e($desc); ?></div>
    </div>
</div>
<?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\components\stepper-step.blade.php ENDPATH**/ ?>