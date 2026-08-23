@extends('layouts.admin')
@section('title', 'تحليل الزوار والأخطاء')
@section('content')
<div x-data="{ activeTab: '{{ request('active_tab', request()->has('errors_page') ? 'errors' : 'visits') }}' }">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-primary mb-1">تحليل الزوار والأخطاء التقنية</h1>
            <p class="text-sm text-tertiary">مراقبة حية لنشاط الزوار ورصد الأخطاء التي تواجههم تلقائياً.</p>
        </div>
        
        <!-- Tabs Nav -->
        <div class="flex bg-white rounded-lg p-1 border border-mist shadow-sm">
            <button @click="activeTab = 'visits'" :class="{'bg-primary text-white font-bold': activeTab === 'visits', 'text-tertiary hover:bg-mist': activeTab !== 'visits'}" class="px-4 py-2 rounded-md text-sm transition">تحركات الزوار</button>
            <button @click="activeTab = 'errors'" :class="{'bg-primary text-white font-bold': activeTab === 'errors', 'text-tertiary hover:bg-mist': activeTab !== 'errors'}" class="px-4 py-2 rounded-md text-sm transition">الأخطاء التقنية</button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="card p-5">
            <div class="text-xs text-tertiary mb-1">زيارات اليوم</div>
            <div class="text-3xl font-black text-primary">{{ $stats['visitors_today'] }}</div>
        </div>
        <div class="card p-5">
            <div class="text-xs text-tertiary mb-1">زوار فريدون (اليوم)</div>
            <div class="text-3xl font-black text-emerald-600">{{ $stats['unique_visitors_today'] }}</div>
        </div>
        <div class="card p-5">
            <div class="text-xs text-tertiary mb-1">أخطاء برمجية اليوم</div>
            <div class="text-3xl font-black text-rose-600">{{ $stats['errors_today'] }}</div>
        </div>
        <div class="card p-5">
            <div class="text-xs text-tertiary mb-1">إجمالي الأخطاء</div>
            <div class="text-3xl font-black text-amber-600">{{ $stats['total_errors'] }}</div>
        </div>
    </div>

    <!-- Tab 1: Visitors -->
    <div x-show="activeTab === 'visits'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-cloak>
        <div class="grid lg:grid-cols-[300px_1fr] gap-8 mb-8">
            <!-- Top Pages -->
            <div class="card p-6 h-fit">
                <h3 class="font-bold text-primary mb-4 text-lg">أكثر الصفحات زيارة (آخر 7 أيام)</h3>
                <div class="space-y-3">
                    @foreach($topPages as $page)
                    <div class="flex items-center justify-between border-b border-mist pb-2 last:border-0">
                        <a href="{{ $page->url }}" target="_blank" class="text-sm text-secondary hover:underline truncate max-w-[80%]" dir="ltr">{{ $page->url }}</a>
                        <span class="badge bg-mist text-primary">{{ $page->total }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Visitors Paginated -->
            <div class="card p-6">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <h3 class="font-bold text-primary text-lg">سجل الزيارات (مفصل)</h3>
                    <form method="GET" class="flex items-center gap-2">
                        <input type="hidden" name="active_tab" value="visits">
                        <input type="text" name="visitor_ip" value="{{ request('visitor_ip') }}" placeholder="بحث بـ IP" class="input !py-1 !px-2 text-sm max-w-[150px]">
                        <input type="text" name="visitor_url" value="{{ request('visitor_url') }}" placeholder="بحث بالرابط" class="input !py-1 !px-2 text-sm max-w-[150px]">
                        <button class="btn-primary !py-1 !px-3 text-sm">تصفية</button>
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-right">
                        <thead>
                            <tr class="bg-mist/50 text-tertiary border-b border-mist">
                                <th class="py-3 px-4 font-bold">الوقت</th>
                                <th class="py-3 px-4 font-bold">الزائر</th>
                                <th class="py-3 px-4 font-bold">IP</th>
                                <th class="py-3 px-4 font-bold">الرابط</th>
                                <th class="py-3 px-4 font-bold">المتصفح</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-mist">
                            @forelse($recentVisitors as $log)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-4 text-xs whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                <td class="py-3 px-4 font-bold text-primary">{{ $log->user->name ?? 'غير مسجل' }}</td>
                                <td class="py-3 px-4 text-xs">{{ $log->ip_address }}</td>
                                <td class="py-3 px-4" dir="ltr">
                                    <span class="truncate block max-w-[200px] text-xs text-tertiary" title="{{ $log->url }}">{{ $log->url }}</span>
                                </td>
                                <td class="py-3 px-4 text-xs text-slate-400">
                                    <span class="line-clamp-1" title="{{ $log->user_agent }}">{{ $log->user_agent }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-tertiary font-bold">لا توجد زيارات مسجلة بعد.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 no-print flex justify-center">
                    {{ $recentVisitors->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 2: Errors -->
    <div x-show="activeTab === 'errors'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-cloak>
        <div class="card p-6">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <h3 class="font-bold text-rose-600 text-lg">سجل الأخطاء التقنية (Exceptions)</h3>
                <form method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="active_tab" value="errors">
                    <input type="text" name="error_search" value="{{ request('error_search') }}" placeholder="ابحث بالرابط أو رسالة الخطأ" class="input !py-1 !px-2 text-sm max-w-[250px]">
                    <button class="btn-primary !py-1 !px-3 text-sm">بحث</button>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right">
                    <thead>
                        <tr class="bg-mist/50 text-tertiary border-b border-mist">
                            <th class="py-3 px-4 font-bold">الوقت</th>
                            <th class="py-3 px-4 font-bold">المستخدم</th>
                            <th class="py-3 px-4 font-bold">الرابط / الصفحة</th>
                            <th class="py-3 px-4 font-bold">رسالة الخطأ</th>
                            <th class="py-3 px-4 font-bold text-center">التفاصيل</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-mist">
                        @forelse($recentErrors as $err)
                        <tr x-data="{ expanded: false }" class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 text-xs whitespace-nowrap">{{ $err->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-3 px-4 font-bold text-primary">{{ $err->user->name ?? 'زائر' }}</td>
                            <td class="py-3 px-4" dir="ltr">
                                <span class="truncate block max-w-[200px] text-xs text-tertiary" title="{{ $err->url }}">{{ $err->url }}</span>
                            </td>
                            <td class="py-3 px-4 text-rose-600 font-semibold text-xs">
                                <span class="line-clamp-2" title="{{ $err->message }}">{{ $err->message }}</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button @click="expanded = !expanded" class="btn-outline text-xs !py-1 !px-2">التفاصيل</button>
                            </td>
                        </tr>
                        <tr x-show="expanded" x-cloak style="display: none;">
                            <td colspan="5" class="p-4 bg-slate-900 text-slate-300 font-mono text-[11px] leading-relaxed">
                                <div class="mb-2 font-bold text-rose-400">{{ $err->message }}</div>
                                <div class="overflow-x-auto max-h-64 overflow-y-auto whitespace-pre">{{ $err->stack_trace }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-tertiary font-bold">النظام مستقر، لا توجد أخطاء مرصودة حالياً.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 no-print flex justify-center">
                {{ $recentErrors->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
