@extends('layouts.admin')
@section('title', 'تفاصيل طلب التنفيذ #' . $request->id)
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.implementations') }}" class="btn-ghost text-sm flex items-center gap-1">
            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            عودة لطلبات التنفيذ
        </a>
        <h1 class="text-2xl font-black text-primary">طلب تنفيذ #{{ $request->id }}</h1>
    </div>
    <div>
        @if($request->status === 'approved')
            <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">موافق عليه</span>
        @elseif($request->status === 'rejected')
            <span class="px-3 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-800 border border-rose-200">مرفوض</span>
        @else
            <span class="px-3 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200">معلّق</span>
        @endif
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <!-- Request details -->
        <div class="card p-6 space-y-4">
            <h2 class="text-lg font-extrabold text-primary border-b border-mist pb-3">تفاصيل الطلب والرسالة</h2>

            <div class="grid grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-tertiary block">طريقة التواصل / النمط</span>
                    <span class="font-bold text-primary text-sm">{{ $request->via === 'elite_tech' ? 'وساطة إليت تك (Elite Tech)' : 'تواصل مباشر' }}</span>
                </div>
                <div>
                    <span class="text-tertiary block">تاريخ إرسال الطلب</span>
                    <span class="font-bold text-primary text-sm">{{ $request->created_at->format('Y-m-d H:i') }} ({{ $request->created_at->diffForHumans() }})</span>
                </div>
            </div>

            @if($request->note)
            <div class="pt-3">
                <h3 class="text-xs font-bold text-tertiary mb-1">الملاحظات / الخطاب المرفق</h3>
                <div class="p-4 rounded-lg bg-neutral border border-mist text-sm text-primary whitespace-pre-line leading-relaxed">
                    {{ $request->note }}
                </div>
            </div>
            @endif
        </div>

        <!-- Target Idea details -->
        <div class="card p-6 space-y-4">
            <h2 class="text-lg font-extrabold text-primary border-b border-mist pb-3">بيانات الفكرة المستهدفة</h2>
            @if($request->idea)
            <div>
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-primary text-base">{{ $request->idea->title }}</h3>
                    <a href="{{ route('admin.ideas.show', $request->idea->id) }}" class="text-xs font-bold text-secondary underline">عرض الفكرة بالأدمن ↗</a>
                </div>
                <p class="text-xs text-tertiary mt-2 line-clamp-3">{{ $request->idea->description }}</p>
                <div class="mt-3 text-xs bg-mist p-3 rounded-lg flex items-center justify-between">
                    <span>صاحب الفكرة: <strong>{{ $request->idea->user->name ?? '—' }}</strong> ({{ $request->idea->user->email ?? '' }})</span>
                    <a href="{{ route('profile.show', $request->idea->user_id) }}" target="_blank" class="text-secondary font-bold underline">الملف</a>
                </div>
            </div>
            @else
            <p class="text-xs text-tertiary">الفكرة غير موجودة.</p>
            @endif
        </div>
    </div>

    <div class="space-y-6">
        <!-- Applicant Developer Details -->
        <div class="card p-6 space-y-4">
            <h3 class="font-bold text-primary pb-2 border-b border-mist">بيانات المطور / المتقدم</h3>
            @if($request->user)
            <div class="space-y-3 text-sm">
                <div>
                    <div class="text-xs text-tertiary">الاسم</div>
                    <div class="font-bold text-primary">{{ $request->user->name }}</div>
                </div>
                <div>
                    <div class="text-xs text-tertiary">البريد الإلكتروني</div>
                    <div class="font-medium text-primary">{{ $request->user->email }}</div>
                </div>
                <div>
                    <div class="text-xs text-tertiary mb-1">المسار / الدور</div>
                    <span class="badge bg-mist text-primary">{{ $request->user->roleLabel() }}</span>
                </div>
                <div>
                    <div class="text-xs text-tertiary mb-1">حالة توثيق KYC</div>
                    @if($request->user->isKycApproved())
                        <span class="badge bg-emerald-100 text-emerald-800">موثّق</span>
                    @else
                        <span class="badge bg-amber-100 text-amber-800">{{ $request->user->kyc_status }}</span>
                    @endif
                </div>
                <div class="pt-2 space-y-2">
                    <a href="{{ route('profile.show', $request->user->id) }}" target="_blank" class="btn-outline text-xs w-full text-center block !py-2">
                        معاينة الملف الشخصي ↗
                    </a>
                </div>
            </div>
            @else
            <p class="text-xs text-tertiary">بيانات المتقدم غير متاحة.</p>
            @endif
        </div>

        <!-- Admin Actions -->
        @if($request->status === 'pending')
        <div class="card p-6 space-y-4" x-data="{ rej: false }">
            <h3 class="font-bold text-primary pb-2 border-b border-mist">إجراءات الأدمن</h3>
            <form method="POST" action="{{ route('admin.implementations.approve', $request->id) }}">
                @csrf
                <button class="btn-primary w-full text-sm">موافقة إدارية على الطلب</button>
            </form>

            <button type="button" @click="rej = !rej" class="btn-outline w-full text-sm !text-rose-600 !border-rose-300">
                رفض الطلب
            </button>

            <form x-show="rej" x-cloak method="POST" action="{{ route('admin.implementations.reject', $request->id) }}" class="space-y-3 pt-2">
                @csrf
                <textarea name="reason" rows="3" class="input text-xs" placeholder="أدخل سبب الرفض الإداري..." required></textarea>
                <button class="btn-secondary w-full text-xs !py-2 !bg-rose-600">تأكيد الرفض</button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
