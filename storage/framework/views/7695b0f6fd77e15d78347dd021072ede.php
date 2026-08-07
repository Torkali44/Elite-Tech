<?php $__env->startSection('title', __('network.title')); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="text-2xl lg:text-3xl font-black text-primary mb-1"><?php echo e(__('network.title')); ?></h1>
        <p class="text-tertiary text-sm"><?php echo e(__('network.subtitle')); ?></p>
    </div>
    <div class="flex bg-mist rounded-xl p-1 text-sm font-bold">
        <a href="<?php echo e(route('network.index', ['tab' => 'inbox'])); ?>"
           class="px-4 py-2 rounded-lg <?php echo e($tab==='inbox' ? 'bg-white shadow-soft text-primary' : 'text-tertiary'); ?>"><?php echo e(__('network.inbox')); ?></a>
        <a href="<?php echo e(route('network.index', ['tab' => 'archive'])); ?>"
           class="px-4 py-2 rounded-lg <?php echo e($tab==='archive' ? 'bg-white shadow-soft text-primary' : 'text-tertiary'); ?>"><?php echo e(__('network.archive')); ?></a>
    </div>
</div>

<div class="grid lg:grid-cols-[300px_1fr_260px] gap-5">
    
    <div class="card overflow-hidden max-h-[70vh] overflow-y-auto">
        <?php $__empty_1 = true; $__currentLoopData = $threads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a href="<?php echo e(route('network.index', ['tab' => $tab, 'with' => $t['id']])); ?>"
               class="block p-4 border-b border-mist last:border-0 hover:bg-neutral transition <?php echo e($withId === $t['id'] ? 'bg-primary/5 border-r-4 border-r-primary' : ''); ?>">
                <div class="flex items-center justify-between gap-2 mb-1">
                    <span class="font-bold text-primary text-sm truncate"><?php echo e($t['partner']->name); ?></span>
                    <span class="text-[10px] text-tertiary shrink-0"><?php echo e($t['time']->diffForHumans()); ?></span>
                </div>
                <div class="text-[11px] text-tertiary mb-1"><?php echo e($t['partner']->title ?: $t['partner']->roleLabel()); ?></div>
                <p class="text-xs text-tertiary line-clamp-1"><?php echo e($t['preview']); ?></p>
                <?php if($t['unread'] > 0): ?>
                    <span class="badge bg-secondary text-white mt-2"><?php echo e($t['unread']); ?> <?php echo e(__('network.new_messages')); ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="p-8 text-center text-sm text-tertiary"><?php echo e(__('network.no_threads_in_section')); ?></div>
        <?php endif; ?>
    </div>

    
    <div class="card flex flex-col min-h-[420px] max-h-[70vh]">
        <?php if($activePartner): ?>
            <div class="flex items-center justify-between border-b border-mist p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary text-white grid place-items-center font-bold">
                        <?php echo e(mb_substr($activePartner->name, 0, 1)); ?>

                    </div>
                    <div>
                        <div class="font-bold text-primary"><?php echo e($activePartner->name); ?></div>
                        <div class="text-xs text-tertiary"><?php echo e($activePartner->title ?: $activePartner->roleLabel()); ?></div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="<?php echo e(route('profile.show', $activePartner->id)); ?>" class="btn-ghost text-xs"><?php echo e(__('network.view_profile')); ?></a>
                    <?php if($tab !== 'archive'): ?>
                    <form action="<?php echo e(route('network.archive', $activePartner->id)); ?>" method="POST"><?php echo csrf_field(); ?>
                        <button class="btn-outline text-xs !py-1.5"><?php echo e(__('network.archive_btn')); ?></button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-neutral/40">
                <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $mine = $m->sender_id === auth()->id(); ?>
                    <div class="flex <?php echo e($mine ? 'justify-start' : 'justify-end'); ?>">
                        <div class="max-w-[80%] rounded-2xl px-4 py-3 text-sm leading-relaxed <?php echo e($mine ? 'bg-primary text-white rounded-tr-sm' : 'bg-white border border-mist text-tertiary rounded-tl-sm'); ?>">
                            <?php echo e($m->body); ?>

                            <div class="text-[10px] mt-1 <?php echo e($mine ? 'text-white/60' : 'text-slate-400'); ?>"><?php echo e($m->created_at->format('Y-m-d H:i')); ?></div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-center text-sm text-tertiary py-10"><?php echo e(__('network.start_first_message')); ?></p>
                <?php endif; ?>
            </div>

            <form action="<?php echo e(route('network.reply', $activePartner->id)); ?>" method="POST" class="p-4 border-t border-mist flex gap-2">
                <?php echo csrf_field(); ?>
                <textarea name="body" rows="2" class="input flex-1" placeholder="<?php echo e(__('network.type_message')); ?>" required></textarea>
                <button class="btn-secondary self-end !py-2"><?php echo e(__('network.send_message')); ?></button>
            </form>
        <?php else: ?>
            <div class="flex-1 grid place-items-center p-8 text-center text-tertiary text-sm">
                <?php echo e(__('network.choose_conversation')); ?>

            </div>
        <?php endif; ?>
    </div>

    
    <div class="card p-4 max-h-[70vh] overflow-y-auto">
        <h3 class="font-bold text-primary mb-3 text-sm"><?php echo e(__('network.new_conversation_title')); ?></h3>
        <form action="<?php echo e(route('network.start')); ?>" method="POST" class="space-y-3 mb-4">
            <?php echo csrf_field(); ?>
            <select name="recipient_id" class="input text-sm" required>
                <option value=""><?php echo e(__('network.choose_member')); ?></option>
                <?php $__currentLoopData = $directory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?> — <?php echo e($u->roleLabel()); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <textarea name="body" rows="3" class="input text-sm" placeholder="<?php echo e(__('network.first_message_placeholder')); ?>" required></textarea>
            <button class="btn-primary w-full text-sm !py-2"><?php echo e(__('network.send_btn')); ?></button>
        </form>
        <p class="text-[11px] text-tertiary leading-relaxed"><?php echo e(__('network.directory_hint')); ?></p>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\network\index.blade.php ENDPATH**/ ?>