<?php $__env->startSection('title','دخول لوحة التحكم'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-6 flex items-center gap-2">
    <span class="badge bg-primary/10 text-primary">منطقة إدارية منفصلة</span>
</div>
<h2 class="text-2xl font-black text-primary mb-2">لوحة تحكم الإدارة</h2>
<p class="text-sm text-tertiary mb-6">هذه الصفحة مختلفة عن تسجيل دخول الأعضاء. استخدم بيانات الإدارة فقط.</p>

<?php if($errors->any()): ?>
    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3">
        <?php echo e($errors->first()); ?>

    </div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="mb-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm px-4 py-3">
        <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>

<form action="<?php echo e(route('admin.login.submit')); ?>" method="POST" class="space-y-4" autocomplete="on">
    <?php echo csrf_field(); ?>
    <div>
        <label class="block text-sm font-bold text-primary mb-1.5">البريد الإلكتروني</label>
        <input type="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="admin@example.com" class="input" required autofocus dir="ltr">
    </div>
    <div>
        <label class="block text-sm font-bold text-primary mb-1.5">كلمة المرور</label>
        <input type="password" name="password" value="" placeholder="••••••••" class="input" required dir="ltr" autocomplete="current-password">
    </div>
    <button type="submit" class="btn-primary w-full">دخول للوحة التحكم</button>
</form>

<a href="<?php echo e(route('home')); ?>" class="block text-center text-sm text-tertiary hover:text-primary mt-6">← العودة إلى الموقع</a>
<p class="text-center text-xs text-tertiary mt-3">دخول الأعضاء من <a href="<?php echo e(route('login')); ?>" class="text-secondary font-bold">/login</a></p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/admin/login.blade.php ENDPATH**/ ?>