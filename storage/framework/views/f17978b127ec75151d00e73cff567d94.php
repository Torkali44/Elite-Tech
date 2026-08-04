<?php $__env->startSection('title','تحديد المسار'); ?>
<?php $__env->startSection('content'); ?>
<?php $current = old('roles', auth()->user()->roles ?? []); if (!is_array($current)) $current = []; ?>

<?php if(session('error')): ?>
    <div class="mb-4 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 text-sm px-4 py-3"><?php echo e(session('error')); ?></div>
<?php endif; ?>
<?php if(session('ok')): ?>
    <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3"><?php echo e(session('ok')); ?></div>
<?php endif; ?>

<div class="text-center mb-8">
    <h2 class="text-2xl font-extrabold text-primary mb-1">تحديد المسار</h2>
    <p class="text-sm text-tertiary leading-relaxed">
        اختر مساراً واحداً على الأقل. مسار صاحب الفكرة ومنتدى التوظيف يتطلبان KYC؛ باحث عن فكرة يتصفّح بحرية ويُطلب KYC عند الرغبة في التنفيذ.
    </p>
</div>

<?php if($errors->any()): ?>
    <div class="mb-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3"><?php echo e($errors->first()); ?></div>
<?php endif; ?>

<form action="<?php echo e(route('auth.path')); ?>" method="POST" class="space-y-3" id="path-form">
    <?php echo csrf_field(); ?>

    <label class="block p-5 rounded-xl border border-mist hover:border-primary/40 cursor-pointer transition path-card">
        <div class="flex items-start gap-4">
            <input type="checkbox" name="roles[]" value="idea_owner" class="mt-1 w-4 h-4 accent-primary role-cb"
                   <?php if(in_array('idea_owner', $current, true)): echo 'checked'; endif; ?>>
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="font-extrabold text-primary">صاحب فكرة</span>
                    <span class="badge bg-primary/10 text-primary">KYC قبل النشر</span>
                </div>
                <p class="text-sm text-tertiary leading-relaxed">أنشر أفكاراً في بنك الأفكار وأدير طلبات الانضمام بعد التوثيق.</p>
            </div>
        </div>
    </label>

    <label class="block p-5 rounded-xl border border-mist hover:border-primary/40 cursor-pointer transition path-card" id="seeker-card">
        <div class="flex items-start gap-4">
            <input type="checkbox" name="roles[]" value="idea_seeker" class="mt-1 w-4 h-4 accent-primary role-cb" id="role-seeker"
                   <?php if(in_array('idea_seeker', $current, true)): echo 'checked'; endif; ?>>
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="font-extrabold text-primary">باحث عن فكرة</span>
                    <span class="badge bg-emerald-50 text-emerald-700">تصفح حر · KYC للتنفيذ</span>
                </div>
                <p class="text-sm text-tertiary leading-relaxed">أتصفح بنك الأفكار بحرية. «الرغبة في التنفيذ» تتطلب KYC.</p>
            </div>
        </div>
    </label>

    <label class="block p-5 rounded-xl border border-mist hover:border-primary/40 cursor-pointer transition path-card" id="dev-card">
        <div class="flex items-start gap-4">
            <input type="checkbox" name="roles[]" value="developer" class="mt-1 w-4 h-4 accent-primary role-cb" id="role-developer"
                   <?php if(in_array('developer', $current, true)): echo 'checked'; endif; ?>>
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="font-extrabold text-primary">باحث عن عمل</span>
                    <span class="badge bg-neutral text-tertiary">CV حر بدون KYC</span>
                </div>
                <p class="text-sm text-tertiary leading-relaxed mb-3">أبني سيرتي وأستخرج PDF مجاناً.</p>
                <div id="jobs-box" class="mt-2 p-3 rounded-lg bg-neutral border border-mist <?php echo e(in_array('developer', $current, true) ? '' : 'hidden'); ?>">
                    <label class="flex items-start gap-2 text-sm">
                        <input type="checkbox" name="wants_jobs_forum" value="1" class="mt-0.5 accent-secondary"
                               <?php if(auth()->user()->wants_jobs_forum): echo 'checked'; endif; ?>>
                        <span>
                            <span class="font-bold text-primary">أريد الانضمام لمنتدى التوظيف</span>
                            <span class="block text-tertiary text-xs mt-0.5">يتطلب KYC + موافقة الإدارة للظهور العام.</span>
                        </span>
                    </label>
                </div>
            </div>
        </div>
    </label>

    <button type="submit" class="btn-primary w-full mt-4" id="submit-btn">حفظ ومتابعة</button>
    <a href="<?php echo e(route('dashboard')); ?>" class="block text-center text-sm text-tertiary hover:text-primary mt-2">العودة للوحة التحكم</a>
</form>

<script>
document.getElementById('role-developer')?.addEventListener('change', function () {
    document.getElementById('jobs-box')?.classList.toggle('hidden', !this.checked);
});
document.querySelectorAll('.role-cb').forEach(function (cb) {
    cb.addEventListener('change', function () {
        const card = this.closest('.path-card');
        if (!card) return;
        card.classList.toggle('border-primary', this.checked);
        card.classList.toggle('bg-primary/5', this.checked);
    });
    if (cb.checked) cb.dispatchEvent(new Event('change'));
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/auth/path-selection.blade.php ENDPATH**/ ?>