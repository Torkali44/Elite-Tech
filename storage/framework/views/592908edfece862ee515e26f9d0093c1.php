<?php $__env->startSection('title', $user->name); ?>
<?php $__env->startSection('content'); ?>
<?php
    $cv = is_array($user->cv?->data) ? $user->cv->data : [];
    $vis = is_array($user->cv?->visibility) ? $user->cv->visibility : [];
    $str = fn ($k) => \App\Http\Controllers\ProfileController::asString($cv[$k] ?? '');
    $list = fn ($k) => \App\Http\Controllers\ProfileController::asSkills($cv[$k] ?? []);
    $showEmail = (bool) ($vis['show_email'] ?? false);
    $showPhone = (bool) ($vis['show_phone'] ?? false);
    $empLabels = ['full_time'=>'دوام كلي','part_time'=>'دوام جزئي','contract'=>'عقود'];
    $workLabels = ['remote'=>'عن بعد','hybrid'=>'هجين','onsite'=>'مقر الشركة'];
?>
<div class="max-w-3xl mx-auto px-4 py-12">
    <div class="card p-8">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-20 h-20 rounded-2xl bg-primary text-white grid place-items-center text-3xl font-black">
                <?php echo e(mb_substr($user->name, 0, 1)); ?>

            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-primary"><?php echo e($user->name); ?></h1>
                <p class="text-tertiary text-sm"><?php echo e($str('title') ?: ($user->title ?: $user->roleLabel())); ?></p>
                <?php if($user->isKycApproved()): ?>
                    <span class="badge bg-emerald-50 text-emerald-700 mt-1"><?php echo e(__('general.verified_badge')); ?></span>
                <?php endif; ?>
                <?php if($user->available_for_hire): ?>
                    <span class="badge bg-secondary/15 text-secondary mt-1"><?php echo e(__('general.available_badge')); ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="text-xs text-tertiary mb-4 space-y-1">
            <?php if($showEmail): ?><div><?php echo e($user->email); ?></div><?php endif; ?>
            <?php if($showPhone && $str('phone')): ?><div><?php echo e($str('phone')); ?></div><?php endif; ?>
            <?php if($str('location') || $user->location): ?><div><?php echo e($str('location') ?: $user->location); ?></div><?php endif; ?>
            <?php if($str('years_experience')): ?><div><?php echo e(__('general.experience_label')); ?>: <?php echo e($str('years_experience')); ?></div><?php endif; ?>
            <?php if($str('availability')): ?><div><?php echo e(__('general.availability_label')); ?>: <?php echo e($str('availability')); ?></div><?php endif; ?>
            <?php if(!empty($vis['employment_type'])): ?>
                <div><?php echo e($empLabels[$vis['employment_type']] ?? ''); ?><?php if(!empty($vis['work_style'])): ?> · <?php echo e($workLabels[$vis['work_style']] ?? ''); ?><?php endif; ?></div>
            <?php endif; ?>
            <div class="flex flex-wrap gap-3 pt-1">
                <?php if($str('portfolio_url')): ?><a class="text-primary underline" href="<?php echo e($str('portfolio_url')); ?>" target="_blank">Portfolio</a><?php endif; ?>
                <?php if($str('linkedin')): ?><a class="text-primary underline" href="<?php echo e($str('linkedin')); ?>" target="_blank">LinkedIn</a><?php endif; ?>
                <?php if($str('github')): ?><a class="text-primary underline" href="<?php echo e($str('github')); ?>" target="_blank">GitHub</a><?php endif; ?>
            </div>
        </div>

        <p class="text-sm text-tertiary leading-relaxed mb-6 whitespace-pre-line"><?php echo e($str('summary') ?: ($user->bio ?: __('general.community_member'))); ?></p>

        <?php if(count($list('skills'))): ?>
            <h3 class="font-bold text-primary text-sm mb-2"><?php echo e(__('general.skills_label')); ?></h3>
            <div class="flex flex-wrap gap-2 mb-5">
                <?php $__currentLoopData = $list('skills'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="badge bg-mist text-primary"><?php echo e($skill); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <?php if($str('experience')): ?>
            <h3 class="font-bold text-primary text-sm mb-1"><?php echo e(__('general.experience_section')); ?></h3>
            <p class="text-sm text-tertiary whitespace-pre-line mb-5"><?php echo e($str('experience')); ?></p>
        <?php endif; ?>

        <?php if($str('projects')): ?>
            <h3 class="font-bold text-primary text-sm mb-1"><?php echo e(__('general.projects_section')); ?></h3>
            <p class="text-sm text-tertiary whitespace-pre-line mb-5"><?php echo e($str('projects')); ?></p>
        <?php endif; ?>

        <?php if(auth()->guard()->check()): ?>
            <?php if(auth()->id() !== $user->id): ?>
                <a href="<?php echo e(route('network.index', ['with' => $user->id])); ?>" class="btn-secondary text-sm"><?php echo e(__('general.connect_btn')); ?></a>
            <?php else: ?>
                <a href="<?php echo e(route('profile.cv')); ?>" class="btn-outline text-sm"><?php echo e(__('general.edit_cv_btn')); ?></a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\profile\show.blade.php ENDPATH**/ ?>