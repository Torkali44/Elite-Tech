@extends('layouts.auth')
@section('title', __('auth.verify_title'))

@section('content')
<div x-data="otpVerify({
    expiresIn: {{ (int)($expiresIn ?? 300) }},
    resendCooldown: {{ (int)($resendCooldown ?? 0) }},
})" x-init="init()">

    {{-- Title --}}
    <h2 class="text-2xl font-black text-primary mb-1">{{ __('auth.verify_title') }}</h2>
    <p class="text-sm text-tertiary mb-5 leading-relaxed">
        {{ __('auth.verify_subtitle') }}<br>
        @if(!empty($maskedEmail))
            <span class="font-bold text-primary">{{ $maskedEmail }}</span>
        @endif
    </p>

    {{-- Flash messages --}}
    @if(session('ok'))
        <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 font-medium">
            {{ session('ok') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 font-medium">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 font-medium">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Attempts warning --}}
    @if(isset($attemptsLeft) && $attemptsLeft !== null && $attemptsLeft <= 3 && $attemptsLeft > 0)
        <div class="mb-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm px-4 py-2.5 font-medium flex items-center gap-2">
            <span>⚠️</span>
            <span>{{ app()->getLocale() === 'ar' ? "المحاولات المتبقية: {$attemptsLeft}" : "Attempts remaining: {$attemptsLeft}" }}</span>
        </div>
    @endif

    {{-- Countdown timer --}}
    <div class="mb-5" x-show="timeLeft > 0" x-cloak>
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-xs font-semibold text-tertiary">
                {{ app()->getLocale() === 'ar' ? 'الرمز صالح لمدة:' : 'Code valid for:' }}
            </span>
            <span class="text-sm font-black tabular-nums"
                  :class="timeLeft <= 60 ? 'text-rose-600' : 'text-primary'"
                  x-text="formatTime(timeLeft)">05:00</span>
        </div>
        <div class="h-1.5 bg-mist rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all duration-1000"
                 :class="timeLeft <= 60 ? 'bg-rose-500' : (timeLeft <= 120 ? 'bg-amber-500' : 'bg-secondary')"
                 :style="`width: ${(timeLeft / {{ (int)($expiresIn ?? 300) }}) * 100}%`">
            </div>
        </div>
    </div>

    {{-- Expired notice --}}
    <div x-show="timeLeft <= 0" x-cloak
         class="mb-5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 font-medium text-center">
        {{ app()->getLocale() === 'ar'
            ? 'انتهت صلاحية الرمز. اطلب رمزاً جديداً أو أعِد التسجيل.'
            : 'Code expired. Request a new code or register again.' }}
    </div>

    {{-- OTP FORM --}}
    <form action="{{ route('auth.verify') }}" method="POST"
          x-ref="form"
          @submit.prevent="handleSubmit">
        @csrf

        {{-- 6-digit OTP boxes --}}
        <div class="mb-5">
            <label class="block text-sm font-bold text-primary mb-3">
                {{ __('auth.otp_label') }}
            </label>
            <div class="flex gap-2 justify-center sm:gap-3" id="otp-boxes" dir="ltr">
                @for ($i = 0; $i < 6; $i++)
                    <input type="text"
                           inputmode="numeric"
                           maxlength="1"
                           id="otp-box-{{ $i }}"
                           data-otp-index="{{ $i }}"
                           class="w-11 h-13 sm:w-13 sm:h-15 text-center text-xl sm:text-2xl font-black rounded-xl border-2 border-mist bg-neutral focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all duration-150 select-all"
                           style="width:2.75rem;height:3.25rem;line-height:3.25rem;"
                           autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}"
                           @input="onDigitInput($event, {{ $i }})"
                           @keydown="onDigitKeydown($event, {{ $i }})"
                           @paste.prevent="onOtpPaste($event)"
                           @focus="$el.select()"
                           {{ $i === 0 ? 'autofocus' : '' }}>
                @endfor
            </div>
            {{-- Hidden input carries the full 6-digit code to server --}}
            <input type="hidden" name="code" id="otp-hidden-value" value="{{ old('code') }}">
        </div>

        {{-- Submit button --}}
        <button type="submit"
                id="verify-btn"
                class="btn-primary w-full relative"
                :disabled="submitting || (timeLeft <= 0 && !isLegacy)"
                :class="(submitting || (timeLeft <= 0 && !isLegacy)) ? 'opacity-60 cursor-not-allowed' : ''">
            <span x-show="!submitting">{{ __('auth.submit_verify') }}</span>
            <span x-show="submitting" x-cloak class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                {{ app()->getLocale() === 'ar' ? 'جارٍ التحقق...' : 'Verifying...' }}
            </span>
        </button>
    </form>

    {{-- Resend section --}}
    <div class="mt-6 text-center space-y-1">
        <p class="text-sm text-tertiary">{{ __('auth.no_code_received') }}</p>

        <div class="flex items-center justify-center gap-1.5 text-sm">
            {{-- Cooldown: show countdown --}}
            <template x-if="resendSecondsLeft > 0">
                <span class="text-tertiary">
                    {{ app()->getLocale() === 'ar' ? 'إعادة الإرسال بعد' : 'Resend in' }}
                    <span class="font-black text-primary tabular-nums" x-text="resendSecondsLeft"></span>
                    {{ app()->getLocale() === 'ar' ? 'ثانية' : 's' }}
                </span>
            </template>

            {{-- Ready to resend --}}
            <template x-if="resendSecondsLeft <= 0">
                <a href="{{ route('auth.verify', ['resend' => 1]) }}"
                   class="text-secondary font-bold hover:underline inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    {{ __('auth.resend_code') }}
                </a>
            </template>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function otpVerify({ expiresIn, resendCooldown }) {
    return {
        timeLeft: expiresIn,
        resendSecondsLeft: resendCooldown,
        submitting: false,
        isLegacy: {{ isset($legacyFlow) && $legacyFlow ? 'true' : 'false' }},

        init() {
            if (this.timeLeft > 0 || this.resendSecondsLeft > 0) {
                setInterval(() => {
                    if (this.timeLeft > 0) this.timeLeft--;
                    if (this.resendSecondsLeft > 0) this.resendSecondsLeft--;
                }, 1000);
            }
            // Populate boxes from old() value if present
            const oldCode = document.getElementById('otp-hidden-value').value;
            if (oldCode && oldCode.length === 6) {
                for (let i = 0; i < 6; i++) {
                    const box = document.getElementById(`otp-box-${i}`);
                    if (box) box.value = oldCode[i] || '';
                }
            }
        },

        formatTime(seconds) {
            const m = Math.floor(seconds / 60).toString().padStart(2, '0');
            const s = (seconds % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        },

        onDigitInput(e, index) {
            let val = e.target.value.replace(/\D/g, '');
            e.target.value = val ? val.charAt(0) : '';
            this.syncHiddenInput();
            if (val && index < 5) {
                const next = document.getElementById(`otp-box-${index + 1}`);
                if (next) next.focus();
            }
        },

        onDigitKeydown(e, index) {
            if (e.key === 'Backspace') {
                if (!e.target.value && index > 0) {
                    const prev = document.getElementById(`otp-box-${index - 1}`);
                    if (prev) {
                        prev.focus();
                        prev.value = '';
                        this.syncHiddenInput();
                    }
                } else {
                    e.target.value = '';
                    this.syncHiddenInput();
                    e.preventDefault();
                }
            } else if (e.key === 'ArrowLeft' && index > 0) {
                document.getElementById(`otp-box-${index - 1}`)?.focus();
            } else if (e.key === 'ArrowRight' && index < 5) {
                document.getElementById(`otp-box-${index + 1}`)?.focus();
            } else if (e.key === 'Enter') {
                this.handleSubmit();
            }
        },

        onOtpPaste(e) {
            const raw = (e.clipboardData || window.clipboardData).getData('text');
            const digits = raw.replace(/\D/g, '').slice(0, 6);
            if (!digits) return;
            for (let i = 0; i < 6; i++) {
                const box = document.getElementById(`otp-box-${i}`);
                if (box) box.value = digits[i] || '';
            }
            this.syncHiddenInput();
            const focusIdx = Math.min(digits.length - 1, 5);
            document.getElementById(`otp-box-${focusIdx}`)?.focus();
        },

        syncHiddenInput() {
            const code = Array.from({length: 6}, (_, i) => {
                const box = document.getElementById(`otp-box-${i}`);
                return box ? box.value : '';
            }).join('');
            document.getElementById('otp-hidden-value').value = code;
        },

        handleSubmit() {
            this.syncHiddenInput();
            const code = document.getElementById('otp-hidden-value').value;
            if (code.length < 6 || /\D/.test(code)) {
                // Focus first empty box
                for (let i = 0; i < 6; i++) {
                    const box = document.getElementById(`otp-box-${i}`);
                    if (box && !box.value) { box.focus(); return; }
                }
                return;
            }
            if (this.submitting) return;
            this.submitting = true;
            this.$refs.form.submit();
        }
    };
}
</script>
@endpush
