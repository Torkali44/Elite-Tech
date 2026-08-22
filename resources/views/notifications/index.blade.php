@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.app')
@section('title', 'الإشعارات')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-black text-primary">الإشعارات</h1>
        @if(auth()->user()->unreadNotifications->count() > 0)
        <form method="POST" action="{{ route('notifications.readAll') }}">
            @csrf
            <button class="text-sm font-bold text-secondary hover:underline">تحديد الكل كمقروء</button>
        </form>
        @endif
    </div>

    <div class="card overflow-hidden">
        @forelse($notifications as $n)
            <a href="{{ route('notifications.read', $n->id) }}" class="flex items-start gap-4 p-4 border-b border-mist hover:bg-slate-50 transition {{ $n->unread() ? 'bg-blue-50/50' : '' }}">
                <div class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center {{ $n->unread() ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-500' }}">
                    @if(($n->data['icon'] ?? '') === 'lightbulb')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    @elseif(($n->data['icon'] ?? '') === 'check')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @elseif(($n->data['icon'] ?? '') === 'exclamation')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    @elseif(($n->data['icon'] ?? '') === 'user')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    @elseif(($n->data['icon'] ?? '') === 'cog')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    @endif
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-sm {{ $n->unread() ? 'text-primary' : 'text-slate-700' }}">{{ $n->data['title'] ?? 'إشعار' }}</span>
                        <span class="text-xs text-tertiary">{{ $n->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-tertiary leading-relaxed">{{ $n->data['message'] ?? '' }}</p>
                </div>
            </a>
        @empty
            <div class="p-8 text-center text-tertiary text-sm">
                لا توجد إشعارات حالياً.
            </div>
        @endforelse
    </div>
    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
