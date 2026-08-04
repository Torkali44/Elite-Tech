@extends('layouts.admin')
@section('title','المستخدمون')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-black text-primary mb-1">المستخدمون</h1>
    <p class="text-sm text-tertiary">فلترة حسب التوثيق والمسار — ملاحظات داخلية وتعليق الحساب</p>
</div>

<form class="card p-4 mb-6 grid sm:grid-cols-4 gap-3">
    <input name="q" value="{{ request('q') }}" class="input" placeholder="اسم / بريد / رقم">
    <select name="kyc" class="input">
        <option value="">كل حالات KYC</option>
        @foreach(['none','pending','approved','rejected','suspended'] as $s)
            <option value="{{ $s }}" @selected(request('kyc')===$s)>{{ $s }}</option>
        @endforeach
    </select>
    <select name="role" class="input">
        <option value="">كل المسارات</option>
        @foreach(['idea_owner','idea_seeker','developer'] as $r)
            <option value="{{ $r }}" @selected(request('role')===$r)>{{ $r }}</option>
        @endforeach
    </select>
    <button class="btn-primary">تطبيق الفلاتر</button>
</form>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-mist text-tertiary text-xs">
                <tr>
                    <th class="text-right p-3">#</th>
                    <th class="text-right p-3">الاسم</th>
                    <th class="text-right p-3">المسار</th>
                    <th class="text-right p-3">KYC</th>
                    <th class="text-right p-3">منتدى</th>
                    <th class="text-right p-3">تاريخ</th>
                    <th class="p-3">إجراءات</th>
                </tr>
            </thead>
            <tbody>
            @foreach($users as $u)
                @php
                    $roleMap = [
                        'idea_owner' => 'صاحب فكرة',
                        'idea_seeker' => 'باحث عن فكرة',
                        'developer' => 'باحث عن عمل',
                        'admin' => 'إدارة',
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
                                    ⚡ أكثر من مسار ({{ count($userRoles) }})
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
                                ⛔ معلّق
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                ✅ نشط
                            </span>
                        @endif
                        <div class="text-[10px] text-tertiary mt-1">KYC: {{ $u->kyc_status }}</div>
                    </td>
                    <td class="p-3">{{ $u->show_in_jobs_forum ? 'نعم' : 'لا' }}</td>
                    <td class="p-3 text-xs text-tertiary">{{ $u->created_at->format('Y-m-d') }}</td>
                    <td class="p-3">
                        <div class="flex gap-2 justify-end items-center">
                            <button type="button" @click="n=!n" class="text-xs font-bold text-primary hover:underline">ملاحظات</button>
                            @if($u->role !== 'admin')
                                @if($u->is_suspended)
                                    <form method="POST" action="{{ route('admin.users.activate', $u->id) }}" onsubmit="return confirm('إعادة تفعيل الحساب؟')">@csrf
                                        <button class="px-2.5 py-1 rounded text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 transition">تفعيل</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.users.suspend', $u->id) }}" onsubmit="return confirm('تعليق الحساب؟')">@csrf
                                        <button class="px-2.5 py-1 rounded text-xs font-bold bg-rose-600 text-white hover:bg-rose-700 transition">تعليق</button>
                                    </form>
                                @endif
                            @else
                                <span class="text-xs font-bold text-tertiary">حساب الإدارة</span>
                            @endif
                        </div>
                        <form x-show="n" x-cloak method="POST" action="{{ route('admin.users.notes', $u->id) }}" class="mt-2">@csrf
                            <textarea name="admin_notes" rows="2" class="input text-xs" placeholder="ملاحظات داخلية...">{{ $u->admin_notes }}</textarea>
                            <button class="btn-ghost text-xs mt-1">حفظ</button>
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
