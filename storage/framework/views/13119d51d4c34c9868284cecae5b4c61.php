<?php $__env->startSection('title', 'تفاصيل الفكرة: ' . $idea->title); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-3">
        <a href="<?php echo e(route('admin.ideas')); ?>" class="btn-ghost text-sm flex items-center gap-1">
            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            عودة لقائمة الأفكار
        </a>
        <h1 class="text-2xl font-black text-primary">تفاصيل الفكرة #<?php echo e($idea->id); ?></h1>
    </div>
    <div class="flex items-center gap-2">
        <?php if($idea->status === 'published'): ?>
            <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">منشورة للعامة</span>
        <?php elseif($idea->status === 'draft'): ?>
            <span class="px-3 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200">مسودة</span>
        <?php elseif($idea->status === 'pending'): ?>
            <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-200">قيد المراجعة</span>
        <?php else: ?>
            <span class="px-3 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-800"><?php echo e($idea->status); ?></span>
        <?php endif; ?>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <!-- Main Idea Details -->
    <div class="lg:col-span-2 space-y-6">
        <div class="card p-6 space-y-4">
            <div>
                <span class="text-xs font-bold text-secondary uppercase tracking-wider"><?php echo e($idea->category ?? 'عام'); ?></span>
                <h2 class="text-2xl font-extrabold text-primary mt-1"><?php echo e($idea->title); ?></h2>
                <div class="text-xs text-tertiary mt-1">تاريخ الإنشاء: <?php echo e($idea->created_at->format('Y-m-d H:i')); ?> (<?php echo e($idea->created_at->diffForHumans()); ?>)</div>
            </div>

            <div class="border-t border-mist pt-4">
                <h3 class="font-bold text-primary mb-2">وصف الفكرة</h3>
                <div class="text-sm text-tertiary whitespace-pre-line leading-relaxed"><?php echo e($idea->description); ?></div>
            </div>

            <?php if($idea->target_audience): ?>
            <div class="border-t border-mist pt-4">
                <h3 class="font-bold text-primary mb-1 text-sm">الجمهور المستهدف</h3>
                <p class="text-sm text-tertiary"><?php echo e($idea->target_audience); ?></p>
            </div>
            <?php endif; ?>

            <?php if($idea->budget_range): ?>
            <div class="border-t border-mist pt-4">
                <h3 class="font-bold text-primary mb-1 text-sm">الميزانية التقديرية</h3>
                <p class="text-sm text-tertiary"><?php echo e($idea->budget_range); ?></p>
            </div>
            <?php endif; ?>

            <?php if($idea->execution_plan): ?>
            <div class="border-t border-mist pt-4">
                <h3 class="font-bold text-primary mb-1 text-sm">خطة التنفيذ / المتطلبات</h3>
                <div class="text-sm text-tertiary whitespace-pre-line"><?php echo e($idea->execution_plan); ?></div>
            </div>
            <?php endif; ?>

            <?php if($idea->admin_notes): ?>
            <div class="border-t border-mist pt-4 bg-amber-50/50 p-3 rounded-lg border border-amber-200">
                <h3 class="font-bold text-amber-900 mb-1 text-xs">ملاحظات الإدارة المسجلة</h3>
                <p class="text-xs text-amber-800"><?php echo e($idea->admin_notes); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Implementation Requests -->
        <div class="card p-6">
            <h3 class="font-bold text-primary mb-4 flex items-center justify-between">
                <span>طلبات التنفيذ المرفوقة</span>
                <span class="badge bg-mist text-primary"><?php echo e($idea->implementRequests->count()); ?></span>
            </h3>
            <div class="space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = $idea->implementRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="p-3 rounded-lg border border-mist flex items-center justify-between">
                    <div>
                        <div class="font-bold text-sm text-primary"><?php echo e($req->user->name ?? 'مستخدم'); ?></div>
                        <div class="text-xs text-tertiary"><?php echo e($req->user->email ?? ''); ?> · <?php echo e($req->created_at->diffForHumans()); ?></div>
                    </div>
                    <a href="<?php echo e(route('admin.implementations.show', $req->id)); ?>" class="text-xs font-bold text-secondary underline">عرض الطلب</a>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-xs text-tertiary">لا توجد طلبات تنفيذ لهذه الفكرة بعد.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Comments -->
        <div class="card p-6">
            <h3 class="font-bold text-primary mb-4">التعليقات (<?php echo e($idea->comments->count()); ?>)</h3>
            <div class="space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $idea->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="p-3 rounded-lg bg-neutral border border-mist flex justify-between items-start">
                    <div>
                        <div class="font-bold text-xs text-primary"><?php echo e($comment->user->name ?? 'مستخدم'); ?></div>
                        <p class="text-xs text-tertiary mt-1 whitespace-pre-line"><?php echo e($comment->content); ?></p>
                        <div class="text-[10px] text-tertiary mt-1"><?php echo e($comment->created_at->diffForHumans()); ?></div>
                    </div>
                    <form method="POST" action="<?php echo e(route('admin.comments.delete', $comment->id)); ?>" onsubmit="return confirm('حذف التعليق؟')">
                        <?php echo csrf_field(); ?>
                        <button class="text-[11px] text-rose-600 font-bold hover:underline">حذف</button>
                    </form>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-xs text-tertiary">لا تعليقات.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar: User Info & Actions -->
    <div class="space-y-6">
        <!-- User Details Card -->
        <div class="card p-6 space-y-4">
            <h3 class="font-bold text-primary pb-2 border-b border-mist">بيانات صاحب الفكرة</h3>
            <?php if($idea->user): ?>
            <div class="space-y-3 text-sm">
                <div>
                    <div class="text-xs text-tertiary">الاسم</div>
                    <div class="font-bold text-primary"><?php echo e($idea->user->name); ?></div>
                </div>
                <div>
                    <text class="text-xs text-tertiary">البريد الإلكتروني</text>
                    <div class="font-medium text-primary"><?php echo e($idea->user->email); ?></div>
                </div>
                <div>
                    <div class="text-xs text-tertiary mb-1">المسار / الدور</div>
                    <span class="badge bg-mist text-primary"><?php echo e($idea->user->roleLabel()); ?></span>
                </div>
                <div>
                    <div class="text-xs text-tertiary mb-1">حالة توثيق KYC</div>
                    <?php if($idea->user->isKycApproved()): ?>
                        <span class="badge bg-emerald-100 text-emerald-800">موثّق</span>
                    <?php else: ?>
                        <span class="badge bg-amber-100 text-amber-800"><?php echo e($idea->user->kyc_status); ?></span>
                    <?php endif; ?>
                </div>
                <div class="pt-2">
                    <a href="<?php echo e(route('profile.show', $idea->user->id)); ?>" target="_blank" class="btn-outline text-xs w-full text-center block !py-2">
                        معاينة الملف الشخصي ↗
                    </a>
                </div>
            </div>
            <?php else: ?>
            <p class="text-xs text-tertiary">صاحب الفكرة غير متاح.</p>
            <?php endif; ?>
        </div>

        <!-- Admin Actions Box -->
        <div class="card p-6 space-y-4" x-data="{ ret: false }">
            <h3 class="font-bold text-primary pb-2 border-b border-mist">إجراءات الأدمن</h3>

            <?php if($idea->status !== 'published'): ?>
            <form method="POST" action="<?php echo e(route('admin.ideas.publish', $idea->id)); ?>">
                <?php echo csrf_field(); ?>
                <button class="btn-primary w-full text-sm">نشر الفكرة للعامة</button>
            </form>
            <?php endif; ?>

            <button type="button" @click="ret = !ret" class="btn-outline w-full text-sm">
                إرجاع كمسودة مع ملاحظة
            </button>

            <form x-show="ret" x-cloak method="POST" action="<?php echo e(route('admin.ideas.return', $idea->id)); ?>" class="space-y-3 pt-2">
                <?php echo csrf_field(); ?>
                <textarea name="note" rows="3" class="input text-xs" placeholder="أدخل سبب الإرجاع للمستخدم..." required></textarea>
                <button class="btn-secondary w-full text-xs !py-2">تأكيد الإرجاع</button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/admin/ideas_show.blade.php ENDPATH**/ ?>