@extends('layouts.app')
@section('title', app()->getLocale()==='ar' ? 'مجتمع إليت تك — حيث تلتقي الخبرة بالابتكار' : 'Elite Tech Community — Where Expertise Meets Innovation')
@section('description', app()->getLocale()==='ar' ? 'انضم إلى المنصة التقنية الرائدة المدعومة من إليت تك حيث يجتمع نخبة المطورين والمبتكرين لبناء مستقبل التكنولوجيا.' : 'Join the leading tech platform powered by Elite Tech where elite developers and innovators meet to build the future of technology.')

@section('content')

{{-- Hero Section --}}
<section class="relative bg-gradient-to-b from-[#0a192f] via-[#0d2240] to-[#14325a] text-white overflow-hidden py-14 sm:py-24 lg:py-32">
    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-blue-400 via-sky-600 to-transparent pointer-events-none"></div>
    <div class="absolute -top-40 -left-40 w-72 sm:w-96 h-72 sm:h-96 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/2 -right-40 w-72 sm:w-96 h-72 sm:h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="font-black text-2xl sm:text-4xl lg:text-5xl tracking-tight leading-tight sm:leading-tight lg:leading-tight mb-4 sm:mb-6">
            {{ app()->getLocale()==='ar' ? 'مجتمع إيليت تك: حيث تلتقي الخبرة بالابتكار' : 'Elite Tech Community: Where Expertise Meets Innovation' }}
        </h1>
        <p class="text-sm sm:text-base lg:text-lg text-blue-100/80 max-w-3xl mx-auto leading-relaxed mb-8 sm:mb-10 font-normal">
            {{ app()->getLocale()==='ar' ? 'انضم إلى المنصة التقنية الرائدة المدعومة من "إليت تك"، حيث يجتمع نخبة المطورين والمبتكرين لبناء مستقبل التكنولوجيا من خلال التعاون المعرفي والتقني.' : 'Join the leading tech platform powered by "Elite Tech", where top developers and innovators gather to build the future of tech through knowledge & technical collaboration.' }}
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3.5 sm:gap-4 w-full max-w-md sm:max-w-none mx-auto">
            <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold px-8 py-3.5 rounded-xl shadow-lg shadow-orange-500/25 transition-all transform hover:-translate-y-0.5 text-sm sm:text-base">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>{{ app()->getLocale()==='ar' ? 'انضم الآن' : 'Join Now' }}</span>
            </a>
            <a href="{{ route('ideas.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 backdrop-blur border border-white/20 text-white font-bold px-8 py-3.5 rounded-xl transition-all transform hover:-translate-y-0.5 text-sm sm:text-base">
                <svg class="w-5 h-5 text-amber-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                <span>{{ app()->getLocale()==='ar' ? 'استكشف الأفكار' : 'Explore Ideas' }}</span>
            </a>
            <a href="https://elitemsr.com/contactus" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white font-bold px-8 py-3.5 rounded-xl shadow-lg shadow-sky-500/25 transition-all transform hover:-translate-y-0.5 text-sm sm:text-base border border-sky-400/30">
                <svg class="w-5 h-5 text-sky-100 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span>{{ app()->getLocale()==='ar' ? 'تواصل معنا' : 'Contact Us' }}</span>
            </a>
        </div>
    </div>
</section>

{{-- Collaborative Environment Section --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20">
    <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
        <div class="order-1 lg:order-2">
            <div class="relative rounded-2xl overflow-hidden shadow-2xl border border-slate-200 group">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=900&q=80"
                     alt="بيئة تعاونية محفزة"
                     class="w-full h-[240px] sm:h-[360px] lg:h-[420px] object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent"></div>
                <div class="absolute bottom-3 sm:bottom-4 {{ app()->getLocale()==='ar' ? 'right-3 sm:right-4' : 'left-3 sm:left-4' }} bg-white/90 backdrop-blur px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-[11px] sm:text-xs font-bold text-slate-800 shadow">
                    👥 {{ app()->getLocale()==='ar' ? 'مجتمع نخبة المطورين والخبراء' : 'Community of Elite Developers & Experts' }}
                </div>
            </div>
        </div>

        <div class="order-2 lg:order-1 space-y-4 sm:space-y-6">
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                {{ app()->getLocale()==='ar' ? 'بيئة تعاونية محفزة' : 'Inspiring Collaborative Environment' }}
            </h2>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                {{ app()->getLocale()==='ar' ? 'انضم إلى نخبة من العقول الذكية في مجتمع يرتكز على الابتكار وتبادل الخبرات. اكتشف فرصاً جديدة للتعاون في مشاريع طموحة وتطوير مهاراتك من خلال الانخراط المباشر مع محترفين في المجال.' : 'Join a community of top minds centered around innovation & experience sharing. Discover new opportunities to collaborate on ambitious projects and enhance your skills.' }}
            </p>
            <ul class="space-y-3 sm:space-y-4 pt-1 sm:pt-2">
                @foreach(app()->getLocale()==='ar' ? [
                    'مشاريع تعاونية ملهمة',
                    'نقاشات تقنية متقدمة',
                    'بناء شبكة علاقات مهنية قوية'
                ] : [
                    'Inspiring Collaborative Projects',
                    'Advanced Technical Discussions',
                    'Building Strong Professional Network'
                ] as $point)
                <li class="flex items-center gap-3 sm:gap-3.5 text-slate-800 font-bold text-sm sm:text-base">
                    <span class="w-5 sm:w-6 h-5 sm:h-6 rounded-full bg-amber-500/20 text-amber-600 grid place-items-center shrink-0 text-xs sm:text-sm font-bold">✓</span>
                    <span>{{ $point }}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

{{-- Everything You Need For Success --}}
<section class="bg-slate-50/70 border-y border-slate-200 py-12 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-14">
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mb-2 sm:mb-3">
                {{ app()->getLocale()==='ar' ? 'كل ما تحتاجه للنجاح' : 'Everything You Need For Success' }}
            </h2>
            <p class="text-slate-600 text-sm sm:text-base">
                {{ app()->getLocale()==='ar' ? 'نقدم لك أدوات وميزات متكاملة لدعم مسيرتك المهنية' : 'We provide comprehensive tools & features to support your career' }}
            </p>
        </div>

        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6 lg:gap-8">
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-sm hover:shadow-md transition text-center flex flex-col items-center">
                <div class="w-12 sm:w-14 h-12 sm:h-14 rounded-2xl bg-[#0f284e] text-white grid place-items-center mb-5 sm:mb-6 shadow-md shadow-blue-900/10">
                    <svg class="w-6 sm:w-7 h-6 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 01-2-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <h3 class="font-extrabold text-lg sm:text-xl text-slate-900 mb-2 sm:mb-3">{{ app()->getLocale()==='ar' ? 'مشاريع تعاونية' : 'Collaborative Projects' }}</h3>
                <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                    {{ app()->getLocale()==='ar' ? 'ساهم في مشاريع تقنية ملهمة وتبادل الأفكار مع أفضل المطورين في مجالك.' : 'Contribute to inspiring tech projects and exchange ideas with top developers.' }}
                </p>
            </div>

            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-sm hover:shadow-md transition text-center flex flex-col items-center">
                <div class="w-12 sm:w-14 h-12 sm:h-14 rounded-2xl bg-amber-500 text-white grid place-items-center mb-5 sm:mb-6 shadow-md shadow-amber-500/20">
                    <svg class="w-6 sm:w-7 h-6 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="font-extrabold text-lg sm:text-xl text-slate-900 mb-2 sm:mb-3">{{ app()->getLocale()==='ar' ? 'تواصل مع الخبراء' : 'Connect with Experts' }}</h3>
                <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                    {{ app()->getLocale()==='ar' ? 'ابنِ شبكة علاقات مهنية مع مهندسي إليت تك وتزداد وضوح الأفكار.' : 'Build a professional network with Elite Tech engineers to turn ideas into reality.' }}
                </p>
            </div>

            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-sm hover:shadow-md transition text-center flex flex-col items-center sm:col-span-2 md:col-span-1">
                <div class="w-12 sm:w-14 h-12 sm:h-14 rounded-2xl bg-[#0a192f] text-white grid place-items-center mb-5 sm:mb-6 shadow-md shadow-slate-900/10">
                    <svg class="w-6 sm:w-7 h-6 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="font-extrabold text-lg sm:text-xl text-slate-900 mb-2 sm:mb-3">{{ app()->getLocale()==='ar' ? 'إرشاد تقني متخصص' : 'Specialized Technical Guidance' }}</h3>
                <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                    {{ app()->getLocale()==='ar' ? 'احصل على توجيه مباشر من خبراء لتطوير مهاراتك البرمجية والقيادية.' : 'Get direct guidance from experts to develop your coding & leadership skills.' }}
                </p>
            </div>
        </div>
    </div>
</section>

@if(isset($featuredIdeas) && $featuredIdeas->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 sm:mb-10">
        <div>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ app()->getLocale()==='ar' ? 'أبرز الأفكار المنشورة' : 'Featured Ideas' }}</h2>
            <p class="text-slate-600 mt-1 sm:mt-2 text-sm sm:text-base">{{ app()->getLocale()==='ar' ? 'استكشف الأفكار التقنية الجاهزة للتنفيذ في بنك الأفكار.' : 'Explore tech ideas ready for implementation in the Ideas Bank.' }}</p>
        </div>
        <a href="{{ route('ideas.index') }}" class="btn-outline text-xs sm:text-sm shrink-0 self-start sm:self-auto">{{ app()->getLocale()==='ar' ? 'عرض كل الأفكار ←' : 'View All Ideas →' }}</a>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 lg:gap-6">
        @foreach($featuredIdeas as $idea)
            <x-idea-card :idea="$idea" />
        @endforeach
    </div>
</section>
@endif

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12 sm:pb-20">
    <div class="rounded-2xl sm:rounded-3xl bg-gradient-to-r from-[#0a192f] to-[#163866] text-white p-6 sm:p-10 lg:p-14 flex flex-col lg:flex-row lg:items-center justify-between gap-6 sm:gap-8 shadow-xl">
        <div class="max-w-xl">
            <h2 class="text-xl sm:text-2xl lg:text-3xl font-black mb-2 sm:mb-3">{{ app()->getLocale()==='ar' ? 'هل أنت جاهز لبناء مستقبلك التقني؟' : 'Ready to Build Your Tech Future?' }}</h2>
            <p class="text-blue-100/80 text-xs sm:text-sm lg:text-base leading-relaxed">
                {{ app()->getLocale()==='ar' ? 'سجّل حسابك الآن، اختر مسارك المناسب، وابدأ بالتفاعل والتعاون مع نخبة المطورين وأصحاب الأفكار.' : 'Sign up now, choose your suitable path, and start collaborating with top developers & idea owners.' }}
            </p>
        </div>
        <div class="flex flex-col sm:flex-row items-center gap-3 shrink-0">
            <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold px-8 py-3.5 sm:py-4 rounded-xl shadow-lg shadow-orange-500/25 transition text-sm sm:text-base">
                {{ app()->getLocale()==='ar' ? 'انضم مجاناً اليوم' : 'Join Free Today' }}
            </a>
            <a href="https://elitemsr.com/contactus" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto inline-flex items-center justify-center bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold px-7 py-3.5 sm:py-4 rounded-xl transition text-sm sm:text-base">
                {{ app()->getLocale()==='ar' ? 'تواصل معنا' : 'Contact Us' }}
            </a>
        </div>
    </div>
</section>
@endsection
