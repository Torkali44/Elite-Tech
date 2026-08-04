@extends('layouts.app')
@section('title', 'رغبة في التنفيذ — '.$idea->title)
@section('content')
<div class="max-w-xl mx-auto px-4 py-12">
    <div class="card p-8 space-y-5">
        <h1 class="text-2xl font-black text-primary">الرغبة في التنفيذ</h1>
        <p class="text-sm text-tertiary">الفكرة: <b class="text-primary">{{ $idea->title }}</b></p>

        @if($existing)
            <div class="rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm px-4 py-3">
                لديك طلب سابق بحالة: <b>{{ $existing->status }}</b>. إعادة الإرسال تستبدله بطلب معلّق جديد.
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('ideas.implement', $idea->id) }}" class="space-y-4">
            @csrf
            <label class="flex gap-3 p-4 rounded-xl border border-mist cursor-pointer has-[:checked]:border-primary">
                <input type="radio" name="via" value="elite_tech" class="mt-1 accent-primary" @checked(old('via')==='elite_tech')>
                <span class="text-sm"><b class="text-primary">عبر شركة إليت تك</b><br><span class="text-tertiary">وساطة وتنسيق احترافي</span></span>
            </label>
            <label class="flex gap-3 p-4 rounded-xl border border-mist cursor-pointer has-[:checked]:border-primary">
                <input type="radio" name="via" value="idea_owner" class="mt-1 accent-primary" @checked(old('via', 'idea_owner')==='idea_owner')>
                <span class="text-sm"><b class="text-primary">شراكة مع صاحب الفكرة</b><br><span class="text-tertiary">بعد موافقة صاحب الفكرة / الإدارة</span></span>
            </label>
            <textarea name="note" rows="3" class="input" placeholder="ملاحظة اختيارية...">{{ old('note') }}</textarea>
            <label class="flex items-start gap-2 text-xs text-tertiary">
                <input type="checkbox" name="agree_terms" value="1" class="mt-0.5 accent-primary" required>
                <span>أوافق على <a href="{{ route('agreement') }}" target="_blank" class="text-primary underline">اتفاقية الاستخدام</a>.</span>
            </label>
            <button class="btn-secondary w-full">إرسال طلب التنفيذ</button>
            <a href="{{ route('ideas.show', $idea->id) }}" class="btn-outline w-full text-center block">رجوع</a>
        </form>

        <p class="text-xs text-tertiary leading-relaxed">
            <b>من يراجع؟</b> صاحب الفكرة يدير طلبات الانضمام لفريقه، والإدارة تراقب السجل ضد الاحتيال ويمكنها الموافقة أو الرفض أيضاً.
        </p>
    </div>
</div>
@endsection
