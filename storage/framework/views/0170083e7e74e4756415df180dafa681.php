<?php $__env->startSection('title', 'رغبة في التنفيذ — '.$idea->title); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-xl mx-auto px-4 py-12">
    <div class="card p-8 space-y-5">
        <h1 class="text-2xl font-black text-primary">الرغبة في التنفيذ</h1>
        <p class="text-sm text-tertiary">الفكرة: <b class="text-primary"><?php echo e($idea->title); ?></b></p>

        <?php if($existing): ?>
            <div class="rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm px-4 py-3">
                لديك طلب سابق بحالة: <b><?php echo e($existing->status); ?></b>. إعادة الإرسال تستبدله بطلب معلّق جديد.
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3"><?php echo e($errors->first()); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('ideas.implement', $idea->id)); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <label class="flex gap-3 p-4 rounded-xl border border-mist cursor-pointer has-[:checked]:border-primary">
                <input type="radio" name="via" value="elite_tech" class="mt-1 accent-primary" <?php if(old('via')==='elite_tech'): echo 'checked'; endif; ?>>
                <span class="text-sm"><b class="text-primary">عبر شركة إليت تك</b><br><span class="text-tertiary">وساطة وتنسيق احترافي</span></span>
            </label>
            <label class="flex gap-3 p-4 rounded-xl border border-mist cursor-pointer has-[:checked]:border-primary">
                <input type="radio" name="via" value="idea_owner" class="mt-1 accent-primary" <?php if(old('via', 'idea_owner')==='idea_owner'): echo 'checked'; endif; ?>>
                <span class="text-sm"><b class="text-primary">شراكة مع صاحب الفكرة</b><br><span class="text-tertiary">بعد موافقة صاحب الفكرة / الإدارة</span></span>
            </label>
            <textarea name="note" rows="3" class="input" placeholder="ملاحظة اختيارية..."><?php echo e(old('note')); ?></textarea>
            <label class="flex items-start gap-2 text-xs text-tertiary">
                <input type="checkbox" name="agree_terms" value="1" class="mt-0.5 accent-primary" required>
                <span>أوافق على <a href="<?php echo e(route('agreement')); ?>" target="_blank" class="text-primary underline">اتفاقية الاستخدام</a>.</span>
            </label>
            <button class="btn-secondary w-full">إرسال طلب التنفيذ</button>
            <a href="<?php echo e(route('ideas.show', $idea->id)); ?>" class="btn-outline w-full text-center block">رجوع</a>
        </form>

        <p class="text-xs text-tertiary leading-relaxed">
            <b>من يراجع؟</b> صاحب الفكرة يدير طلبات الانضمام لفريقه، والإدارة تراقب السجل ضد الاحتيال ويمكنها الموافقة أو الرفض أيضاً.
        </p>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\ideas\implement.blade.php ENDPATH**/ ?>