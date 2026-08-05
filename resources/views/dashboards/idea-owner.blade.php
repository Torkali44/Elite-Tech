@extends('layouts.dashboard')
@section('title', __('general.my_ideas_title'))
@section('content')
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl lg:text-3xl font-extrabold text-primary mb-1">{{ __('general.my_ideas_title') }}</h1>
        <p class="text-tertiary text-sm">{{ __('general.my_ideas_subtitle') }}</p>
    </div>
    <a href="{{ route('ideas.create') }}" class="btn-primary text-sm">{{ __('general.add_new_idea') }}</a>
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
                {{ [
                    'published' => __('general.status_published'),
                    'pending'   => __('general.status_pending'),
                    'draft'     => __('general.status_draft'),
                    'archived'  => __('general.status_archived'),
                ][$idea->status] ?? $idea->status }}
            </span>
            <span class="badge bg-mist text-tertiary text-[10px]">{{ $idea->category }}</span>
        </div>
        <a href="{{ route('ideas.show', $idea->id) }}" class="font-bold text-primary mb-2 block hover:text-secondary">{{ $idea->title }}</a>
        <p class="text-xs text-tertiary line-clamp-3 mb-4">{{ $idea->shortDesc(120) }}</p>
        @if($idea->admin_notes)
            <p class="text-xs text-rose-600 mb-3">{{ __('general.info') ?? 'Note' }}: {{ $idea->admin_notes }}</p>
        @endif

        <div class="flex flex-wrap gap-2 mb-3">
            <a href="{{ route('ideas.edit', $idea->id) }}" class="btn-outline text-xs !py-1.5 !px-3">{{ __('general.edit_btn') }}</a>
            @if(in_array($idea->status, ['draft', 'archived'], true))
                <form method="POST" action="{{ route('ideas.submit', $idea->id) }}" class="inline"
                      onsubmit="return confirm('{{ __('general.confirm_submit_idea') }}')">
                    @csrf
                    <button type="submit" class="btn-secondary text-xs !py-1.5 !px-3">{{ __('general.send_to_publish') }}</button>
                </form>
            @endif
            @if($idea->status === 'published')
                <a href="{{ route('ideas.show', $idea->id) }}" class="btn-ghost text-xs !py-1.5 !px-3">{{ __('general.view_btn') }}</a>
            @endif
        </div>

        <div class="flex items-center justify-between text-xs text-tertiary border-t border-mist pt-3">
            <span>{{ $idea->created_at->diffForHumans() }}</span>
            <span>{{ $idea->likes_count }} {{ __('general.likes_count') }}</span>
        </div>
    </div>
    @empty
    <div class="card p-10 text-center col-span-full text-tertiary">{{ __('general.no_ideas_yet') }} <a href="{{ route('ideas.create') }}" class="text-secondary font-bold">{{ __('general.add_your_idea') }}</a></div>
    @endforelse
</div>
@endsection
