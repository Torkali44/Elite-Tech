<?php $__env->startSection('title', 'تطوير الفكرة — '.$idea->title); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-xl mx-auto px-4 py-12">
    <div class="card p-8 space-y-5">
        <h1 class="text-2xl font-black text-primary">تطوير / استنساخ الفكرة</h1>
        <p class="text-sm text-tertiary leading-relaxed">
            سيتم إنشاء <b>مسودة جديدة</b> مرتبطة بالفكرة الأصلية
            «<?php echo e($idea->title); ?>» لصاحبها <b><?php echo e($idea->user->name ?? 'عضو'); ?></b>
            مع شارة حفظ الحقوق الأدبية عند النشر.
        </p>
        <form method="POST" action="<?php echo e(route('ideas.fork', $idea->id)); ?>" class="space-y-3">
            <?php echo csrf_field(); ?>
            <button class="btn-secondary w-full">تأكيد الاستنساخ وإنشاء المسودة</button>
            <a href="<?php echo e(route('ideas.show', $idea->id)); ?>" class="btn-outline w-full text-center block">رجوع</a>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\ideas\fork-confirm.blade.php ENDPATH**/ ?>