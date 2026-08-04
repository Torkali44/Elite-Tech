@extends('layouts.app')
@section('title', 'تطوير الفكرة — '.$idea->title)
@section('content')
<div class="max-w-xl mx-auto px-4 py-12">
    <div class="card p-8 space-y-5">
        <h1 class="text-2xl font-black text-primary">تطوير / استنساخ الفكرة</h1>
        <p class="text-sm text-tertiary leading-relaxed">
            سيتم إنشاء <b>مسودة جديدة</b> مرتبطة بالفكرة الأصلية
            «{{ $idea->title }}» لصاحبها <b>{{ $idea->user->name ?? 'عضو' }}</b>
            مع شارة حفظ الحقوق الأدبية عند النشر.
        </p>
        <form method="POST" action="{{ route('ideas.fork', $idea->id) }}" class="space-y-3">
            @csrf
            <button class="btn-secondary w-full">تأكيد الاستنساخ وإنشاء المسودة</button>
            <a href="{{ route('ideas.show', $idea->id) }}" class="btn-outline w-full text-center block">رجوع</a>
        </form>
    </div>
</div>
@endsection
