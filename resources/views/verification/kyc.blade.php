@extends('layouts.dashboard')
@section('title','التحقق من الهوية (KYC)')
@section('content')
<div class="max-w-3xl" x-data="{
    frontName: '',
    backName: '',
    selfieName: '',
    pick(ref, key) { this.$refs[ref].click() },
    onFile(e, key) { this[key] = e.target.files[0]?.name || '' }
}">
    <h1 class="text-2xl lg:text-3xl font-black text-primary mb-2">التحقق من الهوية (KYC)</h1>
    <p class="text-tertiary text-sm mb-6 leading-relaxed">
        المستندات تُعرض للإدارة فقط بشكل آمن. بعد المراجعة: موافقة تفعّل صلاحياتك، أو رفض مع سبب لاستكمال النواقص.
    </p>

    @if($latest)
        <div class="card p-4 mb-6 flex flex-wrap items-center gap-3">
            <span class="text-sm font-bold text-primary">آخر طلب:</span>
            @if($effectiveKycStatus === 'pending')
                <span class="badge bg-amber-50 text-amber-700">قيد المراجعة</span>
            @elseif($effectiveKycStatus === 'approved')
                <span class="badge bg-emerald-50 text-emerald-700">موافق عليه</span>
            @else
                <span class="badge bg-rose-50 text-rose-700">مرفوض</span>
            @endif
            <span class="text-xs text-tertiary">{{ $latest->purposeLabel() }} · {{ $latest->created_at->diffForHumans() }}</span>
            @if($effectiveKycStatus === 'rejected' && $latest->rejection_reason)
                <p class="w-full text-sm text-rose-700 bg-rose-50 rounded-lg px-3 py-2 mt-1">السبب: {{ $latest->rejection_reason }}</p>
            @endif
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 space-y-1">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <form action="{{ route('verification.kyc') }}" method="POST" enctype="multipart/form-data" class="card p-6 space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-bold text-primary mb-1.5">الغرض من التحقق</label>
            <select name="purpose" class="input" required>
                <option value="publish_idea" @selected($purpose==='publish_idea')>نشر أفكار (صاحب فكرة)</option>
                <option value="implement" @selected($purpose==='implement')>رغبة في التنفيذ</option>
                <option value="jobs_forum" @selected($purpose==='jobs_forum')>الظهور في منتدى التوظيف</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-bold text-primary mb-1.5">نوع المستند</label>
            <select name="doc_type" class="input" required>
                <option value="national_id">بطاقة الهوية الوطنية</option>
                <option value="passport">جواز سفر</option>
                <option value="driver_license">رخصة قيادة</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-bold text-primary mb-2">صورة المستند (الجهة الأمامية) *</label>
            <div @click="pick('front')" class="border-2 border-dashed border-mist rounded-xl p-8 text-center hover:border-primary transition cursor-pointer bg-neutral/50">
                <div class="font-bold text-primary text-sm mb-1">انقر للرفع</div>
                <div class="text-xs text-tertiary" x-text="frontName || 'JPG, PNG أو PDF — حد أقصى 5 ميغابايت'"></div>
                <input type="file" name="id_front" accept=".jpg,.jpeg,.png,.pdf" class="hidden" x-ref="front" @change="onFile($event,'frontName')" required>
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-primary mb-2">صورة المستند (الجهة الخلفية)</label>
            <div @click="pick('back')" class="border-2 border-dashed border-mist rounded-xl p-8 text-center hover:border-primary transition cursor-pointer bg-neutral/50">
                <div class="font-bold text-primary text-sm mb-1">انقر للرفع</div>
                <div class="text-xs text-tertiary" x-text="backName || 'اختياري للجواز'"></div>
                <input type="file" name="id_back" accept=".jpg,.jpeg,.png,.pdf" class="hidden" x-ref="back" @change="onFile($event,'backName')">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-primary mb-2">صورة شخصية (Selfie)</label>
            <div @click="pick('selfie')" class="border-2 border-dashed border-mist rounded-xl p-6 text-center hover:border-secondary transition cursor-pointer">
                <div class="font-bold text-primary text-sm" x-text="selfieName || 'رفع صورة شخصية للتطابق'"></div>
                <input type="file" name="selfie" accept=".jpg,.jpeg,.png" class="hidden" x-ref="selfie" @change="onFile($event,'selfieName')">
            </div>
        </div>

        <div class="flex gap-3 pt-4 border-t border-mist">
            <a href="{{ route('dashboard') }}" class="btn-outline">إلغاء</a>
            <button type="submit" class="btn-primary">إرسال للمراجعة</button>
        </div>
    </form>
</div>
@endsection
