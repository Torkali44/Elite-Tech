<?php $__env->startSection('title','نسيت كلمة المرور'); ?>
<?php $__env->startSection('content'); ?>
<h2 class="text-2xl font-black text-primary mb-2">استعادة كلمة المرور</h2>
<p class="text-sm text-tertiary mb-8">أدخل بريدك الإلكتروني وسنرسل لك رابط إعادة التعيين.</p>

<?php if(session('ok')): ?>
    <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3"><?php echo e(session('ok')); ?></div>
<?php endif; ?>
<?php if($errors->any()): ?>
    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3"><?php echo e($errors->first()); ?></div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('password.email')); ?>" class="space-y-4">
    <?php echo csrf_field(); ?>
    <div>
        <label class="block text-sm font-semibold text-primary mb-1.5">البريد الإلكتروني</label>
        <input type="email" name="email" value="<?php echo e(old('email')); ?>" class="input" required>
    </div>
    <button type="submit" class="btn-primary">إرسال الرابط ←</button>
</form>
<p class="text-center text-sm text-tertiary mt-6"><a href="<?php echo e(route('login')); ?>" class="text-primary hover:underline">← العودة لتسجيل الدخول</a></p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/auth/forgot.blade.php ENDPATH**/ ?>