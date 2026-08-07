<?php $__env->startSection('title', __('admin.ideas_title')); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="text-2xl font-black text-primary mb-1"><?php echo e(__('admin.ideas_title')); ?></h1>
        <p class="text-sm text-tertiary"><?php echo e(__('admin.ideas_desc')); ?></p>
    </div>
    <form>
        <select name="status" class="input !py-2" onchange="this.form.submit()">
            <option value=""><?php echo e(__('admin.all_statuses')); ?></option>
            <?php $__currentLoopData = [
                'pending'  => (app()->getLocale()==='ar' ? 'مراجعة' : 'Pending'),
                'published'=> (app()->getLocale()==='ar' ? 'منشورة' : 'Published'),
                'draft'    => (app()->getLocale()==='ar' ? 'مسودة'  : 'Draft'),
                'archived' => (app()->getLocale()==='ar' ? 'مؤرشفة' : 'Archived'),
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($k); ?>" <?php if(request('status')===$k): echo 'selected'; endif; ?>><?php echo e($l); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </form>
</div>

<div class="space-y-4">
<?php $__empty_1 = true; $__currentLoopData = $ideas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idea): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<div class="card p-5" x-data="{ ret:false }">
    <div class="flex flex-wrap justify-between gap-3">
        <div>
            <h3 class="font-bold text-primary text-lg"><?php echo e($idea->title); ?></h3>
            <div class="text-xs text-tertiary mb-2"><?php echo e($idea->user->name ?? '—'); ?> · <?php echo e($idea->category); ?> · <?php echo e($idea->created_at->diffForHumans()); ?></div>
            <span class="badge bg-mist text-primary"><?php echo e($idea->status); ?></span>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(route('admin.ideas.show', $idea->id)); ?>" class="btn-ghost text-sm !py-2 border border-mist hover:bg-mist"><?php echo e(__('admin.view_details')); ?></a>
            <?php if($idea->status !== 'published'): ?>
            <form method="POST" action="<?php echo e(route('admin.ideas.publish', $idea->id)); ?>"><?php echo csrf_field(); ?>
                <button class="btn-primary text-sm !py-2"><?php echo e(__('admin.publish')); ?></button>
            </form>
            <?php endif; ?>
            <button type="button" @click="ret=!ret" class="btn-outline text-sm !py-2"><?php echo e(__('admin.return_draft')); ?></button>
        </div>
    </div>
    <p class="text-sm text-tertiary mt-3 line-clamp-3 whitespace-pre-line"><?php echo e($idea->description); ?></p>
    <form x-show="ret" x-cloak method="POST" action="<?php echo e(route('admin.ideas.return', $idea->id)); ?>" class="mt-3 space-y-2">
        <?php echo csrf_field(); ?>
        <textarea name="note" rows="2" class="input" placeholder="<?php echo e(app()->getLocale()==='ar' ? 'ما الذي يحتاج توضيحاً؟' : 'What needs clarification?'); ?>" required></textarea>
        <button class="btn-secondary text-sm !py-2"><?php echo e(__('admin.send_note')); ?></button>
    </form>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<div class="card p-10 text-center text-tertiary"><?php echo e(__('admin.no_ideas')); ?></div>
<?php endif; ?>
</div>
<div class="mt-6"><?php echo e($ideas->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/admin/ideas.blade.php ENDPATH**/ ?>