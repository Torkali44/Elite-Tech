@extends('layouts.auth')
@section('title', __('auth.reset_title'))

@section('content')
<h2 class="text-2xl font-black text-primary mb-1">{{ __('auth.reset_title') }}</h2>
<p class="text-sm text-tertiary mb-6">
    {{ __('auth.verify_subtitle') }}<br>
    @if(!empty($maskedEmail))
        <span class="font-bold text-primary">{{ $maskedEmail }}</span>
    @endif
</p>

@if (session('ok'))
    <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 font-medium">{{ session('ok') }}</div>
@endif
@if ($errors->any())
    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 font-medium">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('password.update') }}" class="space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-bold text-primary mb-1.5">{{ __('auth.otp_label') }}</label>
        <input type="text" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6"
               class="input text-center text-2xl font-black tracking-[0.4em]" placeholder="{{ __('auth.otp_placeholder') }}" required autofocus>
    </div>
    <div>
        <label class="block text-sm font-bold text-primary mb-1.5">{{ __('auth.new_password') }}</label>
        <input type="password" name="password" class="input" required minlength="8" autocomplete="new-password">
    </div>
    <div>
        <label class="block text-sm font-bold text-primary mb-1.5">{{ __('auth.confirm_password') }}</label>
        <input type="password" name="password_confirmation" class="input" required minlength="8" autocomplete="new-password">
    </div>
    <button type="submit" class="btn-primary w-full">{{ __('auth.submit_reset') }}</button>
</form>

<p class="text-center text-sm text-tertiary mt-6">
    <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">{{ __('auth.back_to_login') }}</a>
</p>
@endsection
