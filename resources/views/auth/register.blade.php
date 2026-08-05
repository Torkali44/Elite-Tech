@extends('layouts.auth')
@section('title', __('auth.register_title'))
@section('content')
<h2 class="text-2xl font-extrabold text-primary mb-1">{{ __('auth.register_title') }}</h2>
<p class="text-sm text-tertiary mb-7">{{ __('auth.register_subtitle') }}</p>

<div class="flex bg-mist rounded-lg p-1 mb-6 text-sm font-bold">
    <a href="{{ route('login') }}" class="flex-1 text-center py-2.5 rounded-md text-tertiary hover:text-primary">{{ __('auth.login_title') }}</a>
    <a href="{{ route('register') }}" class="flex-1 text-center py-2.5 rounded-md bg-white text-primary">{{ __('auth.new_account') }}</a>
</div>

@if ($errors->any())
    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 space-y-1">
        @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
    </div>
@endif

<form action="{{ route('register') }}" method="POST" class="space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-bold text-primary mb-1.5">{{ __('auth.name') }}</label>
        <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('auth.name_placeholder') }}" class="input" required autofocus>
    </div>
    <div>
        <label class="block text-sm font-bold text-primary mb-1.5">{{ __('auth.email') }}</label>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('auth.email_placeholder') }}" class="input" required>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-bold text-primary mb-1.5">{{ __('auth.password') }}</label>
            <input type="password" name="password" class="input" required minlength="8">
        </div>
        <div>
            <label class="block text-sm font-bold text-primary mb-1.5">{{ __('auth.password_confirm') }}</label>
            <input type="password" name="password_confirmation" class="input" required minlength="8">
        </div>
    </div>
    <label class="flex items-start gap-2.5 text-sm text-tertiary">
        <input type="checkbox" name="terms" value="1" class="mt-1 rounded accent-primary" required>
        <span>{{ __('auth.terms_agree') }} <a href="{{ route('terms') }}" class="text-secondary underline" target="_blank">{{ __('auth.terms_link') }}</a>
        {{ __('auth.and') }} <a href="{{ route('privacy') }}" class="text-secondary underline" target="_blank">{{ __('auth.privacy_link') }}</a></span>
    </label>
    <button type="submit" class="btn-primary w-full">{{ __('auth.submit_register') }}</button>
</form>

<p class="text-center text-sm text-tertiary mt-6">
    {{ __('auth.already_have_account') }} <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">{{ __('auth.login_title') }}</a>
</p>
@endsection
