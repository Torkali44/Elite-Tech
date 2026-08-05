@extends('layouts.admin')
@section('title', __('admin.users_title'))
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-black text-primary mb-1">{{ __('admin.users_title') }}</h1>
    <p class="text-sm text-tertiary">{{ __('admin.users_desc') }}</p>
</div>

<form class="card p-4 mb-6 grid sm:grid-cols-4 gap-3">
    <input name="q" value="{{ request('q') }}" class="input" placeholder="{{ app()->getLocale() === 'ar' ? 'اسم / بريد / رقم' : 'Name / email / ID' }}">
    <select name="kyc" class="input">
        <option value="">{{ __('admin.filter_all_kyc') }}</option>
        @foreach(['none','pending','approved','rejected','suspended'] as $s)
            <option value="{{ $s }}" @selected(request('kyc')===$s)>{{ $s }}</option>
        @endforeach
    </select>
    <select name="role" class="input">
        <option value="">{{ __('admin.filter_all_roles') }}</option>
        @foreach(['idea_owner','idea_seeker','developer'] as $r)
            <option value="{{ $r }}" @selected(request('role')===$r)>{{ $r }}</option>
        @endforeach
    </select>
    <button class="btn-primary">{{ __('admin.apply_filters') }}</button>
</form>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-mist text-tertiary text-xs">
                <tr>
                    <th class="text-start p-3">{{ __('admin.table.id') }}</th>
                    <th class="text-start p-3">{{ __('admin.table.name') }}</th>
                    <th class="text-start p-3">{{ __('admin.table.path') }}</th>
                    <th class="text-start p-3">{{ __('admin.table.kyc') }}</th>
                    <th class="text-start p-3">{{ __('admin.table.forum') }}</th>
                    <th class="text-start p-3">{{ __('admin.table.date') }}</th>
                    <th class="p-3">{{ __('admin.table.actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($users as $u)
                @php
                    $roleMap = [
                        'idea_owner'  => __('dashboard.roles.idea_owner'),
                        'idea_seeker' => __('dashboard.roles.idea_seeker'),
                        'developer'   => __('dashboard.roles.developer'),
                        'admin'       => __('dashboard.roles.admin'),
                    ];
                    $userRoles = is_array($u->roles) && count($u->roles) > 0 ? $u->roles : [$u->role];
                    $hasMultiple = count($userRoles) > 1;
                @endphp
                <tr class="border-t border-mist hover:bg-neutral/60" x-data="{ n:false }">
                    <td class="p-3 text-tertiary">{{ $u->id }}</td>
                    <td class="p-3">
                        <div class="font-bold text-primary">{{ $u->name }}</div>
                        <div class="text-xs text-tertiary">{{ $u->email }}</div>
                    </td>
                    <td class="p-3">
                        @if($hasMultiple)
                            <div class="mb-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-extrabold bg-purple-100 text-purple-800 border border-purple-200">
                                    ⚡ {{ __('admin.multiple_tracks', ['count' => count($userRoles)]) }}
                                </span>
                            </div>
                            <div class="text-xs text-tertiary flex flex-wrap gap-1">
                                @foreach($userRoles as $rKey)
                                    <span class="inline-block bg-neutral px-1.5 py-0.5 rounded border border-mist text-[11px] font-medium text-primary">
                                        {{ $roleMap[$rKey] ?? $rKey }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="font-medium text-primary">{{ $u->roleLabel() }}</span>
                        @endif
                    </td>
                    <td class="p-3">
                        @if($u->is_suspended)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                ⛔ {{ __('admin.suspended') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                ✅ {{ __('admin.active') }}
                            </span>
                        @endif
                        <div class="text-[10px] text-tertiary mt-1">KYC: {{ $u->kyc_status }}</div>
                    </td>
                    <td class="p-3">{{ $u->show_in_jobs_forum ? __('general.yes') : __('general.no') }}</td>
                    <td class="p-3 text-xs text-tertiary">{{ $u->created_at->format('Y-m-d') }}</td>
                    <td class="p-3">
                        <div class="flex gap-2 justify-end items-center">
                            <button type="button" @click="n=!n" class="text-xs font-bold text-primary hover:underline">{{ __('admin.notes') }}</button>
                            @if($u->role !== 'admin')
                                @if($u->is_suspended)
                                    <form method="POST" action="{{ route('admin.users.activate', $u->id) }}" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'إعادة تفعيل الحساب؟' : 'Reactivate this account?' }}')">@csrf
                                        <button class="px-2.5 py-1 rounded text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 transition">{{ __('admin.activate') }}</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.users.suspend', $u->id) }}" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'تعليق الحساب؟' : 'Suspend this account?' }}')">@csrf
                                        <button class="px-2.5 py-1 rounded text-xs font-bold bg-rose-600 text-white hover:bg-rose-700 transition">{{ __('admin.suspend') }}</button>
                                    </form>
                                @endif
                            @else
                                <span class="text-xs font-bold text-tertiary">{{ __('admin.admin_account') }}</span>
                            @endif
                        </div>
                        <form x-show="n" x-cloak method="POST" action="{{ route('admin.users.notes', $u->id) }}" class="mt-2">@csrf
                            <textarea name="admin_notes" rows="2" class="input text-xs" placeholder="{{ __('admin.internal_notes') }}">{{ $u->admin_notes }}</textarea>
                            <button class="btn-ghost text-xs mt-1">{{ __('admin.save_notes') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">{{ $users->links() }}</div>
@endsection
