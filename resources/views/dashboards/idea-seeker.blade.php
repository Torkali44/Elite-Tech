@extends('layouts.dashboard')
@section('title','باحث عن فكرة')
@section('content')
@php $user = auth()->user(); @endphp

<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-extrabold text-primary mb-1">لوحة الباحث عن فكرة</h1>
    <p class="text-tertiary text-sm">استكشف الأفكار المتاحة وقدّم طلبات التنفيذ بعد التوثيق.</p>
</div>

@if(! $user->isKycApproved())
    <div class="mb-6 rounded-lg bg-primary/5 border border-primary/15 p-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <div class="font-bold text-primary">KYC عند التنفيذ</div>
            <p class="text-sm text-tertiary mt-0.5">يمكنك التصفح بحرية. التحقق من الهوية مطلوب فقط عند تقديم «رغبة في التنفيذ».</p>
        </div>
        <a href="{{ route('ideas.index') }}" class="btn-outline text-sm !py-2">تصفح الأفكار</a>
    </div>
@endif

<div class="grid sm:grid-cols-3 gap-4 mb-8">
    <div class="card p-5">
        <div class="text-xs text-tertiary mb-1">حالة التوثيق</div>
        <div class="text-lg font-extrabold text-primary">
            {{ ['none'=>'غير مفعّل','pending'=>'قيد المراجعة','approved'=>'موثّق','rejected'=>'مرفوض'][$user->kyc_status ?? 'none'] ?? 'غير مفعّل' }}
        </div>
    </div>
    <div class="card p-5">
        <div class="text-xs text-tertiary mb-1">بنك الأفكار</div>
        <a href="{{ route('ideas.index') }}" class="text-lg font-extrabold text-primary hover:text-secondary">تصفح الآن ←</a>
    </div>
    <div class="card p-5">
        <div class="text-xs text-tertiary mb-1">الخطوة التالية</div>
        <div class="text-lg font-extrabold text-primary">
            {{ $user->isKycApproved() ? 'اطلب تنفيذاً' : 'أكمل KYC' }}
        </div>
    </div>
</div>

<div class="card p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-primary">ابدأ من هنا</h3>
        <a href="{{ route('ideas.index') }}" class="text-xs font-bold text-secondary">بنك الأفكار</a>
    </div>
    <p class="text-sm text-tertiary leading-relaxed mb-4">
        تصفّح الأفكار المنشورة، واختر فكرة تناسب مهاراتك، ثم قدّم طلب التنفيذ بعد اجتياز KYC.
    </p>
    <a href="{{ route('ideas.index') }}" class="btn-primary text-sm">استكشف الأفكار</a>
</div>
@endsection
