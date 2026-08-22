@extends('layouts.app')
@section('title', $idea->title)
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <nav class="text-xs text-tertiary mb-4">
        <a href="{{ route('ideas.index') }}" class="hover:text-primary">بنك الأفكار</a>
        <span class="mx-1">›</span>
        <span>{{ $idea->category }}</span>
    </nav>

    <div class="grid lg:grid-cols-[1fr_300px] gap-6">
        <article class="space-y-6">
            <div class="card p-6 lg:p-8">
                <div class="flex items-center gap-2 mb-4 flex-wrap">
                    <span class="badge bg-primary/10 text-primary">{{ $idea->category }}</span>
                    <span class="text-xs text-tertiary">{{ $idea->created_at->diffForHumans() }}</span>
                    @if($idea->parent)
                        <div class="w-full rounded-xl bg-secondary/10 border border-secondary/20 text-sm text-primary px-4 py-3 leading-relaxed">
                            <span class="font-black text-secondary">شارة الاستنساخ:</span>
                            هذه الفكرة مستلهمة/مبنية على فكرة
                            «<a href="{{ route('ideas.show', $idea->parent->id) }}" class="font-bold underline">{{ $idea->parent->title }}</a>»
                            لصاحبها <b>{{ $idea->parent->user->name ?? 'عضو' }}</b>
                            — حفظاً للتقدير الأدبي والشفافية.
                        </div>
                    @endif
                </div>
                <h1 class="text-3xl font-black text-primary mb-6 leading-tight">{{ $idea->title }}</h1>
                <div class="prose max-w-none text-tertiary leading-relaxed whitespace-pre-line text-sm">{{ $idea->description }}</div>

                @if($idea->technologies)
                    <h3 class="font-bold text-primary mt-8 mb-3">التقنيات المقترحة</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($idea->technologies as $tech)
                            <span class="badge bg-mist text-primary border border-slate-200">{{ $tech }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            @if($idea->budget)
            <div class="card p-6">
                <h3 class="font-bold text-primary mb-3">الميزانية التقديرية</h3>
                <p class="text-sm text-tertiary leading-relaxed">{{ $idea->budget }}</p>
            </div>
            @endif

            @if($idea->feasibility)
            <div class="card p-6">
                <h3 class="font-bold text-primary mb-3">دراسة جدوى مبسطة</h3>
                <p class="text-sm text-tertiary leading-relaxed whitespace-pre-line">{{ $idea->feasibility }}</p>
            </div>
            @endif

            <div class="card p-6" id="comments">
                <h3 class="font-bold text-primary mb-4">النقاشات ({{ $idea->comments->count() }})</h3>
                @auth
                    <form method="POST" action="{{ route('ideas.comment', $idea->id) }}" class="mb-6">
                        @csrf
                        <textarea name="body" rows="2" placeholder="أضف رؤيتك للنقاش..." class="input" required></textarea>
                        <button type="submit" class="btn-primary mt-2 text-sm !py-2">إرسال التعليق</button>
                    </form>
                @else
                    <button type="button" class="w-full text-center text-sm bg-mist rounded-xl py-4 mb-6 text-primary font-semibold hover:bg-slate-100"
                            @click="gateOpen=true; gateMsg='التعليق على الأفكار يتطلب إنشاء حساب.'">
                        سجّل الدخول للمشاركة في النقاش
                    </button>
                @endauth

                <div class="space-y-4">
                    @forelse($idea->comments as $c)
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-secondary/15 shrink-0 grid place-items-center font-bold text-secondary text-sm">
                                {{ mb_substr($c->user->name ?? '?', 0, 1) }}
                            </div>
                            <div class="flex-1 bg-mist rounded-xl p-4">
                                <div class="flex items-center justify-between mb-1 gap-2 flex-wrap">
                                    <span class="font-bold text-primary text-sm">{{ $c->user->name ?? 'عضو' }}</span>
                                    <span class="text-xs text-tertiary">{{ $c->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-tertiary leading-relaxed">{{ $c->body }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-tertiary text-center py-4">لا تعليقات بعد — كن أول من يشارك رأيه.</p>
                    @endforelse
                </div>
            </div>
        </article>

        <aside class="space-y-4">
            <div class="card p-5 space-y-2">
                @auth
                    @if($userRequest)
                        <div class="badge bg-amber-50 text-amber-700 mb-1">طلب تنفيذ: {{ $userRequest->status }}</div>
                    @endif

                    @if($idea->status === 'published')
                        @if(auth()->user()->isKycApproved())
                            <a href="{{ route('ideas.implement.form', $idea->id) }}" class="btn-secondary w-full text-center block">رغبة في التنفيذ</a>
                        @else
                            <a href="{{ route('verification.kyc', ['purpose'=>'implement','idea'=>$idea->id]) }}" class="btn-secondary w-full text-center block">
                                رغبة في التنفيذ → KYC
                            </a>
                            <p class="text-[11px] text-tertiary text-center leading-relaxed">يتطلب اجتياز التحقق لضمان الجدية.</p>
                        @endif

                        <a href="{{ route('ideas.fork.confirm', $idea->id) }}" class="btn-outline w-full text-sm text-center block">تطوير الفكرة (Fork)</a>

                        <form method="POST" action="{{ route('ideas.favorite', $idea->id) }}">
                            @csrf
                            <button class="btn-ghost w-full text-sm {{ !empty($isFavorited) ? 'text-secondary font-bold' : '' }}">
                                {{ !empty($isFavorited) ? '★ إزالة من المفضلة' : '☆ أضف للمفضلة' }}
                            </button>
                        </form>
                    @else
                        <div class="rounded-lg bg-neutral text-tertiary text-sm p-3 text-center space-y-3">
                            <p>هذه الفكرة غير منشورة بعد — التنفيذ والتعليق بعد النشر.</p>
                            @if(auth()->id() === $idea->user_id && in_array($idea->status, ['draft', 'archived'], true))
                                <a href="{{ route('ideas.edit', $idea->id) }}" class="btn-outline w-full text-sm block">تعديل المسودة</a>
                                <form method="POST" action="{{ route('ideas.submit', $idea->id) }}"
                                      onsubmit="return confirm('إرسال الفكرة للمراجعة الإدارية؟')">
                                    @csrf
                                    <button class="btn-secondary w-full text-sm">إرسال للنشر</button>
                                </form>
                            @endif
                        </div>
                    @endif
                @else
                    <button type="button" class="btn-secondary w-full"
                            @click="gateOpen=true; gateMsg='الرغبة في التنفيذ تتطلب حساباً ثم اجتياز KYC.'">
                        رغبة في التنفيذ
                    </button>
                    <a href="{{ route('register') }}" class="btn-outline w-full text-center block text-sm">إنشاء حساب</a>
                @endauth

                <div class="grid grid-cols-2 gap-2 mt-2 text-center text-xs">
                    <div class="bg-mist rounded-lg p-2">
                        <div class="font-extrabold text-primary text-lg">{{ $idea->budget ? number_format($idea->budget).'$' : '—' }}</div>
                        <div class="text-tertiary">الميزانية</div>
                    </div>
                    <div class="bg-mist rounded-lg p-2">
                        <div class="font-extrabold text-primary text-lg">{{ $idea->likes_count }}</div>
                        <div class="text-tertiary">إعجاب</div>
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <div class="text-xs text-tertiary mb-2">صاحب الفكرة</div>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-primary/10 grid place-items-center font-bold text-primary">
                        {{ mb_substr($idea->user->name ?? '?', 0, 1) }}
                    </div>
                    <div>
                        <div class="font-bold text-primary">{{ $idea->user->name ?? 'عضو' }}</div>
                        <div class="text-xs text-tertiary">{{ $idea->user->title ?? $idea->user->roleLabel() }}</div>
                    </div>
                </div>
                @if($idea->user)
                    <a href="{{ route('profile.show', $idea->user->id) }}" class="block text-center text-xs text-primary font-bold mt-3 hover:underline">عرض الملف الشخصي ←</a>
                @endif
            </div>
        </aside>
    </div>
</div>
@endsection
