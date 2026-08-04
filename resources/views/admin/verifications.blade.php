@extends('layouts.admin')
@section('title','طلبات KYC')
@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="text-2xl font-black text-primary mb-1">طلبات التحقق (KYC)</h1>
        <p class="text-sm text-tertiary">موافقة · رفض مع سبب · المستندات للإدارة فقط</p>
    </div>
    <form class="flex gap-2">
        <select name="status" class="input !py-2" onchange="this.form.submit()">
            <option value="pending" @selected(request('status','pending')==='pending')>معلّق</option>
            <option value="approved" @selected(request('status')==='approved')>موافق</option>
            <option value="rejected" @selected(request('status')==='rejected')>مرفوض</option>
            <option value="" @selected(request()->has('status') && request('status')==='')>الكل</option>
        </select>
    </form>
</div>

<div class="space-y-4">
@forelse($verifications as $v)
<div class="card p-5" x-data="{ reject:false, notes:false }">
    <div class="flex flex-wrap gap-4 items-start justify-between">
        <div>
            <div class="font-bold text-primary text-lg">{{ $v->user->name ?? '—' }}</div>
            <div class="text-xs text-tertiary mb-2">{{ $v->user->email ?? '' }} · #{{ $v->user_id }} · {{ $v->user->roleLabel() ?? '' }}</div>
            <div class="flex flex-wrap gap-2">
                <span class="badge bg-primary/10 text-primary">{{ $v->purposeLabel() }}</span>
                <span class="badge bg-mist text-tertiary">{{ $v->doc_type }}</span>
                <span class="badge {{ $v->status==='pending'?'bg-amber-50 text-amber-700':($v->status==='approved'?'bg-emerald-50 text-emerald-700':'bg-rose-50 text-rose-700') }}">{{ $v->status }}</span>
            </div>
            @if($v->user?->admin_notes)
                <p class="text-xs text-tertiary mt-2 bg-mist rounded-lg px-2 py-1">ملاحظة داخلية: {{ $v->user->admin_notes }}</p>
            @endif
        </div>
        <div class="text-xs text-tertiary">{{ $v->created_at->format('Y-m-d H:i') }}</div>
    </div>

    <div class="grid sm:grid-cols-3 gap-3 mt-4">
        @if($v->doc_type === 'reevaluation')
            <div class="sm:col-span-3 bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-900">
                طلب إعادة تقييم بسبب تعديل بيانات حسّاسة — لا مستندات جديدة مطلوبة بالضرورة.
                @if($v->admin_notes)<div class="mt-1 font-bold">{{ $v->admin_notes }}</div>@endif
            </div>
        @else
            @foreach([['أمامية','id_front',$v->id_front],['خلفية','id_back',$v->id_back],['Selfie','selfie',$v->selfie]] as [$label,$fieldKey,$path])
                <div class="bg-mist rounded-xl p-3 text-center text-xs">
                    <div class="font-bold text-primary mb-1">{{ $label }}</div>
                    @if($path)
                        <a href="{{ route('admin.verifications.file', [$v->id, $fieldKey]) }}" target="_blank" class="text-secondary font-bold underline">عرض المستند</a>
                    @else
                        <span class="text-tertiary">—</span>
                    @endif
                </div>
            @endforeach
        @endif
    </div>

    @if($v->status === 'pending')
    <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-mist">
        <form method="POST" action="{{ route('admin.verifications.approve', $v->id) }}">@csrf
            <button class="btn-primary text-sm !py-2">موافقة</button>
        </form>
        <button type="button" @click="reject=!reject" class="btn-outline text-sm !py-2 !border-rose-300 !text-rose-600">رفض</button>
        <form method="POST" action="{{ route('admin.users.notes', $v->user_id) }}" class="flex-1 flex gap-2 min-w-[200px]">@csrf
            <input name="admin_notes" class="input !py-2 text-xs" placeholder="ملاحظة داخلية..." value="{{ $v->user->admin_notes }}">
            <button class="btn-ghost text-xs">حفظ</button>
        </form>
    </div>
    <form x-show="reject" x-cloak method="POST" action="{{ route('admin.verifications.reject', $v->id) }}" class="mt-3 space-y-2">
        @csrf
        <textarea name="reason" class="input" rows="2" placeholder="سبب الرفض (إلزامي)..." required></textarea>
        <button class="btn-secondary text-sm !py-2">تأكيد الرفض</button>
    </form>
    @endif
</div>
@empty
<div class="card p-10 text-center text-tertiary">لا طلبات في هذه الحالة.</div>
@endforelse
</div>
<div class="mt-6">{{ $verifications->links() }}</div>
@endsection
