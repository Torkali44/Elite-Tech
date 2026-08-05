@extends('layouts.auth')
@section('title', __('auth.login_title'))
@section('content')
<h2 class="text-2xl font-extrabold text-primary mb-1">{{ __('auth.welcome') }}</h2>
<p class="text-sm text-tertiary mb-7">{{ __('auth.login_subtitle') }}</p>

<div class="flex bg-mist rounded-lg p-1 mb-6 text-sm font-bold">
    <a href="{{ route('login') }}" class="flex-1 text-center py-2.5 rounded-md bg-white text-primary">{{ __('auth.login_title') }}</a>
    <a href="{{ route('register') }}" class="flex-1 text-center py-2.5 rounded-md text-tertiary hover:text-primary">{{ __('auth.new_account') }}</a>
</div>

@if ($errors->any())
    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3">{{ $errors->first() }}</div>
@endif

<form action="{{ route('login') }}" method="POST" class="space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-bold text-primary mb-1.5">{{ __('auth.email') }}</label>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('auth.email_placeholder') }}" class="input" required autofocus>
    </div>
    <div>
        <div class="flex items-center justify-between mb-1.5">
            <label class="block text-sm font-bold text-primary">{{ __('auth.password') }}</label>
            <a href="{{ route('password.request') }}" class="text-xs text-secondary font-semibold hover:underline">{{ __('auth.forgot_password') }}</a>
        </div>
        <input type="password" name="password" placeholder="••••••••" class="input" required>
    </div>
    <label class="flex items-center gap-2 text-sm text-tertiary">
        <input type="checkbox" name="remember" class="rounded border-slate-300 accent-primary"> {{ __('auth.remember_me') }}
    </label>
    <button type="submit" class="btn-primary w-full">{{ __('auth.submit_login') }}</button>
</form>

<div class="my-6 flex items-center gap-3 text-xs text-tertiary">
    <div class="flex-1 h-px bg-slate-200"></div>
    {{ __('auth.or') }}
    <div class="flex-1 h-px bg-slate-200"></div>
</div>

<a href="{{ route('home') }}" class="block text-center text-sm font-semibold text-tertiary hover:text-primary transition">
    {{ __('navigation.browse_as_guest') }}
</a>
@endsection
