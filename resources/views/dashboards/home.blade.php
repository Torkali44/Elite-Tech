@extends('layouts.dashboard')
@section('title', __('dashboard.title'))
@section('content')
@php $user = auth()->user(); @endphp

<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-primary mb-1">{{ app()->getLocale()==='ar' ? 'مرحباً،' : 'Welcome,' }} {{ $user->name }}</h1>
    <p class="text-tertiary text-sm">{{ app()->getLocale()==='ar' ? 'مسارك:' : 'Your path:' }} {{ $user->roleLabel() }} — {{ app()->getLocale()==='ar' ? 'إليك ملخص نشاطك وخطواتك التالية.' : 'Here is a summary of your activity and next steps.' }}</p>
</div>

@if($user->kyc_status === 'rejected' && $user->rejection_reason)
    <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 p-4">
        <div class="font-bold text-rose-700 mb-1">{{ __('kyc.status_rejected') }}</div>
        <p class="text-sm text-rose-700 mb-3">{{ $user->rejection_reason }}</p>
        <a href="{{ route('verification.kyc') }}" class="btn-secondary text-sm !py-2">{{ __('kyc.resubmit') }}</a>
    </div>
@elseif($user->kyc_status === 'pending')
    <div class="mb-6 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm px-4 py-3">
        {{ __('kyc.status_pending') }}
    </div>
@elseif($user->kyc_status === 'none' && ($user->hasRole('idea_owner') || $user->wants_jobs_forum))
    <div class="mb-6 rounded-lg bg-secondary/10 border border-secondary/25 p-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <div class="font-bold text-primary">{{ app()->getLocale()==='ar' ? 'الخطوة التالية: التحقق من الهوية (KYC)' : 'Next Step: Identity Verification (KYC)' }}</div>
            <p class="text-sm text-tertiary">{{ app()->getLocale()==='ar' ? 'مسارك يتطلب KYC لتفعيل الصلاحيات الكاملة.' : 'Your path requires KYC to unlock full permissions.' }}</p>
        </div>
        <a href="{{ route('verification.kyc', ['purpose' => $user->wants_jobs_forum ? 'jobs_forum' : 'publish_idea']) }}"
           class="btn-secondary text-sm !py-2">{{ __('dashboard.complete_kyc_now') }}</a>
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
                <span class="badge bg-primary/10 text-primary">{{ __('dashboard.roles.'.$r) ?? $r }}</span>
            @endforeach
            @if($user->isKycApproved())
                <span class="badge bg-emerald-50 text-emerald-700">✓ KYC</span>
            @endif
        </div>
        <a href="{{ route('profile.cv') }}" class="btn-primary w-full text-sm">{{ __('dashboard.profile_cv') }}</a>
        <a href="{{ route('auth.path') }}" class="btn-outline w-full text-sm mt-2 block text-center">{{ __('dashboard.change_path') }}</a>
        @if($user->hasRole('idea_owner'))
            <a href="{{ route('dashboard.ideaOwner') }}" class="btn-ghost w-full text-sm mt-1 block text-center">{{ __('dashboard.my_ideas') }} ←</a>
            <a href="{{ route('dashboard.implementRequests') }}" class="btn-ghost w-full text-sm block text-center">{{ __('dashboard.implement_requests') }} ({{ $stats['incoming'] ?? 0 }}) ←</a>
        @endif
        @if($user->hasRole('developer') && ! $user->show_in_jobs_forum)
            <a href="{{ route('verification.kyc', ['purpose'=>'jobs_forum']) }}" class="btn-secondary w-full text-sm mt-2 block text-center">{{ app()->getLocale()==='ar' ? 'الانضمام لمنتدى التوظيف' : 'Join Jobs Forum' }}</a>
        @endif
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="card p-5"><div class="text-xs text-tertiary mb-1">{{ __('dashboard.my_ideas') }}</div><div class="text-3xl font-black text-primary">{{ $stats['ideas'] }}</div></div>
        <div class="card p-5"><div class="text-xs text-tertiary mb-1">{{ __('ideas.status_published') }}</div><div class="text-3xl font-black text-primary">{{ $stats['published'] }}</div></div>
        <div class="card p-5"><div class="text-xs text-tertiary mb-1">{{ app()->getLocale()==='ar' ? 'إعجابات' : 'Likes' }}</div><div class="text-3xl font-black text-primary">{{ $stats['likes'] }}</div></div>
        <div class="card p-5"><div class="text-xs text-tertiary mb-1">{{ __('dashboard.implement_requests') }}</div><div class="text-3xl font-black text-primary">{{ $stats['implements'] }}</div></div>
    </div>
</div>

<div class="card p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-primary">{{ app()->getLocale()==='ar' ? 'أحدث أفكاري' : 'My Recent Ideas' }}</h3>
        <a href="{{ route('ideas.create') }}" class="text-xs font-bold text-secondary">{{ __('ideas.create_new') }}</a>
    </div>
    @forelse($myIdeas as $idea)
        <div class="flex flex-wrap items-center gap-3 py-3 border-b border-mist last:border-0">
            <div class="flex-1 min-w-0">
                <a href="{{ route('ideas.show', $idea->id) }}" class="font-bold text-primary text-sm hover:text-secondary">{{ $idea->localized_title }}</a>
                <div class="text-xs text-tertiary">{{ $idea->category }} · {{ $idea->created_at->diffForHumans() }}</div>
                @if($idea->admin_notes && in_array($idea->status, ['draft', 'archived'], true))
                    <div class="text-xs text-rose-600 mt-1">{{ app()->getLocale()==='ar' ? 'ملاحظة الإدارة:' : 'Admin note:' }} {{ $idea->admin_notes }}</div>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if(in_array($idea->status, ['draft', 'archived'], true))
                    <form method="POST" action="{{ route('ideas.submit', $idea->id) }}"
                          onsubmit="return confirm('{{ app()->getLocale()==='ar' ? 'إرسال الفكرة للمراجعة الإدارية؟' : 'Submit idea for admin review?' }}')">
                        @csrf
                        <button class="btn-secondary text-xs !py-1.5 !px-3">{{ app()->getLocale()==='ar' ? 'إرسال للنشر' : 'Submit for Publishing' }}</button>
                    </form>
                @endif
                <span class="badge {{ [
                    'published'=>'bg-emerald-50 text-emerald-700',
                    'pending'=>'bg-amber-50 text-amber-700',
                    'draft'=>'bg-mist text-tertiary',
                    'archived'=>'bg-rose-50 text-rose-600',
                ][$idea->status] ?? 'bg-mist text-tertiary' }}">
                    {{ [
                        'published' => __('ideas.status_published'),
                        'pending'   => __('ideas.status_pending'),
                        'draft'     => __('ideas.status_draft'),
                        'archived'  => (app()->getLocale()==='ar' ? 'مؤرشفة' : 'Archived'),
                    ][$idea->status] ?? $idea->status }}
                </span>
            </div>
        </div>
    @empty
        <p class="text-sm text-tertiary text-center py-6">{{ app()->getLocale()==='ar' ? 'لا أفكار بعد. ابدأ من بنك الأفكار أو أنشئ فكرتك.' : 'No ideas yet. Start from the Ideas Bank or create your own.' }}</p>
    @endforelse
</div>
@endsection
