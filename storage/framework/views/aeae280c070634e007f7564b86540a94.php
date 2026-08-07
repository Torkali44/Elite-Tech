<header x-data="{ mobileOpen: false }" class="sticky top-0 z-50 bg-white border-b border-mist">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">
            <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-2.5 shrink-0">
                <?php if (isset($component)) { $__componentOriginal987d96ec78ed1cf75b349e2e5981978f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal987d96ec78ed1cf75b349e2e5981978f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.logo','data' => ['class' => 'h-9 w-9 object-cover rounded-lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-9 w-9 object-cover rounded-lg']); ?>
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
                <div class="leading-tight">
                    <div class="font-extrabold text-primary text-sm tracking-tight">
                        Elite <span class="text-secondary">Community</span>
                    </div>
                </div>
            </a>

            <nav class="hidden lg:flex items-center gap-1 text-sm font-semibold">
                <?php
                    $links = [
                        ['home', __('navigation.home')],
                        ['ideas.index', __('navigation.ideas_bank')],
                        ['jobs', __('navigation.jobs_forum')],
                        ['about', __('navigation.about')],
                    ];
                ?>
                <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$route, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isActive = request()->routeIs($route) || ($route === 'ideas.index' && request()->routeIs('ideas.*'));
                    ?>
                    <a href="<?php echo e(route($route)); ?>"
                       class="px-3.5 py-2 rounded-md transition-colors <?php echo e($isActive ? 'text-primary font-extrabold' : 'text-tertiary hover:text-primary'); ?>">
                        <?php echo e($label); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </nav>

            <div class="flex items-center gap-2 shrink-0">
                <!-- Language Switcher -->
                <a href="<?php echo e(route('lang.switch', app()->getLocale() === 'ar' ? 'en' : 'ar')); ?>"
                   class="px-2.5 py-1.5 rounded-lg text-xs font-extrabold bg-neutral text-primary border border-mist hover:bg-mist transition flex items-center gap-1">
                    🌐 <?php echo e(app()->getLocale() === 'ar' ? 'English' : 'العربية'); ?>

                </a>

                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('dashboard')); ?>" class="btn-primary text-sm !py-2 !px-4"><?php echo e(__('navigation.dashboard')); ?></a>
                    <form action="<?php echo e(route('logout')); ?>" method="POST" class="hidden sm:block">
                        <?php echo csrf_field(); ?>
                        <button class="btn-ghost text-sm !py-2"><?php echo e(__('navigation.logout')); ?></button>
                    </form>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="btn-ghost text-sm hidden sm:inline-flex"><?php echo e(__('navigation.login')); ?></a>
                    <a href="<?php echo e(route('register')); ?>" class="btn-secondary text-sm !py-2 !px-4"><?php echo e(__('navigation.register')); ?></a>
                <?php endif; ?>

                <button @click="mobileOpen = !mobileOpen"
                        type="button"
                        class="lg:hidden p-2 rounded-md bg-neutral text-primary"
                        aria-label="<?php echo e(__('navigation.menu')); ?>">
                    <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <nav x-show="mobileOpen"
             x-cloak
             x-transition
             class="lg:hidden py-3 flex flex-col gap-1 border-t border-mist">
            <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$route, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $isActive = request()->routeIs($route) || ($route === 'ideas.index' && request()->routeIs('ideas.*'));
                ?>
                <a href="<?php echo e(route($route)); ?>"
                   class="px-3 py-2.5 rounded-md font-semibold text-sm <?php echo e($isActive ? 'bg-primary/5 text-primary' : 'text-tertiary hover:bg-neutral'); ?>">
                    <?php echo e($label); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <div class="pt-3 border-t border-mist mt-1 flex flex-col gap-2">
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('dashboard')); ?>" class="btn-primary w-full text-center text-sm"><?php echo e(__('navigation.dashboard')); ?></a>
                    <form action="<?php echo e(route('logout')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button class="btn-ghost w-full text-center text-sm text-rose-600"><?php echo e(__('navigation.logout_full')); ?></button>
                    </form>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="btn-outline w-full text-center text-sm"><?php echo e(__('navigation.login')); ?></a>
                    <a href="<?php echo e(route('register')); ?>" class="btn-secondary w-full text-center text-sm"><?php echo e(__('navigation.register')); ?></a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>
<?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/partials/navbar.blade.php ENDPATH**/ ?>