<?php $__env->startSection('title', $track['title'] . ' — التفاصيل'); ?>

<?php $__env->startSection('content'); ?>
<!-- Top Breadcrumb & Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <nav class="text-xs text-slate-500 font-bold mb-2 flex items-center gap-1.5">
            <a href="<?php echo e(route('career-tracks.index')); ?>" class="hover:text-primary transition-colors">المسارات المهنية</a>
            <span>‹</span>
            <span class="text-primary"><?php echo e($track['title']); ?></span>
        </nav>
        <h1 class="text-2xl sm:text-4xl font-black text-primary flex items-center gap-3">
            <span><?php echo e($track['title']); ?> - التفاصيل</span>
        </h1>
        <p class="text-sm text-slate-500 font-medium mt-1"><?php echo e($track['subtitle']); ?></p>
    </div>

    <!-- Status Badge -->
    <div class="shrink-0">
        <span class="px-4 py-2 rounded-xl text-sm font-extrabold shadow-sm <?php echo e($track['statusColor']); ?>">
            <?php echo e($track['statusLabel']); ?>

        </span>
    </div>
</div>

<?php if($track['needsAction']): ?>
<div class="bg-rose-50 border border-rose-200 rounded-2xl p-5 mb-8 flex items-start gap-4 shadow-sm animate-fade-in">
    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 grid place-items-center shrink-0 font-bold text-xl">⚠️</div>
    <div class="flex-1">
        <div class="font-extrabold text-rose-800 text-base">يتطلب اتخاذ إجراء (Needs Action)</div>
        <p class="text-sm text-rose-700 font-medium mt-0.5">تم مراجعة ملفك جزئياً، يرجى استكمال المستندات أو رابط الأعمال أدناه لتفعيل المسار.</p>
    </div>
</div>
<?php endif; ?>

<!-- Main Content Grid -->
<div class="grid lg:grid-cols-12 gap-8 items-start">
    
    <!-- Right/Center Timeline Block (9 cols) -->
    <div class="lg:col-span-8 space-y-6">
        
        <!-- Timeline Progress Card -->
        <div class="card p-6 sm:p-8 shadow-card">
            <h3 class="font-black text-xl text-primary mb-8 pb-4 border-b border-slate-100 flex items-center justify-between">
                <span>تقدم الطلب</span>
                <span class="text-xs font-bold text-slate-400">الخطوات الإجرائية</span>
            </h3>

            <div class="relative space-y-8 pr-2">
                <!-- Connecting Line -->
                <div class="absolute top-4 bottom-4 right-[21px] w-0.5 bg-slate-200 pointer-events-none"></div>

                <?php $__currentLoopData = $track['steps']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="relative flex items-start gap-5">
                    
                    <!-- Circle Icon Indicator -->
                    <div class="relative z-10 w-11 h-11 rounded-full grid place-items-center text-sm font-extrabold shrink-0 shadow-sm transition-transform hover:scale-110
                        <?php if($step['state'] === 'done'): ?> bg-blue-600 text-white ring-4 ring-blue-100
                        <?php elseif($step['state'] === 'current'): ?> bg-secondary text-white ring-4 ring-orange-100 animate-pulse
                        <?php else: ?> bg-slate-100 text-slate-400 border border-slate-200 <?php endif; ?>">
                        <span><?php echo e($step['icon']); ?></span>
                    </div>

                    <!-- Step Text Info -->
                    <div class="flex-1 bg-slate-50/70 border border-slate-100 rounded-2xl p-5 hover:bg-white hover:shadow-md transition-all duration-200">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-1.5">
                            <h4 class="font-extrabold text-primary text-base sm:text-lg"><?php echo e($step['title']); ?></h4>
                            <span class="badge text-xs font-bold
                                <?php if($step['state'] === 'done'): ?> bg-blue-50 text-blue-700 border border-blue-200
                                <?php elseif($step['state'] === 'current'): ?> bg-orange-50 text-secondary border border-orange-200
                                <?php else: ?> bg-slate-100 text-slate-500 border border-slate-200 <?php endif; ?>">
                                <?php echo e($step['stateLabel']); ?>

                            </span>
                        </div>
                        <p class="text-sm text-slate-600 font-medium leading-relaxed"><?php echo e($step['desc']); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <!-- Blue Reminder Info Note (from image 1) -->
        <div class="rounded-2xl bg-blue-50/80 border border-blue-200 p-5 text-sm text-blue-900 flex items-start gap-4 shadow-sm">
            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 grid place-items-center shrink-0 font-bold text-base">ℹ️</div>
            <div class="leading-relaxed font-medium">
                <span class="font-extrabold text-blue-950">تذكير أخير:</span> 
                تستغرق عملية المراجعة الدقيقة عادة ما يصل إلى <span class="font-extrabold text-secondary">48 ساعة</span> عمل لضمان جودة المسار التقديمي للمجتمع.
            </div>
        </div>

        <!-- Technical Action / Github form if needed -->
        <?php if($track['needsAction']): ?>
        <div class="card p-6 shadow-card">
            <h3 class="font-extrabold text-primary text-lg mb-4">استكمال بيانات الطلب</h3>
            <form action="<?php echo e(route('career-tracks.update', $track['slug'])); ?>" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm font-bold text-primary mb-1.5">رابط معرض الأعمال / GitHub / Pitch Deck</label>
                    <input type="url" name="github" placeholder="https://..." class="input" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-primary mb-1.5">ملاحظات إضافية (اختياري)</label>
                    <textarea rows="3" name="notes" class="input" placeholder="أي معلومات أو توضيحات إضافية..."></textarea>
                </div>
                <button type="submit" class="btn-primary text-sm !py-2.5">إرسال للتحديث</button>
            </form>
        </div>
        <?php endif; ?>

    </div>

    <!-- Sidebar Info Cards (4 cols) -->
    <div class="lg:col-span-4 space-y-6">
        
        <!-- Summary Card 1: ملخص الفكرة -->
        <div class="card p-6 shadow-card space-y-4">
            <h3 class="font-black text-lg text-primary pb-3 border-b border-slate-100 flex items-center gap-2">
                <span>📋</span> <span>ملخص الفكرة</span>
            </h3>

            <div class="space-y-3.5 text-sm">
                <div>
                    <div class="text-xs text-slate-400 font-bold mb-1">اسم المشروع</div>
                    <div class="font-extrabold text-primary text-base"><?php echo e($track['projectName'] ?? 'منصة إبداع للتقنيات العقارية'); ?></div>
                </div>

                <div>
                    <div class="text-xs text-slate-400 font-bold mb-1">التصنيف</div>
                    <span class="badge bg-slate-100 text-slate-700 font-bold border border-slate-200">
                        <?php echo e($track['projectCategory'] ?? 'PropTech'); ?>

                    </span>
                </div>

                <div>
                    <div class="text-xs text-slate-400 font-bold mb-1">تاريخ التقديم</div>
                    <div class="font-semibold text-slate-700"><?php echo e($track['submissionDate'] ?? '12 أكتوبر 2023'); ?></div>
                </div>
            </div>

            <div class="pt-2">
                <a href="<?php echo e(route('ideas.index')); ?>" class="btn-outline w-full text-center text-sm !py-2.5">
                    👁️ عرض الطلب كاملاً
                </a>
            </div>
        </div>

        <!-- Summary Card 2: تحتاج مساعدة؟ -->
        <div class="card p-6 shadow-card">
            <h3 class="font-black text-lg text-primary mb-2 flex items-center gap-2">
                <span>💬</span> <span>تحتاج مساعدة؟</span>
            </h3>
            <p class="text-xs sm:text-sm text-slate-500 font-medium leading-relaxed mb-4">
                تواصل مع مرشدي المسارات للاستفسار عن حالة طلبك أو للمساعدة التقنية.
            </p>
            <a href="<?php echo e(route('network.index')); ?>" class="text-secondary font-extrabold text-sm hover:underline inline-flex items-center gap-1">
                <span>التحدث مع الدعم الفني</span>
                <span>←</span>
            </a>
        </div>

    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\career-tracks\show.blade.php ENDPATH**/ ?>