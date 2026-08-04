@extends('layouts.dashboard')
@section('title','لوحة التحكم')
@section('content')
@php $user = auth()->user(); @endphp

<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-primary mb-1">مرحباً، {{ $user->name }}</h1>
    <p class="text-tertiary text-sm">مسارك: {{ $user->roleLabel() }} — إليك ملخص نشاطك وخطواتك التالية.</p>
</div>

@if($user->kyc_status === 'rejected' && $user->rejection_reason)
    <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 p-4">
        <div class="font-bold text-rose-700 mb-1">طلب KYC مرفوض — أكمل النواقص</div>
        <p class="text-sm text-rose-700 mb-3">{{ $user->rejection_reason }}</p>
        <a href="{{ route('verification.kyc') }}" class="btn-secondary text-sm !py-2">إعادة التقديم</a>
    </div>
@elseif($user->kyc_status === 'pending')
    <div class="mb-6 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm px-4 py-3">
        طلب التحقق قيد مراجعة الإدارة. يمكنك متابعة بناء ملفك في الأثناء.
    </div>
@elseif($user->kyc_status === 'none' && ($user->hasRole('idea_owner') || $user->wants_jobs_forum))
    <div class="mb-6 rounded-lg bg-secondary/10 border border-secondary/25 p-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <div class="font-bold text-primary">الخطوة التالية: التحقق من الهوية (KYC)</div>
            <p class="text-sm text-tertiary">مسارك يتطلب KYC لتفعيل الصلاحيات الكاملة.</p>
        </div>
        <a href="{{ route('verification.kyc', ['purpose' => $user->wants_jobs_forum ? 'jobs_forum' : 'publish_idea']) }}"
           class="btn-secondary text-sm !py-2">ابدأ KYC</a>
    </div>
@endif

<div class="grid lg:grid-cols-[300px_1fr] gap-6 mb-8">
    <div class="card p-6 text-center">
        <div class="w-20 h-20 rounded-2xl bg-primary text-white grid place-items-center mx-auto mb-3 text-2xl font-black">
            {{ mb_substr($user->name, 0, 1) }}
        </div>
        <h3 class="font-black text-primary text-lg">{{ $user->name }}</h3>
        <p class="text-xs text-tertiary mb-3">{{ $user->title ?: $user->roleLabel() }}</p>
        <div class="flex gap-2 justify-center mb-4 flex-wrap">
            @foreach(($user->roles ?: [$user->role]) as $r)
                <span class="badge bg-primary/10 text-primary">{{ ['idea_owner'=>'صاحب فكرة','idea_seeker'=>'باحث عن فكرة','developer'=>'باحث عن عمل'][$r] ?? $r }}</span>
            @endforeach
            @if($user->isKycApproved())
                <span class="badge bg-emerald-50 text-emerald-700">✓ KYC</span>
            @endif
        </div>
        <a href="{{ route('profile.cv') }}" class="btn-primary w-full text-sm">الملف / CV</a>
        <a href="{{ route('auth.path') }}" class="btn-outline w-full text-sm mt-2 block text-center">تغيير / إضافة مسار</a>
        @if($user->hasRole('idea_owner'))
            <a href="{{ route('dashboard.ideaOwner') }}" class="btn-ghost w-full text-sm mt-1 block text-center">إدارة أفكاري ←</a>
            <a href="{{ route('dashboard.implementRequests') }}" class="btn-ghost w-full text-sm block text-center">طلبات التنفيذ ({{ $stats['incoming'] ?? 0 }}) ←</a>
        @endif
        @if($user->hasRole('developer') && ! $user->show_in_jobs_forum)
            <a href="{{ route('verification.kyc', ['purpose'=>'jobs_forum']) }}" class="btn-secondary w-full text-sm mt-2 block text-center">الانضمام لمنتدى التوظيف</a>
        @endif
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="card p-5"><div class="text-xs text-tertiary mb-1">أفكاري</div><div class="text-3xl font-black text-primary">{{ $stats['ideas'] }}</div></div>
        <div class="card p-5"><div class="text-xs text-tertiary mb-1">منشورة</div><div class="text-3xl font-black text-primary">{{ $stats['published'] }}</div></div>
        <div class="card p-5"><div class="text-xs text-tertiary mb-1">إعجابات</div><div class="text-3xl font-black text-primary">{{ $stats['likes'] }}</div></div>
        <div class="card p-5"><div class="text-xs text-tertiary mb-1">طلبات تنفيذ</div><div class="text-3xl font-black text-primary">{{ $stats['implements'] }}</div></div>
    </div>
</div>

<div class="card p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-primary">أحدث أفكاري</h3>
        <a href="{{ route('ideas.create') }}" class="text-xs font-bold text-secondary">+ فكرة جديدة</a>
    </div>
    @forelse($myIdeas as $idea)
        <div class="flex flex-wrap items-center gap-3 py-3 border-b border-mist last:border-0">
            <div class="flex-1 min-w-0">
                <a href="{{ route('ideas.show', $idea->id) }}" class="font-bold text-primary text-sm hover:text-secondary">{{ $idea->title }}</a>
                <div class="text-xs text-tertiary">{{ $idea->category }} · {{ $idea->created_at->diffForHumans() }}</div>
                @if($idea->admin_notes && in_array($idea->status, ['draft', 'archived'], true))
                    <div class="text-xs text-rose-600 mt-1">ملاحظة الإدارة: {{ $idea->admin_notes }}</div>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if(in_array($idea->status, ['draft', 'archived'], true))
                    <form method="POST" action="{{ route('ideas.submit', $idea->id) }}"
                          onsubmit="return confirm('إرسال الفكرة للمراجعة الإدارية؟')">
                        @csrf
                        <button class="btn-secondary text-xs !py-1.5 !px-3">إرسال للنشر</button>
                    </form>
                @endif
                <span class="badge {{ [
                    'published'=>'bg-emerald-50 text-emerald-700',
                    'pending'=>'bg-amber-50 text-amber-700',
                    'draft'=>'bg-mist text-tertiary',
                    'archived'=>'bg-rose-50 text-rose-600',
                ][$idea->status] ?? 'bg-mist text-tertiary' }}">
                    {{ ['published'=>'منشورة','pending'=>'قيد المراجعة','draft'=>'مسودة','archived'=>'مؤرشفة'][$idea->status] ?? $idea->status }}
                </span>
            </div>
        </div>
    @empty
        <p class="text-sm text-tertiary text-center py-6">لا أفكار بعد. ابدأ من بنك الأفكار أو أنشئ فكرتك.</p>
    @endforelse
</div>
@endsection
