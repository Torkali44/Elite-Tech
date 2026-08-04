<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title','لوحة التحكم'); ?> — Elite Tech Admin</title>
    <?php echo $__env->make('partials.styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body class="min-h-screen">
<div x-data="{ open:false }" class="flex min-h-screen">
    <aside :class="open ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
           class="fixed lg:static inset-y-0 right-0 w-72 bg-primary text-white z-40 transform transition lg:transform-none flex flex-col">
        <div class="p-6 border-b border-white/10">
            <div class="flex items-center gap-3">
                <?php if (isset($component)) { $__componentOriginal987d96ec78ed1cf75b349e2e5981978f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal987d96ec78ed1cf75b349e2e5981978f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.logo','data' => ['class' => 'h-11 w-11 object-cover rounded-xl']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-11 w-11 object-cover rounded-xl']); ?>
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
                <div>
                    <div class="font-bold text-sm">Elite Tech</div>
                    <div class="text-xs text-white/60">لوحة الإدارة</div>
                </div>
            </div>
        </div>
        <nav class="p-4 space-y-1 flex-1">
            <?php $active = optional(request()->route())->getName(); ?>
            <?php $__currentLoopData = [
                ['admin.dashboard','نظرة عامة','M3 12l9-9 9 9M5 10v10h14V10'],
                ['admin.verifications','طلبات KYC','M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['admin.ideas','مراجعة الأفكار','M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3'],
                ['admin.users','المستخدمون','M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87'],
                ['admin.implementations','طلبات التنفيذ','M13 10V3L4 14h7v7l9-11h-7z'],
                ['admin.reports','الإحصائيات','M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6m6 0V9a2 2 0 012-2h2a2 2 0 012 2v10'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route($item[0])); ?>"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition <?php echo e($active===$item[0] ? 'bg-white/15 text-white font-bold' : 'text-white/70 hover:bg-white/10 hover:text-white'); ?>">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($item[2]); ?>"/></svg>
                <?php echo e($item[1]); ?>

            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>
        <div class="p-4 border-t border-white/10">
            <form action="<?php echo e(route('admin.logout')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button class="w-full text-sm font-bold text-rose-200 border border-rose-300/30 rounded-lg py-2.5 hover:bg-rose-500/20">تسجيل خروج الإدارة</button>
            </form>
        </div>
    </aside>

    <div class="flex-1 min-w-0 bg-neutral">
        <header class="bg-white border-b border-mist sticky top-0 z-30">
            <div class="flex items-center justify-between px-4 lg:px-8 h-16 gap-4">
                <button @click="open=!open" type="button" class="lg:hidden p-2 rounded-lg hover:bg-neutral">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <span class="badge bg-primary/10 text-primary">جلسة إدارية نشطة</span>
                <div class="flex items-center gap-2">
                    <?php if(auth()->guard()->check()): ?>
                    <span class="text-xs font-bold text-tertiary hidden sm:block"><?php echo e(auth()->user()->name); ?></span>
                    <div class="w-9 h-9 rounded-full bg-primary text-white grid place-items-center font-bold text-xs shrink-0">
                        <?php echo e(mb_substr(auth()->user()->name ?? 'A', 0, 1)); ?>

                    </div>
                    <?php else: ?>
                    <div class="w-9 h-9 rounded-full bg-primary text-white grid place-items-center font-bold text-xs">A</div>
                    <?php endif; ?>
                </div>
            </div>
        </header>
        <main class="p-4 lg:p-8">
            <?php if(session('ok')): ?>
                <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3"><?php echo e(session('ok')); ?></div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3"><?php echo e(session('error')); ?></div>
            <?php endif; ?>
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
    <div x-show="open" x-cloak @click="open=false" class="fixed inset-0 bg-ink/40 z-30 lg:hidden"></div>
</div>
</body>
</html>
<?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/layouts/admin.blade.php ENDPATH**/ ?>