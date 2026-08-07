<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['idea']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['idea']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $isModel = $idea instanceof \App\Models\Idea;
    $id = $isModel ? $idea->id : ($idea['id'] ?? 0);
    $title = $isModel ? $idea->title : ($idea['title'] ?? '');
    $desc = $isModel ? $idea->shortDesc(110) : ($idea['desc'] ?? '');
    $category = $isModel ? $idea->category : ($idea['category'] ?? '');
    $author = $isModel ? ($idea->user->name ?? 'عضو') : ($idea['author'] ?? '');
    $likes = $isModel ? $idea->likes_count : ($idea['likes'] ?? 0);
    $comments = $isModel ? ($idea->comments_count ?? $idea->comments()->count()) : 0;
    $techs = $isModel ? (array) ($idea->technologies ?? []) : [];
    $featured = $isModel ? ($idea->likes_count >= 200) : ($idea['featured'] ?? false);
    $parent = $isModel ? $idea->parent : null;
    $status = $isModel ? $idea->status : 'published';
    $isFavorited = $isModel && auth()->check()
        ? (bool) ($idea->is_favorited ?? $idea->isFavoritedBy(auth()->user()))
        : false;
?>

<div class="card p-5 flex flex-col <?php echo e($featured ? 'bg-primary text-white border-primary' : 'bg-white'); ?>">
    <div class="flex items-center justify-between gap-2 mb-3">
        <div class="flex items-center gap-2 min-w-0">
            <div class="w-7 h-7 rounded-full <?php echo e($featured ? 'bg-white/20 text-white' : 'bg-primary/10 text-primary'); ?> grid place-items-center text-xs font-extrabold shrink-0">
                <?php echo e(mb_substr($author, 0, 1)); ?>

            </div>
            <span class="text-xs font-semibold truncate <?php echo e($featured ? 'text-white/80' : 'text-tertiary'); ?>"><?php echo e($author); ?></span>
        </div>
        <span class="badge shrink-0 <?php echo e($featured ? 'bg-white/15 text-white' : 'bg-neutral text-tertiary'); ?>"><?php echo e($category); ?></span>
    </div>

    <?php if($parent): ?>
        <div class="text-[11px] mb-2 px-2 py-1 rounded font-semibold <?php echo e($featured ? 'bg-white/10 text-white/90' : 'bg-secondary/10 text-secondary'); ?>">
            مستلهمة من «<?php echo e($parent->title); ?>»
        </div>
    <?php endif; ?>

    <?php if($status !== 'published'): ?>
        <span class="badge mb-2 <?php echo e($status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-mist text-tertiary'); ?>">
            <?php echo e(['draft'=>'مسودة','pending'=>'قيد المراجعة','archived'=>'مؤرشفة'][$status] ?? $status); ?>

        </span>
    <?php endif; ?>

    <h3 class="font-extrabold text-base mb-2 leading-snug <?php echo e($featured ? 'text-white' : 'text-primary'); ?> line-clamp-2">
        <a href="<?php echo e(route('ideas.show', $id)); ?>" class="hover:text-secondary"><?php echo e($title); ?></a>
    </h3>

    <p class="text-sm <?php echo e($featured ? 'text-white/75' : 'text-tertiary'); ?> mb-3 line-clamp-2 leading-relaxed flex-1">
        <?php echo e($desc); ?>

    </p>

    <?php if(count($techs)): ?>
        <div class="flex flex-wrap gap-1.5 mb-4">
            <?php $__currentLoopData = array_slice($techs, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tech): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="badge text-[10px] <?php echo e($featured ? 'bg-white/15 text-white' : 'bg-mist text-tertiary'); ?>"><?php echo e($tech); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <div class="flex items-center justify-between gap-2 pt-3 border-t <?php echo e($featured ? 'border-white/15' : 'border-mist'); ?> mt-auto">
        <a href="<?php echo e(route('ideas.show', $id)); ?>" class="text-xs font-bold <?php echo e($featured ? 'text-secondary' : 'text-secondary'); ?> hover:underline">
            عرض التفاصيل
        </a>

        <div class="flex items-center gap-3 text-xs font-semibold <?php echo e($featured ? 'text-white/70' : 'text-tertiary'); ?>">
            <span title="إعجابات">▲ <?php echo e(number_format($likes)); ?></span>
            <span title="تعليقات">💬 <?php echo e(number_format($comments)); ?></span>

            <?php if(auth()->guard()->check()): ?>
                <?php if($status === 'published'): ?>
                    <form method="POST" action="<?php echo e(route('ideas.favorite', $id)); ?>" class="inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit"
                                title="<?php echo e($isFavorited ? 'إزالة من المفضلة' : 'إضافة للمفضلة'); ?>"
                                class="transition <?php echo e($isFavorited ? 'text-secondary' : ($featured ? 'text-white/70 hover:text-secondary' : 'text-tertiary hover:text-secondary')); ?>">
                            <?php echo e($isFavorited ? '★' : '☆'); ?>

                        </button>
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <button type="button" title="المفضلة"
                        class="<?php echo e($featured ? 'text-white/70' : 'text-tertiary'); ?>"
                        onclick="window.dispatchEvent(new CustomEvent('open-gate',{detail:'احفظ الأفكار في المفضلة بعد تسجيل الدخول.'}))"
                        @click="gateOpen=true; gateMsg='احفظ الأفكار في المفضلة بعد تسجيل الدخول.'">
                    ☆
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/components/idea-card.blade.php ENDPATH**/ ?>