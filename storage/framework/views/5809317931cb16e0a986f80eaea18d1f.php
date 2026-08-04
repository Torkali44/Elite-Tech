<?php $__env->startSection('title','لوحة التحكم'); ?>
<?php $__env->startSection('content'); ?>
<?php $user = auth()->user(); ?>

<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-primary mb-1">مرحباً، <?php echo e($user->name); ?></h1>
    <p class="text-tertiary text-sm">مسارك: <?php echo e($user->roleLabel()); ?> — إليك ملخص نشاطك وخطواتك التالية.</p>
</div>

<?php if($user->kyc_status === 'rejected' && $user->rejection_reason): ?>
    <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 p-4">
        <div class="font-bold text-rose-700 mb-1">طلب KYC مرفوض — أكمل النواقص</div>
        <p class="text-sm text-rose-700 mb-3"><?php echo e($user->rejection_reason); ?></p>
        <a href="<?php echo e(route('verification.kyc')); ?>" class="btn-secondary text-sm !py-2">إعادة التقديم</a>
    </div>
<?php elseif($user->kyc_status === 'pending'): ?>
    <div class="mb-6 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm px-4 py-3">
        طلب التحقق قيد مراجعة الإدارة. يمكنك متابعة بناء ملفك في الأثناء.
    </div>
<?php elseif($user->kyc_status === 'none' && ($user->hasRole('idea_owner') || $user->wants_jobs_forum)): ?>
    <div class="mb-6 rounded-lg bg-secondary/10 border border-secondary/25 p-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <div class="font-bold text-primary">الخطوة التالية: التحقق من الهوية (KYC)</div>
            <p class="text-sm text-tertiary">مسارك يتطلب KYC لتفعيل الصلاحيات الكاملة.</p>
        </div>
        <a href="<?php echo e(route('verification.kyc', ['purpose' => $user->wants_jobs_forum ? 'jobs_forum' : 'publish_idea'])); ?>"
           class="btn-secondary text-sm !py-2">ابدأ KYC</a>
    </div>
<?php endif; ?>

<div class="grid lg:grid-cols-[300px_1fr] gap-6 mb-8">
    <div class="card p-6 text-center">
        <div class="w-20 h-20 rounded-2xl bg-primary text-white grid place-items-center mx-auto mb-3 text-2xl font-black">
            <?php echo e(mb_substr($user->name, 0, 1)); ?>

        </div>
        <h3 class="font-black text-primary text-lg"><?php echo e($user->name); ?></h3>
        <p class="text-xs text-tertiary mb-3"><?php echo e($user->title ?: $user->roleLabel()); ?></p>
        <div class="flex gap-2 justify-center mb-4 flex-wrap">
            <?php $__currentLoopData = ($user->roles ?: [$user->role]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="badge bg-primary/10 text-primary"><?php echo e(['idea_owner'=>'صاحب فكرة','idea_seeker'=>'باحث عن فكرة','developer'=>'باحث عن عمل'][$r] ?? $r); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($user->isKycApproved()): ?>
                <span class="badge bg-emerald-50 text-emerald-700">✓ KYC</span>
            <?php endif; ?>
        </div>
        <a href="<?php echo e(route('profile.cv')); ?>" class="btn-primary w-full text-sm">الملف / CV</a>
        <a href="<?php echo e(route('auth.path')); ?>" class="btn-outline w-full text-sm mt-2 block text-center">تغيير / إضافة مسار</a>
        <?php if($user->hasRole('idea_owner')): ?>
            <a href="<?php echo e(route('dashboard.ideaOwner')); ?>" class="btn-ghost w-full text-sm mt-1 block text-center">إدارة أفكاري ←</a>
            <a href="<?php echo e(route('dashboard.implementRequests')); ?>" class="btn-ghost w-full text-sm block text-center">طلبات التنفيذ (<?php echo e($stats['incoming'] ?? 0); ?>) ←</a>
        <?php endif; ?>
        <?php if($user->hasRole('developer') && ! $user->show_in_jobs_forum): ?>
            <a href="<?php echo e(route('verification.kyc', ['purpose'=>'jobs_forum'])); ?>" class="btn-secondary w-full text-sm mt-2 block text-center">الانضمام لمنتدى التوظيف</a>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="card p-5"><div class="text-xs text-tertiary mb-1">أفكاري</div><div class="text-3xl font-black text-primary"><?php echo e($stats['ideas']); ?></div></div>
        <div class="card p-5"><div class="text-xs text-tertiary mb-1">منشورة</div><div class="text-3xl font-black text-primary"><?php echo e($stats['published']); ?></div></div>
        <div class="card p-5"><div class="text-xs text-tertiary mb-1">إعجابات</div><div class="text-3xl font-black text-primary"><?php echo e($stats['likes']); ?></div></div>
        <div class="card p-5"><div class="text-xs text-tertiary mb-1">طلبات تنفيذ</div><div class="text-3xl font-black text-primary"><?php echo e($stats['implements']); ?></div></div>
    </div>
</div>

<div class="card p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-primary">أحدث أفكاري</h3>
        <a href="<?php echo e(route('ideas.create')); ?>" class="text-xs font-bold text-secondary">+ فكرة جديدة</a>
    </div>
    <?php $__empty_1 = true; $__currentLoopData = $myIdeas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idea): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="flex flex-wrap items-center gap-3 py-3 border-b border-mist last:border-0">
            <div class="flex-1 min-w-0">
                <a href="<?php echo e(route('ideas.show', $idea->id)); ?>" class="font-bold text-primary text-sm hover:text-secondary"><?php echo e($idea->title); ?></a>
                <div class="text-xs text-tertiary"><?php echo e($idea->category); ?> · <?php echo e($idea->created_at->diffForHumans()); ?></div>
                <?php if($idea->admin_notes && in_array($idea->status, ['draft', 'archived'], true)): ?>
                    <div class="text-xs text-rose-600 mt-1">ملاحظة الإدارة: <?php echo e($idea->admin_notes); ?></div>
                <?php endif; ?>
            </div>
            <div class="flex items-center gap-2">
                <?php if(in_array($idea->status, ['draft', 'archived'], true)): ?>
                    <form method="POST" action="<?php echo e(route('ideas.submit', $idea->id)); ?>"
                          onsubmit="return confirm('إرسال الفكرة للمراجعة الإدارية؟')">
                        <?php echo csrf_field(); ?>
                        <button class="btn-secondary text-xs !py-1.5 !px-3">إرسال للنشر</button>
                    </form>
                <?php endif; ?>
                <span class="badge <?php echo e([
                    'published'=>'bg-emerald-50 text-emerald-700',
                    'pending'=>'bg-amber-50 text-amber-700',
                    'draft'=>'bg-mist text-tertiary',
                    'archived'=>'bg-rose-50 text-rose-600',
                ][$idea->status] ?? 'bg-mist text-tertiary'); ?>">
                    <?php echo e(['published'=>'منشورة','pending'=>'قيد المراجعة','draft'=>'مسودة','archived'=>'مؤرشفة'][$idea->status] ?? $idea->status); ?>

                </span>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-sm text-tertiary text-center py-6">لا أفكار بعد. ابدأ من بنك الأفكار أو أنشئ فكرتك.</p>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/dashboards/home.blade.php ENDPATH**/ ?>