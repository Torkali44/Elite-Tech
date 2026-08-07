<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="<?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', __('auth.welcome')); ?> — Elite Tech</title>
    <?php echo $__env->make('partials.styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body class="min-h-screen bg-neutral relative">
<div class="absolute top-4 <?php echo e(app()->getLocale() === 'ar' ? 'left-4' : 'right-4'); ?> z-50">
    <a href="<?php echo e(route('lang.switch', app()->getLocale() === 'ar' ? 'en' : 'ar')); ?>"
       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-extrabold bg-white border border-mist text-primary hover:bg-neutral transition shadow-sm">
        🌐 <?php echo e(app()->getLocale() === 'ar' ? 'English' : 'العربية'); ?>

    </a>
</div>
<div class="min-h-screen grid lg:grid-cols-2">
    <aside class="hidden lg:block relative overflow-hidden">
        <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=1200&q=80"
             alt="Elite Tech Community"
             class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-primary/70"></div>
        <div class="relative z-10 h-full flex flex-col justify-between p-12 text-white">
            <div>
                <div class="font-extrabold text-lg">
                    Elite <span class="text-secondary">Community</span>
                </div>
            </div>
            <div class="max-w-md">
                <h1 class="text-3xl font-extrabold mb-3 leading-tight"><?php echo e(__('auth.welcome')); ?></h1>
                <p class="text-white/80 leading-relaxed text-sm">
                    <?php echo e(__('auth.tagline')); ?>

                </p>
            </div>
            <div class="flex gap-4 text-xs text-white/70">
                <a href="<?php echo e(route('privacy')); ?>" class="hover:text-white"><?php echo e(__('navigation.privacy')); ?></a>
                <a href="<?php echo e(route('terms')); ?>" class="hover:text-white"><?php echo e(__('navigation.terms')); ?></a>
            </div>
        </div>
    </aside>

    <div class="flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-md">
            <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-2.5 mb-8 lg:hidden">
                <?php if (isset($component)) { $__componentOriginal987d96ec78ed1cf75b349e2e5981978f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal987d96ec78ed1cf75b349e2e5981978f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.logo','data' => ['class' => 'h-10 w-10 object-cover rounded-lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-10 w-10 object-cover rounded-lg']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal987d96ec78ed1cf75b349e2e5981978f)): ?>
<?php $attributes = $__attributesOriginal987d96ec78ed1cf75b349e2e5981978f; ?>
<?php unset($__attributesOriginal987d96ec78ed1cf75b349e2e5981978f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal987d96ec78ed1cf75b349e2e5981978f)): ?>
<?php $component = $__componentOriginal987d96ec78ed1cf75b349e2e5981978f; ?>
<?php unset($__componentOriginal987d96ec78ed1cf75b349e2e5981978f); ?>
<?php endif; ?>
                <div class="font-extrabold text-primary text-sm">
                    Elite <span class="text-secondary">Community</span>
                </div>
            </a>
            <div class="hidden lg:flex items-center gap-2.5 mb-8">
                <?php if (isset($component)) { $__componentOriginal987d96ec78ed1cf75b349e2e5981978f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal987d96ec78ed1cf75b349e2e5981978f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.logo','data' => ['class' => 'h-10 w-10 object-cover rounded-lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-10 w-10 object-cover rounded-lg']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal987d96ec78ed1cf75b349e2e5981978f)): ?>
<?php $attributes = $__attributesOriginal987d96ec78ed1cf75b349e2e5981978f; ?>
<?php unset($__attributesOriginal987d96ec78ed1cf75b349e2e5981978f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal987d96ec78ed1cf75b349e2e5981978f)): ?>
<?php $component = $__componentOriginal987d96ec78ed1cf75b349e2e5981978f; ?>
<?php unset($__componentOriginal987d96ec78ed1cf75b349e2e5981978f); ?>
<?php endif; ?>
                <div class="font-extrabold text-primary text-sm">
                    Elite <span class="text-secondary">Community</span>
                </div>
            </div>
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>
</div>
</body>
</html>
<?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\layouts\auth.blade.php ENDPATH**/ ?>