<footer class="mt-16 border-t border-mist bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-2.5">
                <?php if (isset($component)) { $__componentOriginal987d96ec78ed1cf75b349e2e5981978f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal987d96ec78ed1cf75b349e2e5981978f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.logo','data' => ['class' => 'h-8 w-8 object-cover rounded-md']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-8 w-8 object-cover rounded-md']); ?>
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
                <span class="font-extrabold text-primary text-sm">
                    Elite <span class="text-secondary">Community</span>
                </span>
            </div>

            <nav class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-tertiary">
                <a href="<?php echo e(route('ideas.index')); ?>" class="hover:text-primary transition"><?php echo e(__('navigation.ideas_bank')); ?></a>
                <a href="<?php echo e(route('jobs')); ?>" class="hover:text-primary transition"><?php echo e(__('navigation.employment')); ?></a>
                <a href="<?php echo e(route('about')); ?>" class="hover:text-primary transition"><?php echo e(__('navigation.about')); ?></a>
                <a href="<?php echo e(route('terms')); ?>" class="hover:text-primary transition"><?php echo e(__('navigation.terms')); ?></a>
                <a href="<?php echo e(route('privacy')); ?>" class="hover:text-primary transition"><?php echo e(__('navigation.privacy')); ?></a>
                <a href="<?php echo e(route('agreement')); ?>" class="hover:text-primary transition"><?php echo e(__('navigation.agreement')); ?></a>
            </nav>
        </div>

        <div class="border-t border-mist mt-8 pt-5 text-xs text-tertiary flex flex-wrap justify-between gap-2">
            <span>© <?php echo e(date('Y')); ?> Elite Tech Community. <?php echo e(app()->getLocale() === 'ar' ? 'جميع الحقوق محفوظة.' : 'All rights reserved.'); ?></span>
            <span><?php echo e(app()->getLocale() === 'ar' ? 'توثيق KYC · حماية البيانات' : 'KYC Verification · Data Protection'); ?></span>
        </div>
    </div>
</footer>
<?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/partials/footer.blade.php ENDPATH**/ ?>