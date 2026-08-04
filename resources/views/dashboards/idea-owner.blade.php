@extends('layouts.dashboard')
@section('title','أفكاري')
@section('content')
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl lg:text-3xl font-extrabold text-primary mb-1">أفكاري</h1>
        <p class="text-tertiary text-sm">إدارة وتتبع مساهماتك في بنك الأفكار.</p>
    </div>
    <a href="{{ route('ideas.create') }}" class="btn-primary text-sm">+ إرسال فكرة جديدة</a>
</div>

<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($ideas as $idea)
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="badge {{ [
                'published'=>'bg-emerald-50 text-emerald-700',
                'pending'=>'bg-amber-50 text-amber-700',
                'draft'=>'bg-mist text-tertiary',
                'archived'=>'bg-rose-50 text-rose-600',
            ][$idea->status] ?? 'bg-mist text-tertiary' }}">
                {{ ['published'=>'منشورة','pending'=>'قيد المراجعة','draft'=>'مسودة','archived'=>'مؤرشفة'][$idea->status] ?? $idea->status }}
            </span>
            <span class="badge bg-mist text-tertiary text-[10px]">{{ $idea->category }}</span>
        </div>
        <a href="{{ route('ideas.show', $idea->id) }}" class="font-bold text-primary mb-2 block hover:text-secondary">{{ $idea->title }}</a>
        <p class="text-xs text-tertiary line-clamp-3 mb-4">{{ $idea->shortDesc(120) }}</p>
        @if($idea->admin_notes)
            <p class="text-xs text-rose-600 mb-3">ملاحظة: {{ $idea->admin_notes }}</p>
        @endif

        <div class="flex flex-wrap gap-2 mb-3">
            <a href="{{ route('ideas.edit', $idea->id) }}" class="btn-outline text-xs !py-1.5 !px-3">تعديل</a>
            @if(in_array($idea->status, ['draft', 'archived'], true))
                <form method="POST" action="{{ route('ideas.submit', $idea->id) }}" class="inline"
                      onsubmit="return confirm('إرسال الفكرة للمراجعة الإدارية؟')">
                    @csrf
                    <button type="submit" class="btn-secondary text-xs !py-1.5 !px-3">إرسال للنشر</button>
                </form>
            @endif
            @if($idea->status === 'published')
                <a href="{{ route('ideas.show', $idea->id) }}" class="btn-ghost text-xs !py-1.5 !px-3">عرض</a>
            @endif
        </div>

        <div class="flex items-center justify-between text-xs text-tertiary border-t border-mist pt-3">
            <span>{{ $idea->created_at->diffForHumans() }}</span>
            <span>{{ $idea->likes_count }} إعجاب</span>
        </div>
    </div>
    @empty
    <div class="card p-10 text-center col-span-full text-tertiary">لا أفكار بعد. <a href="{{ route('ideas.create') }}" class="text-secondary font-bold">أضف فكرتك</a></div>
    @endforelse
</div>
@endsection
