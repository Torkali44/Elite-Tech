@extends('layouts.dashboard')
@section('title', __('general.cv_builder_page_title'))
@section('content')
@php
    $g = fn ($k, $default = '') => old($k, is_array($data[$k] ?? null) ? implode(', ', $data[$k]) : ($data[$k] ?? $default));
    $skills = \App\Http\Controllers\ProfileController::asSkills($data['skills'] ?? []);
    $languages = \App\Http\Controllers\ProfileController::asSkills($data['languages'] ?? []);
    $certs = \App\Http\Controllers\ProfileController::asSkills($data['certifications'] ?? []);
@endphp

@push('head')
<style>
  @media print {
    @page { margin: 1.2cm; size: A4; }
    body * { visibility: hidden !important; }
    #cv-preview, #cv-preview * { visibility: visible !important; }
    #cv-preview {
      position: absolute !important;
      inset: 0 !important;
      width: 100% !important;
      max-width: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
      border: none !important;
      box-shadow: none !important;
      background: #fff !important;
    }
  }
</style>
@endpush

<div class="mb-6 flex flex-wrap items-end justify-between gap-3 no-print">
    <div>
        <h1 class="text-2xl font-extrabold text-primary mb-1">{{ __('general.cv_builder_page_title') }}</h1>
        <p class="text-sm text-tertiary max-w-2xl leading-relaxed">
            {{ __('general.cv_builder_subtitle_text') }}
        </p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('settings') }}" class="btn-ghost text-sm">{{ __('general.cv_appearance_settings') }}</a>
        <button type="button" onclick="window.print()" class="btn-secondary text-sm">{{ __('general.cv_export_pdf') }}</button>
    </div>
</div>

<div class="grid xl:grid-cols-2 gap-6">
    <form method="POST" action="{{ route('profile.cv') }}" class="card p-6 space-y-5 no-print">
        @csrf
        @if ($errors->any())
            <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 space-y-1">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif

        <section class="space-y-3">
            <h3 class="font-extrabold text-primary border-b border-mist pb-2">{{ __('general.cv_basic_info') }}</h3>
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_job_title_label') }}</label>
                    <input name="title" class="input" value="{{ $g('title') }}" placeholder="Full-stack Developer">
                </div>
                <div>
                    <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_years_exp') }}</label>
                    <input name="years_experience" class="input" value="{{ $g('years_experience') }}" placeholder="{{ __('general.cv_years_exp_placeholder') }}">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_summary_label') }}</label>
                <textarea name="summary" rows="3" class="input" placeholder="{{ __('general.cv_summary_placeholder') }}">{{ $g('summary') }}</textarea>
            </div>
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_location_label') }}</label>
                    <input name="location" class="input" value="{{ $g('location', auth()->user()->location) }}" placeholder="{{ __('general.cv_location_placeholder') }}">
                </div>
                <div>
                    <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_phone_label') }}</label>
                    <input name="phone" class="input" value="{{ $g('phone') }}" placeholder="+20..." dir="ltr">
                </div>
            </div>
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_availability_label') }}</label>
                    <select name="availability" class="input">
                        @php
                            $availOpts = [
                                '' => __('general.cv_choose_availability'),
                                'متاح فوراً' => __('general.cv_avail_immediate'),
                                'خلال أسبوعين' => __('general.cv_avail_two_weeks'),
                                'دوام جزئي' => __('general.cv_avail_part_time'),
                                'عن بُعد فقط' => __('general.cv_avail_remote'),
                                'غير متاح حالياً' => __('general.cv_avail_not_available'),
                            ];
                        @endphp
                        @foreach($availOpts as $val => $lbl)
                            <option value="{{ $val }}" @selected($g('availability')===$val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_salary_label') }}</label>
                    <input name="expected_salary" class="input" value="{{ $g('expected_salary') }}" placeholder="{{ __('general.cv_salary_optional') }}">
                </div>
            </div>
        </section>

        <section class="space-y-3">
            <h3 class="font-extrabold text-primary border-b border-mist pb-2">{{ __('general.cv_skills_section') }}</h3>
            <div>
                <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_tech_skills') }}</label>
                <input name="skills" class="input" value="{{ $g('skills', implode(', ', $skills)) }}" placeholder="Laravel, React, SQL">
            </div>
            <div>
                <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_languages_label') }}</label>
                <input name="languages" class="input" value="{{ $g('languages', implode(', ', $languages)) }}" placeholder="العربية, الإنجليزية">
            </div>
            <div>
                <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_certifications_label') }}</label>
                <input name="certifications" class="input" value="{{ $g('certifications', implode(', ', $certs)) }}" placeholder="AWS, PMP...">
            </div>
        </section>

        <section class="space-y-3">
            <h3 class="font-extrabold text-primary border-b border-mist pb-2">{{ __('general.cv_links_section') }}</h3>
            <div>
                <label class="block text-sm font-bold text-primary mb-1">Portfolio</label>
                <input type="url" name="portfolio_url" class="input" value="{{ $g('portfolio_url') }}" placeholder="https://" dir="ltr">
            </div>
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-bold text-primary mb-1">LinkedIn</label>
                    <input type="url" name="linkedin" class="input" value="{{ $g('linkedin') }}" placeholder="https://linkedin.com/in/..." dir="ltr">
                </div>
                <div>
                    <label class="block text-sm font-bold text-primary mb-1">GitHub</label>
                    <input type="url" name="github" class="input" value="{{ $g('github') }}" placeholder="https://github.com/..." dir="ltr">
                </div>
            </div>
        </section>

        <section class="space-y-3">
            <h3 class="font-extrabold text-primary border-b border-mist pb-2">{{ __('general.cv_experience_section') }}</h3>
            <div>
                <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_work_experience') }}</label>
                <textarea name="experience" rows="4" class="input" placeholder="{{ __('general.cv_work_placeholder') }}">{{ $g('experience') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_education_label') }}</label>
                <textarea name="education" rows="2" class="input" placeholder="{{ __('general.cv_education_placeholder') }}">{{ $g('education') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_projects_label') }}</label>
                <textarea name="projects" rows="3" class="input" placeholder="{{ __('general.cv_projects_placeholder') }}">{{ $g('projects') }}</textarea>
            </div>
        </section>

        <label class="flex items-start gap-2 p-3 rounded-lg bg-neutral text-sm">
            <input type="checkbox" name="join_forum" value="1" class="mt-1 accent-secondary" @checked(auth()->user()->wants_jobs_forum)>
            <span>
                <span class="font-bold text-primary">{{ __('general.cv_show_in_forum') }}</span>
                <span class="block text-xs text-tertiary mt-0.5">{{ __('general.cv_show_in_forum_desc') }}</span>
            </span>
        </label>

        <button class="btn-primary w-full">{{ __('general.cv_save_btn') }}</button>
    </form>

    {{-- Printable CV preview only --}}
    <div class="card p-8 bg-white sticky top-20 self-start print:static print:shadow-none print:border-0" id="cv-preview">
        <div class="border-b-2 border-primary pb-5 mb-6">
            <div class="text-2xl font-extrabold text-primary">{{ auth()->user()->name }}</div>
            <div class="text-secondary font-bold text-lg mt-0.5">{{ $g('title') ?: __('general.cv_preview_job_title_placeholder') }}</div>
            <div class="text-xs text-tertiary mt-3 space-y-1">
                <div>{{ auth()->user()->email }}@if($g('phone')) · {{ $g('phone') }}@endif</div>
                @if($g('location'))<div>{{ $g('location') }}</div>@endif
                @if($g('years_experience') || $g('availability'))
                    <div>{{ $g('years_experience') }}@if($g('years_experience') && $g('availability')) · @endif{{ $g('availability') }}</div>
                @endif
                @if($g('expected_salary'))<div>{{ __('general.expected_salary_label') }}: {{ $g('expected_salary') }}</div>@endif
            </div>
            <div class="flex flex-wrap gap-3 mt-3 text-xs">
                @if($g('portfolio_url'))<a href="{{ $g('portfolio_url') }}" class="text-primary underline" target="_blank">Portfolio</a>@endif
                @if($g('linkedin'))<a href="{{ $g('linkedin') }}" class="text-primary underline" target="_blank">LinkedIn</a>@endif
                @if($g('github'))<a href="{{ $g('github') }}" class="text-primary underline" target="_blank">GitHub</a>@endif
            </div>
        </div>

        @if($g('summary'))
            <h4 class="font-extrabold text-primary text-sm mb-1">{{ __('general.cv_summary_label') }}</h4>
            <p class="text-sm text-tertiary mb-5 whitespace-pre-line leading-relaxed">{{ $g('summary') }}</p>
        @endif

        @if(count($skills))
            <h4 class="font-extrabold text-primary text-sm mb-2">{{ __('general.cv_skills_heading') }}</h4>
            <div class="flex flex-wrap gap-1.5 mb-5">
                @foreach($skills as $s)<span class="badge bg-mist text-primary">{{ $s }}</span>@endforeach
            </div>
        @endif

        @if(count($languages))
            <h4 class="font-extrabold text-primary text-sm mb-2">{{ __('general.cv_languages_heading') }}</h4>
            <div class="flex flex-wrap gap-1.5 mb-5">
                @foreach($languages as $s)<span class="badge bg-primary/10 text-primary">{{ $s }}</span>@endforeach
            </div>
        @endif

        @if($g('experience'))
            <h4 class="font-extrabold text-primary text-sm mb-1">{{ __('general.cv_experience_heading') }}</h4>
            <p class="text-sm text-tertiary mb-5 whitespace-pre-line leading-relaxed">{{ $g('experience') }}</p>
        @endif

        @if($g('projects'))
            <h4 class="font-extrabold text-primary text-sm mb-1">{{ __('general.cv_projects_heading') }}</h4>
            <p class="text-sm text-tertiary mb-5 whitespace-pre-line leading-relaxed">{{ $g('projects') }}</p>
        @endif

        @if($g('education'))
            <h4 class="font-extrabold text-primary text-sm mb-1">{{ __('general.cv_education_heading') }}</h4>
            <p class="text-sm text-tertiary mb-5 whitespace-pre-line leading-relaxed">{{ $g('education') }}</p>
        @endif

        @if(count($certs))
            <h4 class="font-extrabold text-primary text-sm mb-2">{{ __('general.cv_certifications_heading') }}</h4>
            <div class="flex flex-wrap gap-1.5">
                @foreach($certs as $s)<span class="badge bg-secondary/10 text-secondary">{{ $s }}</span>@endforeach
            </div>
        @endif
    </div>
</div>
@endsection
