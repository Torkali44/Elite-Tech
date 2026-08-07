<?php $__env->startSection('title', __('general.my_ideas_title')); ?>
<?php $__env->startSection('content'); ?>
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl lg:text-3xl font-extrabold text-primary mb-1"><?php echo e(__('general.my_ideas_title')); ?></h1>
        <p class="text-tertiary text-sm"><?php echo e(__('general.my_ideas_subtitle')); ?></p>
    </div>
    <a href="<?php echo e(route('ideas.create')); ?>" class="btn-primary text-sm"><?php echo e(__('general.add_new_idea')); ?></a>
</div>

<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php $__empty_1 = true; $__currentLoopData = $ideas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idea): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="badge <?php echo e([
                'published'=>'bg-emerald-50 text-emerald-700',
                'pending'=>'bg-amber-50 text-amber-700',
                'draft'=>'bg-mist text-tertiary',
                'archived'=>'bg-rose-50 text-rose-600',
            ][$idea->status] ?? 'bg-mist text-tertiary'); ?>">
                <?php echo e([
                    'published' => __('general.status_published'),
                    'pending'   => __('general.status_pending'),
                    'draft'     => __('general.status_draft'),
                    'archived'  => __('general.status_archived'),
                ][$idea->status] ?? $idea->status); ?>

            </span>
            <span class="badge bg-mist text-tertiary text-[10px]"><?php echo e($idea->category); ?></span>
        </div>
        <a href="<?php echo e(route('ideas.show', $idea->id)); ?>" class="font-bold text-primary mb-2 block hover:text-secondary"><?php echo e($idea->title); ?></a>
        <p class="text-xs text-tertiary line-clamp-3 mb-4"><?php echo e($idea->shortDesc(120)); ?></p>
        <?php if($idea->admin_notes): ?>
            <p class="text-xs text-rose-600 mb-3"><?php echo e(__('general.info') ?? 'Note'); ?>: <?php echo e($idea->admin_notes); ?></p>
        <?php endif; ?>

        <div class="flex flex-wrap gap-2 mb-3">
            <a href="<?php echo e(route('ideas.edit', $idea->id)); ?>" class="btn-outline text-xs !py-1.5 !px-3"><?php echo e(__('general.edit_btn')); ?></a>
            <?php if(in_array($idea->status, ['draft', 'archived'], true)): ?>
                <form method="POST" action="<?php echo e(route('ideas.submit', $idea->id)); ?>" class="inline"
                      onsubmit="return confirm('<?php echo e(__('general.confirm_submit_idea')); ?>')">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn-secondary text-xs !py-1.5 !px-3"><?php echo e(__('general.send_to_publish')); ?></button>
                </form>
            <?php endif; ?>
            <?php if($idea->status === 'published'): ?>
                <a href="<?php echo e(route('ideas.show', $idea->id)); ?>" class="btn-ghost text-xs !py-1.5 !px-3"><?php echo e(__('general.view_btn')); ?></a>
            <?php endif; ?>
        </div>

        <div class="flex items-center justify-between text-xs text-tertiary border-t border-mist pt-3">
            <span><?php echo e($idea->created_at->diffForHumans()); ?></span>
            <span><?php echo e($idea->likes_count); ?> <?php echo e(__('general.likes_count')); ?></span>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="card p-10 text-center col-span-full text-tertiary"><?php echo e(__('general.no_ideas_yet')); ?> <a href="<?php echo e(route('ideas.create')); ?>" class="text-secondary font-bold"><?php echo e(__('general.add_your_idea')); ?></a></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\dashboards\idea-owner.blade.php ENDPATH**/ ?>