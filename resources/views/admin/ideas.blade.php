@extends('layouts.admin')
@section('title', __('admin.ideas_title'))
@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="text-2xl font-black text-primary mb-1">{{ __('admin.ideas_title') }}</h1>
        <p class="text-sm text-tertiary">{{ __('admin.ideas_desc') }}</p>
    </div>
    <form>
        <select name="status" class="input !py-2" onchange="this.form.submit()">
            <option value="">{{ __('admin.all_statuses') }}</option>
            @foreach([
                'pending'  => (app()->getLocale()==='ar' ? 'مراجعة' : 'Pending'),
                'published'=> (app()->getLocale()==='ar' ? 'منشورة' : 'Published'),
                'draft'    => (app()->getLocale()==='ar' ? 'مسودة'  : 'Draft'),
                'archived' => (app()->getLocale()==='ar' ? 'مؤرشفة' : 'Archived'),
            ] as $k=>$l)
                <option value="{{ $k }}" @selected(request('status')===$k)>{{ $l }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="space-y-4">
@forelse($ideas as $idea)
<div class="card p-5" x-data="{ ret:false }">
    <div class="flex flex-wrap justify-between gap-3">
        <div>
            <h3 class="font-bold text-primary text-lg">{{ $idea->title }}</h3>
            <div class="text-xs text-tertiary mb-2">{{ $idea->user->name ?? '—' }} · {{ $idea->category }} · {{ $idea->created_at->diffForHumans() }}</div>
            <span class="badge bg-mist text-primary">{{ $idea->status }}</span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.ideas.show', $idea->id) }}" class="btn-ghost text-sm !py-2 border border-mist hover:bg-mist">{{ __('admin.view_details') }}</a>
            @if($idea->status !== 'published')
            <form method="POST" action="{{ route('admin.ideas.publish', $idea->id) }}">@csrf
                <button class="btn-primary text-sm !py-2">{{ __('admin.publish') }}</button>
            </form>
            @endif
            <button type="button" @click="ret=!ret" class="btn-outline text-sm !py-2">{{ __('admin.return_draft') }}</button>
        </div>
    </div>
    <p class="text-sm text-tertiary mt-3 line-clamp-3 whitespace-pre-line">{{ $idea->description }}</p>
    <form x-show="ret" x-cloak method="POST" action="{{ route('admin.ideas.return', $idea->id) }}" class="mt-3 space-y-2">
        @csrf
        <textarea name="note" rows="2" class="input" placeholder="{{ app()->getLocale()==='ar' ? 'ما الذي يحتاج توضيحاً؟' : 'What needs clarification?' }}" required></textarea>
        <button class="btn-secondary text-sm !py-2">{{ __('admin.send_note') }}</button>
    </form>
</div>
@empty
<div class="card p-10 text-center text-tertiary">{{ __('admin.no_ideas') }}</div>
@endforelse
</div>
<div class="mt-6">{{ $ideas->links() }}</div>
@endsection
