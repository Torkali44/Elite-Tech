@extends('layouts.auth')
@section('title', __('auth.path_title'))
@section('content')
@php $current = old('roles', auth()->user()->roles ?? []); if (!is_array($current)) $current = []; @endphp

@if (session('error'))
    <div class="mb-4 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 text-sm px-4 py-3">{{ session('error') }}</div>
@endif
@if (session('ok'))
    <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3">{{ session('ok') }}</div>
@endif

<div class="text-center mb-8">
    <h2 class="text-2xl font-extrabold text-primary mb-1">{{ __('auth.path_title') }}</h2>
    <p class="text-sm text-tertiary leading-relaxed">{{ __('auth.path_subtitle') }}</p>
</div>

@if ($errors->any())
    <div class="mb-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3">{{ $errors->first() }}</div>
@endif

<form action="{{ route('auth.path') }}" method="POST" class="space-y-3" id="path-form">
    @csrf

    <label class="block p-5 rounded-xl border border-mist hover:border-primary/40 cursor-pointer transition path-card">
        <div class="flex items-start gap-4">
            <input type="checkbox" name="roles[]" value="idea_owner" class="mt-1 w-4 h-4 accent-primary role-cb"
                   @checked(in_array('idea_owner', $current, true))>
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="font-extrabold text-primary">{{ __('dashboard.roles.idea_owner') }}</span>
                    <span class="badge bg-primary/10 text-primary">{{ app()->getLocale() === 'ar' ? 'KYC قبل النشر' : 'KYC before publish' }}</span>
                </div>
                <p class="text-sm text-tertiary leading-relaxed">{{ app()->getLocale() === 'ar' ? 'أنشر أفكاراً في بنك الأفكار وأدير طلبات الانضمام بعد التوثيق.' : 'Publish ideas in the Ideas Bank and manage joining requests after verification.' }}</p>
            </div>
        </div>
    </label>

    <label class="block p-5 rounded-xl border border-mist hover:border-primary/40 cursor-pointer transition path-card" id="seeker-card">
        <div class="flex items-start gap-4">
            <input type="checkbox" name="roles[]" value="idea_seeker" class="mt-1 w-4 h-4 accent-primary role-cb" id="role-seeker"
                   @checked(in_array('idea_seeker', $current, true))>
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="font-extrabold text-primary">{{ __('dashboard.roles.idea_seeker') }}</span>
                    <span class="badge bg-emerald-50 text-emerald-700">{{ app()->getLocale() === 'ar' ? 'تصفح حر · KYC للتنفيذ' : 'Free Browse · KYC for implementation' }}</span>
                </div>
                <p class="text-sm text-tertiary leading-relaxed">{{ app()->getLocale() === 'ar' ? 'أتصفح بنك الأفكار بحرية. «الرغبة في التنفيذ» تتطلب KYC.' : 'Browse Ideas Bank freely. "Request Implementation" requires KYC.' }}</p>
            </div>
        </div>
    </label>

    <label class="block p-5 rounded-xl border border-mist hover:border-primary/40 cursor-pointer transition path-card" id="dev-card">
        <div class="flex items-start gap-4">
            <input type="checkbox" name="roles[]" value="developer" class="mt-1 w-4 h-4 accent-primary role-cb" id="role-developer"
                   @checked(in_array('developer', $current, true))>
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="font-extrabold text-primary">{{ __('dashboard.roles.developer') }}</span>
                    <span class="badge bg-neutral text-tertiary">{{ app()->getLocale() === 'ar' ? 'CV حر بدون KYC' : 'Free CV without KYC' }}</span>
                </div>
                <p class="text-sm text-tertiary leading-relaxed mb-3">{{ app()->getLocale() === 'ar' ? 'أبني سيرتي وأستخرج PDF مجاناً.' : 'Build my resume and export PDF for free.' }}</p>
                <div id="jobs-box" class="mt-2 p-3 rounded-lg bg-neutral border border-mist {{ in_array('developer', $current, true) ? '' : 'hidden' }}">
                    <label class="flex items-start gap-2 text-sm">
                        <input type="checkbox" name="wants_jobs_forum" value="1" class="mt-0.5 accent-secondary"
                               @checked(auth()->user()->wants_jobs_forum)>
                        <span>
                            <span class="font-bold text-primary">{{ __('auth.jobs_forum_interest') }}</span>
                            <span class="block text-tertiary text-xs mt-0.5">{{ app()->getLocale() === 'ar' ? 'يتطلب KYC + موافقة الإدارة للظهور العام.' : 'Requires KYC + admin approval for public listing.' }}</span>
                        </span>
                    </label>
                </div>
            </div>
        </div>
    </label>

    <button type="submit" class="btn-primary w-full mt-4" id="submit-btn">{{ __('auth.path_submit') }}</button>
    <a href="{{ route('dashboard') }}" class="block text-center text-sm text-tertiary hover:text-primary mt-2">{{ __('navigation.dashboard') }}</a>
</form>

<script>
document.getElementById('role-developer')?.addEventListener('change', function () {
    document.getElementById('jobs-box')?.classList.toggle('hidden', !this.checked);
});
document.querySelectorAll('.role-cb').forEach(function (cb) {
    cb.addEventListener('change', function () {
        const card = this.closest('.path-card');
        if (!card) return;
        card.classList.toggle('border-primary', this.checked);
        card.classList.toggle('bg-primary/5', this.checked);
    });
    if (cb.checked) cb.dispatchEvent(new Event('change'));
});
</script>
@endsection
