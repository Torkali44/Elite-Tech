<?php $__env->startSection('title', isset($idea) && $idea ? 'تعديل الفكرة' : 'إضافة فكرة جديدة'); ?>
<?php $__env->startSection('content'); ?>
<?php
    $editing = isset($idea) && $idea;
    $parsed = $parsed ?? ['summary' => '', 'problem' => '', 'solution' => ''];
    $fromParent = $prefill ?? ($editing ? $idea->parent : null);
    $defaultBased = old('based_on_previous', ($fromParent || ($editing && $idea->forked_from)) ? 'yes' : 'no');
    $techDefaults = old('technologies', $editing ? ($idea->technologies ?? []) : ($fromParent->technologies ?? []));
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
     x-data="ideaForm({
        based: <?php echo \Illuminate\Support\Js::from($defaultBased)->toHtml() ?>,
        techs: <?php echo \Illuminate\Support\Js::from(array_values($techDefaults ?: []))->toHtml() ?>,
        parentId: <?php echo \Illuminate\Support\Js::from(old('parent_id', $fromParent->id ?? ($editing ? $idea->forked_from : null)))->toHtml() ?>
     })">

    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-black text-primary mb-2"><?php echo e($editing ? 'تعديل الفكرة' : 'إضافة فكرة جديدة'); ?></h1>
        <p class="text-tertiary text-sm leading-relaxed">
            المسار: صياغة كمسودة → إرسال للمراجعة الإدارية → نشر بعد القبول.
            الاستنساخ من فكرة سابقة يحفظ حقوق صاحبها بشارة واضحة.
        </p>
    </div>

    <?php if($errors->any()): ?>
        <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 space-y-1">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div><?php echo e($error); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <?php if($editing && $idea->admin_notes && $idea->status === 'draft'): ?>
        <div class="mb-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm px-4 py-3">
            <b>أُعيدت من الإدارة:</b> <?php echo e($idea->admin_notes); ?>

        </div>
    <?php endif; ?>

    <form action="<?php echo e($editing ? route('ideas.update', $idea->id) : route('ideas.store')); ?>" method="POST" class="card p-6 lg:p-8 space-y-6">
        <?php echo csrf_field(); ?>
        <?php if($editing): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

        
        <div class="rounded-2xl border border-mist bg-neutral/60 p-5 space-y-4">
            <h3 class="font-black text-primary">هل الفكرة مبنية على فكرة سابقة؟</h3>
            <div class="flex flex-wrap gap-3">
                <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border cursor-pointer"
                       :class="based==='no' ? 'border-primary bg-primary/5' : 'border-mist'">
                    <input type="radio" name="based_on_previous" value="no" x-model="based" class="accent-primary">
                    <span class="text-sm font-bold text-primary">لا — فكرة أصلية مستقلة</span>
                </label>
                <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border cursor-pointer"
                       :class="based==='yes' ? 'border-secondary bg-secondary/5' : 'border-mist'">
                    <input type="radio" name="based_on_previous" value="yes" x-model="based" class="accent-secondary">
                    <span class="text-sm font-bold text-primary">نعم — مستلهمة / تطوير (Fork)</span>
                </label>
            </div>

            <div x-show="based==='yes'" x-cloak class="space-y-2">
                <label class="block text-sm font-bold text-primary">الفكرة الأصلية (Parent)</label>
                <select name="parent_id" class="input" x-model="parentId" @change="applyParent()">
                    <option value="">اختر من بنك الأفكار المنشور...</option>
                    <?php $__currentLoopData = $parents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p->id); ?>"
                                data-title="<?php echo e($p->title); ?>"
                                data-category="<?php echo e($p->category); ?>"
                                data-desc="<?php echo e(e(\Illuminate\Support\Str::limit($p->description, 800))); ?>"
                                data-techs="<?php echo e(e(json_encode($p->technologies ?? []))); ?>"
                                data-feasibility="<?php echo e(e($p->feasibility ?? '')); ?>"
                                data-author="<?php echo e(e($p->user->name ?? '')); ?>">
                            #<?php echo e($p->id); ?> — <?php echo e($p->title); ?> (<?php echo e($p->user->name ?? 'عضو'); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <p class="text-xs text-tertiary">سيتم ربط السجل بـ parent_id وعرض شارة: مستلهمة من فكرة صاحبها الأصلي.</p>
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-primary mb-1.5">عنوان الفكرة *</label>
            <input type="text" name="title" x-ref="title" value="<?php echo e(old('title', $editing ? $idea->title : ($fromParent ? 'تطوير: '.$fromParent->title : ''))); ?>" class="input" maxlength="120" required>
        </div>

        <div>
            <label class="block text-sm font-bold text-primary mb-1.5">وصف مختصر *</label>
            <textarea name="summary" rows="2" class="input" maxlength="300" required placeholder="ملخص واضح للفكرة..."><?php echo e(old('summary', $editing ? ($parsed['summary'] ?? '') : '')); ?></textarea>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-primary mb-1.5">المشكلة *</label>
                <textarea name="problem" rows="5" class="input" required minlength="20" placeholder="ما الفجوة أو الألم الذي تعالجه؟"><?php echo e(old('problem', $editing ? ($parsed['problem'] ?? '') : '')); ?></textarea>
            </div>
            <div>
                <label class="block text-sm font-bold text-primary mb-1.5">الحل المقترح *</label>
                <textarea name="solution" rows="5" class="input" required minlength="20" placeholder="كيف تحل الفكرة المشكلة؟"><?php echo e(old('solution', $editing ? ($parsed['solution'] ?? '') : '')); ?></textarea>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-primary mb-1.5">الفئة *</label>
                <select name="category" class="input" x-ref="category" required>
                    <option value="">اختر الفئة</option>
                    <?php $__currentLoopData = ['الذكاء الاصطناعي','الأمن السيبراني','تطوير الويب','Blockchain','تطبيقات الجوال','أخرى']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat); ?>" <?php if(old('category', $editing ? $idea->category : ($fromParent->category ?? ''))==$cat): echo 'selected'; endif; ?>><?php echo e($cat); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-primary mb-1.5">الميزانية التقديرية (اختياري)</label>
                <input type="number" min="0" step="0.01" name="budget" value="<?php echo e(old('budget', $editing ? $idea->budget : '')); ?>" class="input" placeholder="0.00">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-primary mb-1.5">المتطلبات التقنية *</label>
            <div class="flex flex-wrap gap-2 mb-2">
                <template x-for="(tech, i) in techs" :key="tech+'-'+i">
                    <span class="badge bg-primary/10 text-primary">
                        <span x-text="tech"></span>
                        <button type="button" @click="techs.splice(i,1)" class="mr-1">✕</button>
                        <input type="hidden" name="technologies[]" :value="tech">
                    </span>
                </template>
            </div>
            <input type="text" x-model="techInput" @keydown.enter.prevent="addTech" class="input" placeholder="أضف تقنية ثم Enter — مثال: Laravel">
            <p class="text-xs text-tertiary mt-1">معايير القبول تتطلب وضوح المتطلبات التقنية.</p>
        </div>

        <div>
            <label class="block text-sm font-bold text-primary mb-1.5">دراسة جدوى مبسطة</label>
            <textarea name="feasibility" rows="3" class="input" x-ref="feasibility"><?php echo e(old('feasibility', $editing ? $idea->feasibility : ($fromParent->feasibility ?? ''))); ?></textarea>
        </div>

        <?php if (! ($editing)): ?>
        <label class="flex items-start gap-3 text-sm p-4 rounded-xl bg-mist cursor-pointer">
            <input type="checkbox" name="ip_agreement" value="1" class="mt-1 accent-primary" required>
            <span class="text-tertiary leading-relaxed">
                أوافق على <a href="<?php echo e(route('agreement')); ?>" target="_blank" class="underline text-primary">اتفاقية الاستخدام</a>
                وآلية استنساخ الأفكار (Forking)، وأدرك أن نشر الفكرة يحفظ الحقوق الأدبية لصاحب الفكرة الأصلية.
            </span>
        </label>
        <?php endif; ?>

        <div class="flex flex-wrap gap-3 pt-2 border-t border-mist">
            <button type="submit" name="intent" value="draft" class="btn-outline">حفظ كمسودة</button>
            <button type="submit" name="intent" value="pending" class="btn-secondary">إرسال للمراجعة الإدارية</button>
            <a href="<?php echo e(route('dashboard')); ?>" class="btn-ghost self-center">إلغاء</a>
        </div>
    </form>
</div>

<script>
function ideaForm(init) {
    return {
        based: init.based || 'no',
        techs: Array.isArray(init.techs) ? init.techs : [],
        parentId: init.parentId ? String(init.parentId) : '',
        techInput: '',
        addTech() {
            const v = this.techInput.replace(',', '').trim();
            if (v && !this.techs.includes(v)) this.techs.push(v);
            this.techInput = '';
        },
        applyParent() {
            const sel = this.$el.querySelector('select[name=parent_id]');
            const opt = sel?.selectedOptions?.[0];
            if (!opt || !opt.value) return;
            const title = opt.dataset.title || '';
            if (this.$refs.title && (!this.$refs.title.value || this.$refs.title.value.startsWith('تطوير:'))) {
                this.$refs.title.value = 'تطوير: ' + title;
            }
            if (this.$refs.category && opt.dataset.category) {
                this.$refs.category.value = opt.dataset.category;
            }
            try {
                const techs = JSON.parse(opt.dataset.techs || '[]');
                if (Array.isArray(techs) && techs.length) this.techs = techs;
            } catch (e) {}
            if (this.$refs.feasibility && opt.dataset.feasibility) {
                this.$refs.feasibility.value = opt.dataset.feasibility;
            }
        }
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\ideas\create.blade.php ENDPATH**/ ?>