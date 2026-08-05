@extends('layouts.app')
@section('title', __('jobs.title') . ' — Elite Tech Community')
@section('description', __('jobs.subtitle'))

@section('content')
<section class="bg-primary text-white py-14 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold mb-3">{{ __('jobs.title') }}</h1>
        <p class="text-white/75 text-base max-w-2xl leading-relaxed">{{ __('jobs.subtitle') }}</p>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-10 pb-6 border-b border-slate-200">
        <div>
            <h2 class="text-xl font-black text-primary">{{ app()->getLocale()==='ar' ? 'المواهب المتاحة للمشاريع' : 'Talent Available for Projects' }}</h2>
            <p class="text-xs sm:text-sm text-slate-500 font-semibold mt-1">{{ app()->getLocale()==='ar' ? 'يتم إظهار الأعضاء الموثقين فقط لحماية جودة البيئة التنافسية.' : 'Only verified members are shown to protect the quality of the competitive environment.' }}</p>
        </div>
        @auth
            <a href="{{ route('profile.cv') }}" class="btn-secondary text-sm !py-2.5 shrink-0">
                {{ app()->getLocale()==='ar' ? 'ابنِ سيرتك / انضم للمنتدى' : 'Build Your CV / Join Forum' }}
            </a>
        @else
            <button type="button"
                    class="btn-secondary text-sm !py-2.5 shrink-0"
                    @click="gateOpen=true; gateMsg='{{ app()->getLocale()==='ar' ? 'لبناء سيرتك الذاتية والانضمام لمنتدى التوظيف أنشئ حساباً أولاً.' : 'Create an account to build your CV and join the Jobs Forum.' }}'">
                {{ app()->getLocale()==='ar' ? 'أظهر موهبتك' : 'Showcase Your Talent' }}
            </button>
        @endauth
    </div>

    @if($talents->isEmpty())
        <div class="card p-12 text-center max-w-xl mx-auto shadow-card">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-500 grid place-items-center text-3xl font-bold mx-auto mb-4">👨‍💻</div>
            <h3 class="font-extrabold text-primary text-xl mb-2">{{ __('jobs.no_developers') }}</h3>
            <p class="text-sm text-slate-500 font-medium mb-6">{{ app()->getLocale()==='ar' ? 'سيظهر هنا المطورون والمهندسون فور اجتيازهم لتوثيق الهوية KYC.' : 'Developers and engineers will appear here once they pass KYC verification.' }}</p>
            @auth
                <a href="{{ route('profile.cv') }}" class="btn-primary text-sm">{{ app()->getLocale()==='ar' ? 'أضف ملفك الشخصي الآن' : 'Add Your Profile Now' }}</a>
            @endauth
        </div>
    @else
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($talents as $t)
                <div class="card card-hover p-6 flex flex-col justify-between group transition-all duration-300">
                    <div>
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary to-slate-800 text-white grid place-items-center font-black text-xl shadow-md group-hover:scale-105 transition-transform">
                                    {{ mb_substr($t->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-extrabold text-primary text-base group-hover:text-secondary transition-colors">{{ $t->name }}</div>
                                    <div class="text-xs font-semibold text-slate-500">{{ $t->title ?: (app()->getLocale()==='ar' ? 'مطور برمجيات' : 'Software Developer') }}</div>
                                </div>
                            </div>
                            <span class="badge bg-emerald-50 text-emerald-700 border border-emerald-200">✓ {{ app()->getLocale()==='ar' ? 'موثّق' : 'Verified' }}</span>
                        </div>

                        @php
                            $vis = is_array($t->cv?->visibility) ? $t->cv->visibility : [];
                            $emp = ['full_time'=> __('settings.types.full_time'), 'part_time'=> __('settings.types.part_time'), 'contract'=> __('settings.types.contract')][$vis['employment_type'] ?? ''] ?? null;
                            $work = ['remote'=> __('settings.styles.remote'), 'hybrid'=> __('settings.styles.hybrid'), 'onsite'=> __('settings.styles.onsite')][$vis['work_style'] ?? ''] ?? null;
                        @endphp

                        <p class="text-sm text-slate-600 font-medium line-clamp-3 mb-3 leading-relaxed">
                            {{ $t->bio ?: (app()->getLocale()==='ar' ? 'عضو متميز في مجتمع النخبة التقنية.' : 'Distinguished member of Elite Tech Community.') }}
                        </p>

                        @if($emp || $work || !empty($t->available_for_hire))
                            <div class="flex flex-wrap gap-1.5 mb-3 text-[11px]">
                                @if($t->available_for_hire)<span class="badge bg-secondary/15 text-secondary">{{ __('jobs.available') }}</span>@endif
                                @if($emp)<span class="badge bg-primary/10 text-primary">{{ $emp }}</span>@endif
                                @if($work)<span class="badge bg-mist text-tertiary">{{ $work }}</span>@endif
                            </div>
                        @endif

                        @if($t->cv?->data['skills'] ?? false)
                            <div class="flex flex-wrap gap-1.5 mb-6">
                                @foreach(array_slice($t->cv->data['skills'], 0, 4) as $skill)
                                    <span class="badge bg-slate-100 text-slate-700 border border-slate-200">{{ $skill }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-2.5 pt-4 border-t border-slate-100">
                        <a href="{{ route('profile.show', $t->id) }}" class="btn-outline text-sm flex-1 text-center !py-2">{{ __('jobs.view_cv') }}</a>
                        @auth
                            <a href="{{ route('network.index', ['with' => $t->id]) }}" class="btn-primary text-sm flex-1 text-center !py-2">{{ __('jobs.contact') }}</a>
                        @else
                            <button type="button"
                                    class="btn-primary text-sm flex-1 !py-2"
                                    @click="gateOpen=true; gateMsg='{{ app()->getLocale()==='ar' ? 'التواصل مع المواهب الموثقة يتطلب تسجيل الدخول.' : 'Contacting verified talent requires login.' }}'">
                                {{ __('jobs.contact') }}
                            </button>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-10 flex justify-center">
            {{ $talents->links() }}
        </div>
    @endif
</div>
@endsection
