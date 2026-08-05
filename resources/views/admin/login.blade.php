@extends('layouts.auth')
@section('title', __('auth.admin_login_title'))
@section('content')
<div class="mb-6 flex items-center gap-2">
    <span class="badge bg-primary/10 text-primary">{{ __('admin.panel_title') }}</span>
</div>
<h2 class="text-2xl font-black text-primary mb-2">{{ __('auth.admin_login_title') }}</h2>
<p class="text-sm text-tertiary mb-6">{{ __('auth.admin_login_subtitle') }}</p>

@if ($errors->any())
    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3">
        {{ $errors->first() }}
    </div>
@endif
@if (session('error'))
    <div class="mb-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm px-4 py-3">
        {{ session('error') }}
    </div>
@endif

<form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4" autocomplete="on">
    @csrf
    <div>
        <label class="block text-sm font-bold text-primary mb-1.5">{{ __('auth.email') }}</label>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com" class="input" required autofocus dir="ltr">
    </div>
    <div>
        <label class="block text-sm font-bold text-primary mb-1.5">{{ __('auth.password') }}</label>
        <input type="password" name="password" value="" placeholder="••••••••" class="input" required dir="ltr" autocomplete="current-password">
    </div>
    <button type="submit" class="btn-primary w-full">{{ __('auth.admin_submit') }}</button>
</form>

<a href="{{ route('home') }}" class="block text-center text-sm text-tertiary hover:text-primary mt-6">{{ __('auth.back_to_login') }}</a>
<p class="text-center text-xs text-tertiary mt-3">{{ app()->getLocale() === 'ar' ? 'دخول الأعضاء من' : 'Members login at' }} <a href="{{ route('login') }}" class="text-secondary font-bold">/login</a></p>
@endsection
