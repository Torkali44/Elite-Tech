<?php $__env->startSection('title', $idea->title); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <nav class="text-xs text-tertiary mb-4">
        <a href="<?php echo e(route('ideas.index')); ?>" class="hover:text-primary">بنك الأفكار</a>
        <span class="mx-1">›</span>
        <span><?php echo e($idea->category); ?></span>
    </nav>

    <div class="grid lg:grid-cols-[1fr_300px] gap-6">
        <article class="space-y-6">
            <div class="card p-6 lg:p-8">
                <div class="flex items-center gap-2 mb-4 flex-wrap">
                    <span class="badge bg-primary/10 text-primary"><?php echo e($idea->category); ?></span>
                    <span class="text-xs text-tertiary"><?php echo e($idea->created_at->diffForHumans()); ?></span>
                    <?php if($idea->parent): ?>
                        <div class="w-full rounded-xl bg-secondary/10 border border-secondary/20 text-sm text-primary px-4 py-3 leading-relaxed">
                            <span class="font-black text-secondary">شارة الاستنساخ:</span>
                            هذه الفكرة مستلهمة/مبنية على فكرة
                            «<a href="<?php echo e(route('ideas.show', $idea->parent->id)); ?>" class="font-bold underline"><?php echo e($idea->parent->title); ?></a>»
                            لصاحبها <b><?php echo e($idea->parent->user->name ?? 'عضو'); ?></b>
                            — حفظاً للتقدير الأدبي والشفافية.
                        </div>
                    <?php endif; ?>
                </div>
                <h1 class="text-3xl font-black text-primary mb-6 leading-tight"><?php echo e($idea->title); ?></h1>
                <div class="prose max-w-none text-tertiary leading-relaxed whitespace-pre-line text-sm"><?php echo e($idea->description); ?></div>

                <?php if($idea->technologies): ?>
                    <h3 class="font-bold text-primary mt-8 mb-3">التقنيات المقترحة</h3>
                    <div class="flex flex-wrap gap-2">
                        <?php $__currentLoopData = $idea->technologies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tech): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="badge bg-mist text-primary border border-slate-200"><?php echo e($tech); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if($idea->feasibility): ?>
            <div class="card p-6">
                <h3 class="font-bold text-primary mb-3">دراسة جدوى مبسطة</h3>
                <p class="text-sm text-tertiary leading-relaxed whitespace-pre-line"><?php echo e($idea->feasibility); ?></p>
            </div>
            <?php endif; ?>

            <div class="card p-6" id="comments">
                <h3 class="font-bold text-primary mb-4">النقاشات (<?php echo e($idea->comments->count()); ?>)</h3>
                <?php if(auth()->guard()->check()): ?>
                    <form method="POST" action="<?php echo e(route('ideas.comment', $idea->id)); ?>" class="mb-6">
                        <?php echo csrf_field(); ?>
                        <textarea name="body" rows="2" placeholder="أضف رؤيتك للنقاش..." class="input" required></textarea>
                        <button type="submit" class="btn-primary mt-2 text-sm !py-2">إرسال التعليق</button>
                    </form>
                <?php else: ?>
                    <button type="button" class="w-full text-center text-sm bg-mist rounded-xl py-4 mb-6 text-primary font-semibold hover:bg-slate-100"
                            @click="gateOpen=true; gateMsg='التعليق على الأفكار يتطلب إنشاء حساب.'">
                        سجّل الدخول للمشاركة في النقاش
                    </button>
                <?php endif; ?>

                <div class="space-y-4">
                    <?php $__empty_1 = true; $__currentLoopData = $idea->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-secondary/15 shrink-0 grid place-items-center font-bold text-secondary text-sm">
                                <?php echo e(mb_substr($c->user->name ?? '?', 0, 1)); ?>

                            </div>
                            <div class="flex-1 bg-mist rounded-xl p-4">
                                <div class="flex items-center justify-between mb-1 gap-2 flex-wrap">
                                    <span class="font-bold text-primary text-sm"><?php echo e($c->user->name ?? 'عضو'); ?></span>
                                    <span class="text-xs text-tertiary"><?php echo e($c->created_at->diffForHumans()); ?></span>
                                </div>
                                <p class="text-sm text-tertiary leading-relaxed"><?php echo e($c->body); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-sm text-tertiary text-center py-4">لا تعليقات بعد — كن أول من يشارك رأيه.</p>
                    <?php endif; ?>
                </div>
            </div>
        </article>

        <aside class="space-y-4">
            <div class="card p-5 space-y-2">
                <?php if(auth()->guard()->check()): ?>
                    <?php if($userRequest): ?>
                        <div class="badge bg-amber-50 text-amber-700 mb-1">طلب تنفيذ: <?php echo e($userRequest->status); ?></div>
                    <?php endif; ?>

                    <?php if($idea->status === 'published'): ?>
                        <?php if(auth()->user()->isKycApproved()): ?>
                            <a href="<?php echo e(route('ideas.implement.form', $idea->id)); ?>" class="btn-secondary w-full text-center block">رغبة في التنفيذ</a>
                        <?php else: ?>
                            <a href="<?php echo e(route('verification.kyc', ['purpose'=>'implement','idea'=>$idea->id])); ?>" class="btn-secondary w-full text-center block">
                                رغبة في التنفيذ → KYC
                            </a>
                            <p class="text-[11px] text-tertiary text-center leading-relaxed">يتطلب اجتياز التحقق لضمان الجدية.</p>
                        <?php endif; ?>

                        <a href="<?php echo e(route('ideas.fork.confirm', $idea->id)); ?>" class="btn-outline w-full text-sm text-center block">تطوير الفكرة (Fork)</a>

                        <form method="POST" action="<?php echo e(route('ideas.favorite', $idea->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <button class="btn-ghost w-full text-sm <?php echo e(!empty($isFavorited) ? 'text-secondary font-bold' : ''); ?>">
                                <?php echo e(!empty($isFavorited) ? '★ إزالة من المفضلة' : '☆ أضف للمفضلة'); ?>

                            </button>
                        </form>
                    <?php else: ?>
                        <div class="rounded-lg bg-neutral text-tertiary text-sm p-3 text-center space-y-3">
                            <p>هذه الفكرة غير منشورة بعد — التنفيذ والتعليق بعد النشر.</p>
                            <?php if(auth()->id() === $idea->user_id && in_array($idea->status, ['draft', 'archived'], true)): ?>
                                <a href="<?php echo e(route('ideas.edit', $idea->id)); ?>" class="btn-outline w-full text-sm block">تعديل المسودة</a>
                                <form method="POST" action="<?php echo e(route('ideas.submit', $idea->id)); ?>"
                                      onsubmit="return confirm('إرسال الفكرة للمراجعة الإدارية؟')">
                                    <?php echo csrf_field(); ?>
                                    <button class="btn-secondary w-full text-sm">إرسال للنشر</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <button type="button" class="btn-secondary w-full"
                            @click="gateOpen=true; gateMsg='الرغبة في التنفيذ تتطلب حساباً ثم اجتياز KYC.'">
                        رغبة في التنفيذ
                    </button>
                    <a href="<?php echo e(route('register')); ?>" class="btn-outline w-full text-center block text-sm">إنشاء حساب</a>
                <?php endif; ?>

                <div class="grid grid-cols-2 gap-2 mt-2 text-center text-xs">
                    <div class="bg-mist rounded-lg p-2">
                        <div class="font-extrabold text-primary text-lg"><?php echo e($idea->budget ? number_format($idea->budget).'$' : '—'); ?></div>
                        <div class="text-tertiary">الميزانية</div>
                    </div>
                    <div class="bg-mist rounded-lg p-2">
                        <div class="font-extrabold text-primary text-lg"><?php echo e($idea->likes_count); ?></div>
                        <div class="text-tertiary">إعجاب</div>
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <div class="text-xs text-tertiary mb-2">صاحب الفكرة</div>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-primary/10 grid place-items-center font-bold text-primary">
                        <?php echo e(mb_substr($idea->user->name ?? '?', 0, 1)); ?>

                    </div>
                    <div>
                        <div class="font-bold text-primary"><?php echo e($idea->user->name ?? 'عضو'); ?></div>
                        <div class="text-xs text-tertiary"><?php echo e($idea->user->title ?? $idea->user->roleLabel()); ?></div>
                    </div>
                </div>
                <?php if($idea->user): ?>
                    <a href="<?php echo e(route('profile.show', $idea->user->id)); ?>" class="block text-center text-xs text-primary font-bold mt-3 hover:underline">عرض الملف الشخصي ←</a>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/ideas/show.blade.php ENDPATH**/ ?>