@extends('layouts.app')
@section('title', isset($idea) && $idea ? 'تعديل الفكرة' : 'إضافة فكرة جديدة')
@section('content')
@php
    $editing = isset($idea) && $idea;
    $parsed = $parsed ?? ['summary' => '', 'problem' => '', 'solution' => ''];
    $fromParent = $prefill ?? ($editing ? $idea->parent : null);
    $defaultBased = old('based_on_previous', ($fromParent || ($editing && $idea->forked_from)) ? 'yes' : 'no');
    $techDefaults = old('technologies', $editing ? ($idea->technologies ?? []) : ($fromParent->technologies ?? []));
@endphp

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
     x-data="ideaForm({
        based: @js($defaultBased),
        techs: @js(array_values($techDefaults ?: [])),
        parentId: @js(old('parent_id', $fromParent->id ?? ($editing ? $idea->forked_from : null)))
     })">

    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-black text-primary mb-2">{{ $editing ? 'تعديل الفكرة' : 'إضافة فكرة جديدة' }}</h1>
        <p class="text-tertiary text-sm leading-relaxed">
            المسار: صياغة كمسودة → إرسال للمراجعة الإدارية → نشر بعد القبول.
            الاستنساخ من فكرة سابقة يحفظ حقوق صاحبها بشارة واضحة.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 space-y-1">
            @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    @if($editing && $idea->admin_notes && $idea->status === 'draft')
        <div class="mb-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm px-4 py-3">
            <b>أُعيدت من الإدارة:</b> {{ $idea->admin_notes }}
        </div>
    @endif

    <form action="{{ $editing ? route('ideas.update', $idea->id) : route('ideas.store') }}" method="POST" class="card p-6 lg:p-8 space-y-6" @submit="submitForm($event)">
        @csrf
        @if($editing) @method('PUT') @endif

        {{-- Step: original vs fork --}}
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
                    @foreach($parents as $p)
                        <option value="{{ $p->id }}"
                                data-title="{{ $p->title }}"
                                data-category="{{ $p->category }}"
                                data-desc="{{ e(\Illuminate\Support\Str::limit($p->description, 800)) }}"
                                data-techs="{{ e(json_encode($p->technologies ?? [])) }}"
                                data-feasibility="{{ e($p->feasibility ?? '') }}"
                                data-author="{{ e($p->user->name ?? '') }}">
                            #{{ $p->id }} — {{ $p->title }} ({{ $p->user->name ?? 'عضو' }})
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-tertiary">سيتم ربط السجل بـ parent_id وعرض شارة: مستلهمة من فكرة صاحبها الأصلي.</p>
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-primary mb-1.5">عنوان الفكرة *</label>
            <input type="text" name="title" x-ref="title" value="{{ old('title', $editing ? $idea->title : ($fromParent ? 'تطوير: '.$fromParent->title : '')) }}" class="input" maxlength="120" required>
        </div>

        <div>
            <label class="block text-sm font-bold text-primary mb-1.5">وصف مختصر *</label>
            <textarea name="summary" rows="2" class="input" maxlength="300" required placeholder="ملخص واضح للفكرة...">{{ old('summary', $editing ? ($parsed['summary'] ?? '') : '') }}</textarea>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-primary mb-1.5">المشكلة *</label>
                <textarea name="problem" rows="5" class="input" required minlength="20" placeholder="ما الفجوة أو الألم الذي تعالجه؟">{{ old('problem', $editing ? ($parsed['problem'] ?? '') : '') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-bold text-primary mb-1.5">الحل المقترح *</label>
                <textarea name="solution" rows="5" class="input" required minlength="20" placeholder="كيف تحل الفكرة المشكلة؟">{{ old('solution', $editing ? ($parsed['solution'] ?? '') : '') }}</textarea>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-primary mb-1.5">الفئة *</label>
                <select name="category" class="input" x-ref="category" required>
                    <option value="">اختر الفئة</option>
                    @foreach(['الذكاء الاصطناعي','الأمن السيبراني','تطوير الويب','Blockchain','تطبيقات الجوال','حاضنات ومسرعات الأعمال','التنفيذ التقني التشاركي','أخرى'] as $cat)
                        <option value="{{ $cat }}" @selected(old('category', $editing ? $idea->category : ($fromParent->category ?? ''))==$cat)>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-primary mb-1.5">الميزانية التقديرية (اختياري)</label>
                <div class="grid grid-cols-[1fr_auto_1fr_auto] items-center gap-2" dir="ltr">
                    <input type="number" min="0" step="1" name="budget_min" value="{{ old('budget_min', $editing ? ($idea->budget_min ?? '') : '') }}" class="input text-center" placeholder="Min">
                    <span class="text-tertiary font-bold">—</span>
                    <input type="number" min="0" step="1" name="budget_max" value="{{ old('budget_max', $editing ? ($idea->budget_max ?? '') : '') }}" class="input text-center" placeholder="Max">
                    <select name="currency" class="input">
                        @foreach(['USD' => 'USD $', 'SAR' => 'SAR ﷼', 'EGP' => 'EGP ج.م'] as $code => $label)
                            <option value="{{ $code }}" @selected(old('currency', $editing ? ($idea->currency ?? 'USD') : 'USD')==$code)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
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
            <textarea name="feasibility" rows="3" class="input" x-ref="feasibility">{{ old('feasibility', $editing ? $idea->feasibility : ($fromParent->feasibility ?? '')) }}</textarea>
        </div>

        @unless($editing)
        <label class="flex items-start gap-3 text-sm p-4 rounded-xl bg-mist cursor-pointer">
            <input type="checkbox" name="ip_agreement" value="1" class="mt-1 accent-primary" required>
            <span class="text-tertiary leading-relaxed">
                أوافق على <a href="{{ route('agreement') }}" target="_blank" class="underline text-primary">اتفاقية الاستخدام</a>
                وآلية استنساخ الأفكار (Forking)، وأدرك أن نشر الفكرة يحفظ الحقوق الأدبية لصاحب الفكرة الأصلية.
            </span>
        </label>
        @endunless

        <div class="flex flex-wrap gap-3 pt-2 border-t border-mist">
            <button type="submit" name="intent" value="draft" class="btn-outline">حفظ كمسودة</button>
            <button type="submit" name="intent" value="pending" class="btn-secondary">إرسال للمراجعة الإدارية</button>
            <a href="{{ route('dashboard') }}" class="btn-ghost self-center">إلغاء</a>
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
        submitForm(event) {
            const v = this.techInput.replace(',', '').trim();
            if (v && !this.techs.includes(v)) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'technologies[]';
                input.value = v;
                event.target.appendChild(input);
            }
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
@endsection
