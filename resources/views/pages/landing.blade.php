@extends('layouts.app')
@section('title', 'مجتمع إليت تك — حيث تلتقي الخبرة بالابتكار')
@section('description', 'انضم إلى المنصة التقنية الرائدة المدعومة من إليت تك حيث يجتمع نخبة المطورين والمبتكرين لبناء مستقبل التكنولوجيا.')

@section('content')


{{-- Hero Section matching screenshot --}}
<section class="relative bg-gradient-to-b from-[#0a192f] via-[#0d2240] to-[#14325a] text-white overflow-hidden py-24 lg:py-32">
    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-blue-400 via-sky-600 to-transparent pointer-events-none"></div>
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/2 -right-40 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="font-black text-3xl sm:text-4xl lg:text-5xl tracking-tight leading-tight mb-6">
            مجتمع إيليت تك: حيث تلتقي الخبرة بالابتكار
        </h1>
        <p class="text-base sm:text-lg text-blue-100/80 max-w-3xl mx-auto leading-relaxed mb-10 font-normal">
            انضم إلى المنصة التقنية الرائدة المدعومة من "إليت تك"، حيث يجتمع نخبة المطورين والمبتكرين لبناء مستقبل التكنولوجيا من خلال التعاون المعرفي والتقني.
        </p>

        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold px-8 py-3.5 rounded-xl shadow-lg shadow-orange-500/25 transition-all transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>انضم الآن</span>
            </a>
            <a href="{{ route('ideas.index') }}" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 backdrop-blur border border-white/20 text-white font-bold px-8 py-3.5 rounded-xl transition-all transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                <span>استكشف الأفكار</span>
            </a>
        </div>
    </div>
</section>

{{-- Collaborative Environment Section --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
        <div class="order-1 lg:order-2">
            <div class="relative rounded-2xl overflow-hidden shadow-2xl border border-slate-200 group">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=900&q=80"
                     alt="بيئة تعاونية محفزة"
                     class="w-full h-[360px] sm:h-[420px] object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent"></div>
                <div class="absolute bottom-4 right-4 bg-white/90 backdrop-blur px-4 py-2 rounded-lg text-xs font-bold text-slate-800 shadow">
                    👥 مجتمع نخبة المطورين والخبراء
                </div>
            </div>
        </div>

        <div class="order-2 lg:order-1 space-y-6">
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">
                بيئة تعاونية محفزة
            </h2>
            <p class="text-slate-600 text-base leading-relaxed">
                انضم إلى نخبة من العقول الذكية في مجتمع يرتكز على الابتكار وتبادل الخبرات. اكتشف فرصاً جديدة للتعاون في مشاريع طموحة وتطوير مهاراتك من خلال الانخراط المباشر مع محترفين في المجال.
            </p>
            <ul class="space-y-4 pt-2">
                @foreach([
                    'مشاريع تعاونية ملهمة',
                    'نقاشات تقنية متقدمة',
                    'بناء شبكة علاقات مهنية قوية'
                ] as $point)
                <li class="flex items-center gap-3.5 text-slate-800 font-bold text-base">
                    <span class="w-6 h-6 rounded-full bg-amber-500/20 text-amber-600 grid place-items-center shrink-0 text-sm font-bold">✓</span>
                    <span>{{ $point }}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

{{-- Everything You Need For Success --}}
<section class="bg-slate-50/70 border-y border-slate-200 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <h2 class="text-3xl font-black text-slate-900 tracking-tight mb-3">
                كل ما تحتاجه للنجاح
            </h2>
            <p class="text-slate-600 text-base">
                نقدم لك أدوات وميزات متكاملة لدعم مسيرتك المهنية
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl p-8 border border-slate-200/80 shadow-sm hover:shadow-md transition text-center flex flex-col items-center">
                <div class="w-14 h-14 rounded-2xl bg-[#0f284e] text-white grid place-items-center mb-6 shadow-md shadow-blue-900/10">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <h3 class="font-extrabold text-xl text-slate-900 mb-3">مشاريع تعاونية</h3>
                <p class="text-slate-600 text-sm leading-relaxed">
                    ساهم في مشاريع تقنية ملهمة وتبادل الأفكار مع أفضل المطورين في مجالك.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-8 border border-slate-200/80 shadow-sm hover:shadow-md transition text-center flex flex-col items-center">
                <div class="w-14 h-14 rounded-2xl bg-amber-500 text-white grid place-items-center mb-6 shadow-md shadow-amber-500/20">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="font-extrabold text-xl text-slate-900 mb-3">تواصل مع الخبراء</h3>
                <p class="text-slate-600 text-sm leading-relaxed">
                    ابن شبكة علاقات مهنية مع مهندسي إليت تك ويزداد وضوح الأفكار.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-8 border border-slate-200/80 shadow-sm hover:shadow-md transition text-center flex flex-col items-center">
                <div class="w-14 h-14 rounded-2xl bg-[#0a192f] text-white grid place-items-center mb-6 shadow-md shadow-slate-900/10">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="font-extrabold text-xl text-slate-900 mb-3">إرشاد تقني متخصص</h3>
                <p class="text-slate-600 text-sm leading-relaxed">
                    احصل على توجيه مباشر من خبراء لتطوير مهاراتك البرمجية والقيادية.
                </p>
            </div>
        </div>
    </div>
</section>

@if(isset($featuredIdeas) && $featuredIdeas->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">أبرز الأفكار المنشورة</h2>
            <p class="text-slate-600 mt-2 text-base">استكشف الأفكار التقنية الجاهزة للتنفيذ في بنك الأفكار.</p>
        </div>
        <a href="{{ route('ideas.index') }}" class="btn-outline text-sm shrink-0">عرض كل الأفكار ←</a>
    </div>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($featuredIdeas as $idea)
            <x-idea-card :idea="$idea" />
        @endforeach
    </div>
</section>
@endif

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
    <div class="rounded-3xl bg-gradient-to-r from-[#0a192f] to-[#163866] text-white p-10 sm:p-14 flex flex-col lg:flex-row lg:items-center justify-between gap-8 shadow-xl">
        <div class="max-w-xl">
            <h2 class="text-2xl sm:text-3xl font-black mb-3">هل أنت جاهز لبناء مستقبلك التقني؟</h2>
            <p class="text-blue-100/80 text-base leading-relaxed">
                سجّل حسابك الآن، اختر مسارك المناسب، وابدأ بالتفاعل والتعاون مع نخبة المطورين وأصحاب الأفكار.
            </p>
        </div>
        <a href="{{ route('register') }}" class="inline-flex items-center justify-center bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold px-8 py-4 rounded-xl shadow-lg shadow-orange-500/25 transition shrink-0">
            انضم مجاناً اليوم
        </a>
    </div>
</section>
@endsection
