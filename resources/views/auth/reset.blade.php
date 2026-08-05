@extends('layouts.auth')
@section('title', __('auth.reset_title'))
@section('content')
<h2 class="text-2xl font-black text-primary mb-2">{{ __('auth.reset_title') }}</h2>

@if ($errors->any())
    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('password.update') }}" class="space-y-4">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div>
        <label class="block text-sm font-semibold text-primary mb-1.5">{{ __('auth.email') }}</label>
        <input type="email" name="email" value="{{ old('email', $email) }}" class="input" required>
    </div>
    <div>
        <label class="block text-sm font-semibold text-primary mb-1.5">{{ __('auth.new_password') }}</label>
        <input type="password" name="password" class="input" required minlength="8" autocomplete="new-password">
    </div>
    <div>
        <label class="block text-sm font-semibold text-primary mb-1.5">{{ __('auth.confirm_password') }}</label>
        <input type="password" name="password_confirmation" class="input" required minlength="8" autocomplete="new-password">
    </div>
    <button type="submit" class="btn-primary">{{ __('auth.submit_reset') }}</button>
</form>
@endsection
