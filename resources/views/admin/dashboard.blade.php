@extends('layouts.admin')
@section('title', __('admin.overview'))
@section('content')
<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-primary mb-1">{{ __('admin.overview') }}</h1>
    <p class="text-tertiary text-sm">{{ __('admin.overview_desc') }}</p>
</div>

<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-8">
    <div class="card p-3.5 sm:p-5 min-w-0">
        <div class="text-xs text-tertiary mb-1 truncate">{{ __('admin.stats.users') }}</div>
        <div class="text-2xl sm:text-3xl font-black text-primary truncate">{{ $stats['users'] }}</div>
    </div>
    <div class="card p-3.5 sm:p-5 min-w-0">
        <div class="text-xs text-tertiary mb-1 truncate">{{ __('admin.stats.ideas') }}</div>
        <div class="text-2xl sm:text-3xl font-black text-primary truncate">{{ $stats['ideas'] }}</div>
    </div>
    <div class="card p-3.5 sm:p-5 min-w-0">
        <div class="text-xs text-tertiary mb-1 truncate">{{ __('admin.stats.published') }}</div>
        <div class="text-2xl sm:text-3xl font-black text-primary truncate">{{ $stats['published'] }}</div>
    </div>
    <div class="card p-3.5 sm:p-5 min-w-0 border-{{ app()->getLocale() === 'ar' ? 'r' : 'l' }}-4 border-indigo-500">
        <div class="text-xs font-bold text-indigo-600 mb-1 truncate">{{ __('admin.stats.conversion') }}</div>
        <div class="text-2xl sm:text-3xl font-black text-indigo-700 truncate">{{ $stats['conversion'] }}%</div>
        <div class="text-[10px] text-tertiary mt-1 truncate">{{ __('admin.stats.conversion_desc') }}</div>
    </div>
    <div class="card p-3.5 sm:p-5 min-w-0 border-{{ app()->getLocale() === 'ar' ? 'r' : 'l' }}-4 border-emerald-500 col-span-2 sm:col-span-1">
        <div class="text-xs font-bold text-emerald-600 mb-1 truncate">{{ __('admin.stats.avg_kyc_sla') }}</div>
        <div class="text-xl sm:text-2xl font-black text-emerald-700 truncate">{{ $stats['avg_kyc_sla'] }}</div>
        <div class="text-[10px] text-tertiary mt-1 truncate">{{ __('admin.stats.avg_kyc_desc') }}</div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-4 sm:gap-6">
    <div class="card p-4 sm:p-6 min-w-0">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-primary text-sm sm:text-base">{{ __('admin.recent_users') }}</h3>
            <a href="{{ route('admin.users') }}" class="text-xs text-secondary font-bold hover:underline">{{ __('admin.view_all') }}</a>
        </div>
        <div class="space-y-2.5">
            @forelse($recentUsers as $u)
            <div class="flex items-center justify-between gap-2.5 p-2.5 rounded-xl bg-neutral/60 hover:bg-neutral transition">
                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-primary/10 text-primary grid place-items-center font-bold text-xs sm:text-sm shrink-0">
                        {{ mb_substr($u->name, 0, 1) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="font-bold text-primary text-xs sm:text-sm truncate">{{ $u->name }}</div>
                        <div class="text-[11px] sm:text-xs text-tertiary truncate">
                            {{ $u->email }} <span class="text-tertiary/60 hidden sm:inline">· {{ $u->roleLabel() }}</span>
                        </div>
                    </div>
                </div>
                <span class="badge text-[10px] sm:text-xs font-bold px-2 py-0.5 rounded-md shrink-0 {{ $u->kyc_status === 'approved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : ($u->kyc_status === 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-200/60' : 'bg-mist text-tertiary') }}">
                    {{ $u->kyc_status }}
                </span>
            </div>
            @empty
                <p class="text-sm text-tertiary py-2 text-center">{{ __('admin.no_users') }}</p>
            @endforelse
        </div>
    </div>

    <div class="card p-4 sm:p-6 min-w-0">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-primary text-sm sm:text-base">{{ __('admin.pending_kyc') }}</h3>
            <a href="{{ route('admin.verifications') }}" class="text-xs text-secondary font-bold hover:underline">{{ __('admin.view_all') }}</a>
        </div>
        <div class="space-y-2.5">
            @forelse($pendingKyc as $v)
            <div class="flex items-center justify-between gap-2.5 p-2.5 rounded-xl bg-neutral/60 hover:bg-neutral transition">
                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg bg-secondary/15 text-secondary grid place-items-center font-bold text-xs shrink-0">
                        KYC
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="font-bold text-primary text-xs sm:text-sm truncate">{{ $v->user->name ?? '—' }}</div>
                        <div class="text-[11px] sm:text-xs text-tertiary truncate">
                            {{ $v->purposeLabel() }} <span class="text-tertiary/60">· {{ $v->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('admin.verifications') }}" class="text-xs bg-primary text-white px-3 py-1.5 rounded-lg font-bold hover:bg-primary/90 transition shrink-0">
                    {{ __('admin.review') }}
                </a>
            </div>
            @empty
                <p class="text-sm text-tertiary py-2 text-center">{{ __('admin.no_pending_kyc') }}</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
