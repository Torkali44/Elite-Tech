@extends('layouts.dashboard')
@section('title', __('general.cv_builder_page_title'))
@section('content')
@php
    $g = fn ($k, $default = '') => old($k, is_array($data[$k] ?? null) ? implode(', ', $data[$k]) : ($data[$k] ?? $default));
    $skills = \App\Http\Controllers\ProfileController::asSkills($data['skills'] ?? []);
    $languages = \App\Http\Controllers\ProfileController::asSkills($data['languages'] ?? []);
    $certs = \App\Http\Controllers\ProfileController::asSkills($data['certifications'] ?? []);
    $user = auth()->user();
    $initials = collect(explode(' ', $user->name))->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
    $experienceFormItems = old('experience_items', $experienceItems);
    $projectFormItems = old('project_items', $projectItems);
    $educationFormItems = old('education_items', $educationItems);
    $parsedLanguages = array_map(function ($item) {
        if (str_contains($item, ':')) {
            [$name, $level] = array_map('trim', explode(':', $item, 2));
            return ['name' => $name, 'level' => $level];
        }
        return ['name' => $item, 'level' => ''];
    }, $languages);
@endphp

@push('head')
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;800&family=Tajawal:wght@400;500;700;800&family=Almarai:wght@400;700;800&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  .cv-document {
    --cv-sidebar: #1e2732;
    --cv-accent: #8da2b5;
    --cv-ink: #1a1a1a;
    display: flex;
    flex-direction: row;
    align-items: stretch;
    width: 100%;
    min-height: 780px;
    max-width: 820px;
    margin: 0 auto;
    overflow: hidden;
    border-radius: 4px;
    box-shadow: 0 4px 24px rgba(0,0,0,.08);
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    direction: ltr;
    text-align: left;
    background: #fff;
  }
  .cv-sidebar {
    width: 32%;
    flex-shrink: 0;
    background: var(--cv-sidebar);
    color: #fff;
    padding: 28px 20px;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  .cv-photo-wrap { text-align: center; margin-bottom: 16px; }
  .cv-photo {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    border: 3px solid rgba(255,255,255,.85);
    object-fit: cover;
    display: inline-block;
  }
  .cv-photo-placeholder {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    border: 3px solid rgba(255,255,255,.85);
    background: rgba(255,255,255,.12);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 700;
    color: #fff;
  }
  .cv-name {
    font-size: 22pt;
    font-weight: 800;
    text-align: center;
    line-height: 1.3;
    margin: 0 0 4px;
    color: #fff;
    word-break: break-word;
  }
  .cv-job-title {
    font-size: 12pt;
    text-align: center;
    color: var(--cv-accent);
    margin: 0 0 20px;
    font-weight: 500;
  }
  .cv-sidebar-section {
    margin-bottom: 18px;
    padding-bottom: 14px;
    border-bottom: 1px solid rgba(255,255,255,.18);
  }
  .cv-sidebar-section:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
  }
  .cv-sidebar-heading {
    font-size: 11pt;
    font-weight: 800;
    letter-spacing: .1em;
    text-transform: uppercase;
    margin: 0 0 10px;
    padding-bottom: 6px;
    border-bottom: 1px solid rgba(255,255,255,.35);
    color: #fff;
  }
  .cv-contact-item {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    font-size: 10pt;
    line-height: 1.45;
    margin-bottom: 7px;
    color: rgba(255,255,255,.92);
    word-break: break-word;
  }
  .cv-contact-item svg {
    width: 11px;
    height: 11px;
    flex-shrink: 0;
    margin-top: 2px;
    color: var(--cv-accent);
  }
  .cv-sidebar-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px 10px;
    align-items: center;
  }
  .cv-sidebar-tag {
    font-size: 10pt;
    line-height: 1.35;
    color: rgba(255,255,255,.92);
    white-space: normal;
    max-width: 100%;
  }
  .cv-sidebar-tag::before {
    content: '○ ';
    opacity: .75;
    font-size: 9pt;
  }
  .cv-main {
    flex: 1;
    min-width: 0;
    background: #fff;
    padding: 28px 32px 32px;
    color: var(--cv-ink);
  }
  .cv-main-section {
    margin-bottom: 22px;
    padding-bottom: 18px;
    border-bottom: 1px solid #e2e8f0;
  }
  .cv-main-section:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
  }
  .cv-main-heading {
    font-size: 14pt;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
    margin: 0 0 12px;
    padding-bottom: 7px;
    border-bottom: 2px solid var(--cv-sidebar);
    color: var(--cv-ink);
  }
  .cv-profile-text {
    font-size: 11pt;
    line-height: 1.65;
    margin: 0;
    color: #333;
  }
  .cv-entry, .cv-sidebar-section {
    page-break-inside: avoid;
    break-inside: avoid;
  }
  .cv-main-heading, .cv-sidebar-heading {
    page-break-inside: avoid;
    break-inside: avoid;
    page-break-after: avoid;
    break-after: avoid;
  }
  .cv-entry { margin-bottom: 16px; }
  .cv-entry:last-child { margin-bottom: 0; }
  .cv-entry-head {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    gap: 12px;
    margin-bottom: 2px;
    page-break-after: avoid;
    break-after: avoid;
  }
  .cv-entry-title {
    font-size: 13pt;
    font-weight: 800;
    margin: 0;
    color: var(--cv-ink);
  }
  .cv-entry-dates {
    font-size: 11pt;
    color: #555;
    white-space: nowrap;
    flex-shrink: 0;
  }
  .cv-entry-sub {
    font-size: 11pt;
    color: #444;
    margin: 0 0 6px;
  }
  .cv-entry-sub em { font-style: italic; }
  .cv-entry-link {
    font-size: 10pt;
    color: #1a365d;
    text-decoration: underline;
    word-break: break-all;
  }
  .cv-bullets {
    margin: 0;
    padding-left: 16px;
    font-size: 11pt;
    line-height: 1.55;
    color: #333;
  }
  .cv-bullets li { margin-bottom: 3px; }
  .cv-repeat-card {
    border: 1px solid #e2e8f0;
    border-radius: .75rem;
    padding: 1rem;
    background: #fafbfc;
  }
  .cv-main-table { width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0; }
  .cv-main-table th, .cv-main-table td { border: none; padding: 0; margin: 0; vertical-align: top; text-align: left; font-weight: normal; }
  .cv-print-spacer-top, .cv-print-spacer-bottom { height: 0; display: none; }

  .sidebar-bg-fix { display: none; }

  @media print {
    @page { margin: 0; size: A4 portrait; }

    .cv-print-spacer-top, .cv-print-spacer-bottom {
      display: table-cell !important;
      height: 15mm !important;
    }

    html, body {
      margin: 0 !important;
      padding: 0 !important;
      width: 100% !important;
      background: #fff !important;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    .sidebar-bg-fix {
      display: block !important;
      position: fixed !important;
      top: 0 !important;
      left: 0 !important;
      bottom: 0 !important;
      width: 32% !important;
      height: 100vh !important;
      background-color: var(--cv-sidebar, #1e2732) !important;
      z-index: -1 !important;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    aside.no-print,
    header,
    .no-print,
    .gate-backdrop { display: none !important; }

    body > div,
    body > div > div,
    body > div > div > main {
      margin: 0 !important;
      padding: 0 !important;
      max-width: none !important;
      width: 100% !important;
    }

    .cv-page-grid {
      display: block !important;
      gap: 0 !important;
      margin: 0 !important;
      padding: 0 !important;
    }

    #cv-preview {
      position: static !important;
      width: 100% !important;
      max-width: none !important;
      margin: 0 !important;
      padding: 0 !important;
      left: 0 !important;
      top: 0 !important;
    }
    .cv-document {
      position: relative !important;
      z-index: 0 !important;
      display: flex !important;
      flex-direction: row !important;
      align-items: stretch !important;
      width: 100% !important;
      max-width: none !important;
      min-height: 100vh !important;
      height: auto !important;
      margin: 0 !important;
      padding: 0 !important;
      border-radius: 0 !important;
      box-shadow: none !important;
      background: transparent !important;
    }

    .cv-sidebar {
      width: 32% !important;
      min-height: 100vh !important;
      margin: 0 !important;
      padding: 15mm 18pt !important;
      background: transparent !important;
      box-sizing: border-box !important;
    }

    .cv-main {
      flex: 1 !important;
      margin: 0 !important;
      padding: 0 32pt !important;
      box-sizing: border-box !important;
      background: #fff !important;
    }

    .cv-photo, .cv-photo-placeholder {
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    a.cv-entry-link { color: #1a365d !important; }
  }
</style>
@endpush

<div class="mb-6 flex flex-wrap items-end justify-between gap-3 no-print">
    <div>
        <h1 class="text-2xl font-extrabold text-primary mb-1">{{ __('general.cv_builder_page_title') }}</h1>
        <p class="text-sm text-tertiary max-w-2xl leading-relaxed">{{ __('general.cv_builder_subtitle_text') }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('settings') }}" class="btn-ghost text-sm">{{ __('general.cv_appearance_settings') }}</a>
        <button type="button" onclick="window.print()" class="btn-secondary text-sm">{{ __('general.cv_export_pdf') }}</button>
    </div>
</div>

<div class="cv-page-grid grid xl:grid-cols-2 gap-6" x-data="{ themeColor: @js($g('theme_color', '#1e2732')), themeFont: @js($g('theme_font', '\'Segoe UI\', system-ui, -apple-system, sans-serif')) }">
    <form method="POST" action="{{ route('profile.cv') }}" enctype="multipart/form-data"
          class="card p-6 space-y-5 no-print" id="cv-form">
        @csrf
        @if ($errors->any())
            <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 space-y-1">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif

        <section class="space-y-3">
            <h3 class="font-extrabold text-primary border-b border-mist pb-2">تخصيص المظهر (Appearance)</h3>
            <div class="grid sm:grid-cols-2 gap-3 p-4 rounded-lg bg-neutral border border-mist">
                <div>
                    <label class="block text-sm font-bold text-primary mb-1">اللون الرئيسي</label>
                    <div class="flex items-center gap-2">
                        <input type="color" name="theme_color" x-model="themeColor" class="h-10 w-14 cursor-pointer border-0 p-0 rounded bg-transparent">
                        <input type="text" x-model="themeColor" class="input flex-1 !py-1 text-sm font-mono" dir="ltr">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-primary mb-1">نوع الخط</label>
                    <select name="theme_font" x-model="themeFont" class="input !py-1">
                        <option value="'Segoe UI', system-ui, -apple-system, sans-serif">الخط الافتراضي (System)</option>
                        <option value="'Cairo', sans-serif">Cairo (عصري)</option>
                        <option value="'Tajawal', sans-serif">Tajawal (رسمي)</option>
                        <option value="'Almarai', sans-serif">Almarai (احترافي)</option>
                        <option value="'IBM Plex Sans Arabic', sans-serif">IBM Plex (تقني)</option>
                    </select>
                </div>
            </div>
        </section>

        <section class="space-y-3">
            <h3 class="font-extrabold text-primary border-b border-mist pb-2">{{ __('general.cv_basic_info') }}</h3>
            <div class="flex flex-wrap items-center gap-4 p-4 rounded-lg bg-neutral border border-mist">
                <div class="shrink-0">
                    @if($avatarDataUri)
                        <img src="{{ $avatarDataUri }}" alt="" class="w-20 h-20 rounded-full object-cover border-2 border-white shadow-sm" id="cv-avatar-preview">
                    @else
                        <div class="w-20 h-20 rounded-full bg-primary/10 text-primary font-extrabold text-xl grid place-items-center border-2 border-white" id="cv-avatar-preview">
                            {{ $initials ?: '?' }}
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-[200px] space-y-2">
                    <div>
                        <div class="text-sm font-bold text-primary">{{ $user->name }}</div>
                        <div class="text-xs text-tertiary">{{ __('general.cv_name_from_account') }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-primary mb-1">{{ __('general.cv_photo_label') }}</label>
                        <input type="file" name="avatar" accept="image/*" class="input text-sm py-2" onchange="previewCvAvatar(this)">
                    </div>
                </div>
            </div>
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
                    <input name="location" class="input" value="{{ $g('location', $user->location) }}" placeholder="{{ __('general.cv_location_placeholder') }}">
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
                <p class="text-xs text-tertiary mt-1">{{ __('general.cv_comma_separated_hint') }}</p>
            </div>
            <div>
                <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_languages_label') }}</label>
                <input name="languages" class="input" value="{{ $g('languages', implode(', ', $languages)) }}" placeholder="English: Proficient, العربية: متقن">
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

        {{-- Work experience (repeatable) --}}
        <section class="space-y-3" x-data="cvRepeater(@js($experienceFormItems), { title:'', company:'', dates:'', description:'' })">
            <div class="flex items-center justify-between gap-3 border-b border-mist pb-2">
                <h3 class="font-extrabold text-primary">{{ __('general.cv_work_experience') }}</h3>
                <button type="button" @click="add()" class="text-xs font-bold text-secondary hover:underline">+ {{ __('general.cv_add_item') }}</button>
            </div>
            <template x-for="(item, index) in items" :key="index">
                <div class="cv-repeat-card space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-bold text-tertiary" x-text="'#' + (index + 1)"></span>
                        <button type="button" @click="remove(index)" class="text-xs text-rose-600 font-bold hover:underline" x-show="items.length > 1">{{ __('general.cv_remove_item') }}</button>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_exp_title_label') }}</label>
                        <input class="input" :name="'experience_items['+index+'][title]'" x-model="item.title" placeholder="Senior Backend Developer">
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_exp_company_label') }}</label>
                            <input class="input" :name="'experience_items['+index+'][company]'" x-model="item.company" placeholder="Company — Country">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_exp_dates_label') }}</label>
                            <input class="input" :name="'experience_items['+index+'][dates]'" x-model="item.dates" placeholder="Jan 2022 – Dec 2023" dir="ltr">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_exp_desc_label') }}</label>
                        <textarea class="input" rows="3" :name="'experience_items['+index+'][description]'" x-model="item.description" placeholder="{{ __('general.cv_exp_desc_placeholder') }}"></textarea>
                    </div>
                </div>
            </template>
        </section>

        {{-- Projects (repeatable) --}}
        <section class="space-y-3" x-data="cvRepeater(@js($projectFormItems), { title:'', dates:'', description:'', url:'' })">
            <div class="flex items-center justify-between gap-3 border-b border-mist pb-2">
                <h3 class="font-extrabold text-primary">{{ __('general.cv_projects_label') }}</h3>
                <button type="button" @click="add()" class="text-xs font-bold text-secondary hover:underline">+ {{ __('general.cv_add_item') }}</button>
            </div>
            <template x-for="(item, index) in items" :key="index">
                <div class="cv-repeat-card space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-bold text-tertiary" x-text="'#' + (index + 1)"></span>
                        <button type="button" @click="remove(index)" class="text-xs text-rose-600 font-bold hover:underline" x-show="items.length > 1">{{ __('general.cv_remove_item') }}</button>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_project_title_label') }}</label>
                            <input class="input" :name="'project_items['+index+'][title]'" x-model="item.title" placeholder="Project name">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_project_dates_label') }}</label>
                            <input class="input" :name="'project_items['+index+'][dates]'" x-model="item.dates" placeholder="Feb 2026" dir="ltr">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_project_desc_label') }}</label>
                        <textarea class="input" rows="3" :name="'project_items['+index+'][description]'" x-model="item.description" placeholder="{{ __('general.cv_project_desc_placeholder') }}"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_project_url_label') }}</label>
                        <input type="url" class="input" :name="'project_items['+index+'][url]'" x-model="item.url" placeholder="https:// ({{ __('general.cv_optional') }})" dir="ltr">
                    </div>
                </div>
            </template>
        </section>

        {{-- Education (repeatable) --}}
        <section class="space-y-3" x-data="cvRepeater(@js($educationFormItems), { title:'', institution:'', dates:'', description:'' })">
            <div class="flex items-center justify-between gap-3 border-b border-mist pb-2">
                <h3 class="font-extrabold text-primary">{{ __('general.cv_education_label') }}</h3>
                <button type="button" @click="add()" class="text-xs font-bold text-secondary hover:underline">+ {{ __('general.cv_add_item') }}</button>
            </div>
            <template x-for="(item, index) in items" :key="index">
                <div class="cv-repeat-card space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-bold text-tertiary" x-text="'#' + (index + 1)"></span>
                        <button type="button" @click="remove(index)" class="text-xs text-rose-600 font-bold hover:underline" x-show="items.length > 1">{{ __('general.cv_remove_item') }}</button>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_edu_degree_label') }}</label>
                        <input class="input" :name="'education_items['+index+'][title]'" x-model="item.title" placeholder="Bachelor in Computer Science">
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_edu_institution_label') }}</label>
                            <input class="input" :name="'education_items['+index+'][institution]'" x-model="item.institution" placeholder="University — City">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_edu_dates_label') }}</label>
                            <input class="input" :name="'education_items['+index+'][dates]'" x-model="item.dates" placeholder="2020 – 2024" dir="ltr">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-primary mb-1">{{ __('general.cv_edu_desc_label') }}</label>
                        <textarea class="input" rows="2" :name="'education_items['+index+'][description]'" x-model="item.description" placeholder="{{ __('general.cv_optional') }}"></textarea>
                    </div>
                </div>
            </template>
        </section>

        <label class="flex items-start gap-2 p-3 rounded-lg bg-neutral text-sm">
            <input type="checkbox" name="join_forum" value="1" class="mt-1 accent-secondary" @checked($user->wants_jobs_forum)>
            <span>
                <span class="font-bold text-primary">{{ __('general.cv_show_in_forum') }}</span>
                <span class="block text-xs text-tertiary mt-0.5">{{ __('general.cv_show_in_forum_desc') }}</span>
            </span>
        </label>

        <button class="btn-primary w-full">{{ __('general.cv_save_btn') }}</button>
    </form>

    {{-- CV Preview / Print --}}
    <div class="sticky top-20 self-start" id="cv-preview">
        <div class="cv-document" :style="`--cv-sidebar: ${themeColor}; font-family: ${themeFont};`">
            <div class="sidebar-bg-fix no-screen"></div>
            <div class="cv-sidebar">
                <div class="cv-photo-wrap">
                    @if($avatarDataUri)
                        <img src="{{ $avatarDataUri }}" alt="Photo" class="cv-photo">
                    @else
                        <div class="cv-photo-placeholder">{{ $initials ?: '?' }}</div>
                    @endif
                </div>

                <h1 class="cv-name">{{ $user->name }}</h1>
                <p class="cv-job-title">{{ $g('title') ?: __('general.cv_preview_job_title_placeholder') }}</p>

                <div class="cv-sidebar-section">
                    <h2 class="cv-sidebar-heading">{{ __('general.cv_contact_heading') }}</h2>
                    @if($g('phone'))
                        <div class="cv-contact-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span dir="ltr">{{ $g('phone') }}</span>
                        </div>
                    @endif
                    <div class="cv-contact-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span dir="ltr">{{ $user->email }}</span>
                    </div>
                    @if($g('location'))
                        <div class="cv-contact-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>{{ $g('location') }}</span>
                        </div>
                    @endif
                    @if($g('linkedin'))
                        <div class="cv-contact-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            <span dir="ltr">{{ parse_url($g('linkedin'), PHP_URL_HOST) ?: $g('linkedin') }}</span>
                        </div>
                    @endif
                    @if($g('github'))
                        <div class="cv-contact-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            <span dir="ltr">{{ parse_url($g('github'), PHP_URL_HOST) ?: $g('github') }}</span>
                        </div>
                    @endif
                    @if($g('portfolio_url'))
                        <div class="cv-contact-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
                            <span dir="ltr">{{ parse_url($g('portfolio_url'), PHP_URL_HOST) ?: $g('portfolio_url') }}</span>
                        </div>
                    @endif
                </div>

                @if(count($skills))
                    <div class="cv-sidebar-section">
                        <h2 class="cv-sidebar-heading">{{ __('general.cv_skills_heading') }}</h2>
                        <div class="cv-sidebar-tags">
                            @foreach($skills as $s)<span class="cv-sidebar-tag">{{ $s }}</span>@endforeach
                        </div>
                    </div>
                @endif

                @if(count($parsedLanguages))
                    <div class="cv-sidebar-section">
                        <h2 class="cv-sidebar-heading">{{ __('general.cv_languages_heading') }}</h2>
                        <div class="cv-sidebar-tags">
                            @foreach($parsedLanguages as $lang)
                                <span class="cv-sidebar-tag">{{ $lang['name'] }}@if($lang['level']): {{ $lang['level'] }}@endif</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(count($certs))
                    <div class="cv-sidebar-section">
                        <h2 class="cv-sidebar-heading">{{ __('general.cv_certifications_heading') }}</h2>
                        <div class="cv-sidebar-tags">
                            @foreach($certs as $s)<span class="cv-sidebar-tag">{{ $s }}</span>@endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="cv-main">
                <table class="cv-main-table">
                    <thead><tr><th class="cv-print-spacer-top"></th></tr></thead>
                    <tbody><tr><td>
                        @if($g('summary'))
                            <section class="cv-main-section">
                                <h2 class="cv-main-heading">{{ __('general.cv_profile_heading') }}</h2>
                                <p class="cv-profile-text">{{ $g('summary') }}</p>
                            </section>
                        @endif

                @php $previewExperience = old('experience_items', $experienceItems); @endphp
                @if(collect($previewExperience)->contains(fn ($i) => ($i['title'] ?? '') !== '' || ($i['description'] ?? '') !== ''))
                    <section class="cv-main-section">
                        <h2 class="cv-main-heading">{{ __('general.cv_experience_heading') }}</h2>
                        @foreach($previewExperience as $entry)
                            @if(($entry['title'] ?? '') !== '' || ($entry['description'] ?? '') !== '')
                                @php $bullets = \App\Http\Controllers\ProfileController::descriptionBullets($entry['description'] ?? ''); @endphp
                                <div class="cv-entry">
                                    <div class="cv-entry-head">
                                        <h3 class="cv-entry-title">{{ $entry['title'] ?? '' }}</h3>
                                        @if($entry['dates'] ?? '')<span class="cv-entry-dates">{{ $entry['dates'] }}</span>@endif
                                    </div>
                                    @if($entry['company'] ?? '')<p class="cv-entry-sub">{{ $entry['company'] }}</p>@endif
                                    @if(count($bullets))
                                        <ul class="cv-bullets">@foreach($bullets as $b)<li>{{ $b }}</li>@endforeach</ul>
                                    @elseif($entry['description'] ?? '')
                                        <p class="cv-profile-text">{{ $entry['description'] }}</p>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </section>
                @endif

                @php $previewProjects = old('project_items', $projectItems); @endphp
                @if(collect($previewProjects)->contains(fn ($i) => ($i['title'] ?? '') !== '' || ($i['description'] ?? '') !== ''))
                    <section class="cv-main-section">
                        <h2 class="cv-main-heading">{{ __('general.cv_projects_heading') }}</h2>
                        @foreach($previewProjects as $entry)
                            @if(($entry['title'] ?? '') !== '' || ($entry['description'] ?? '') !== '')
                                @php $bullets = \App\Http\Controllers\ProfileController::descriptionBullets($entry['description'] ?? ''); @endphp
                                <div class="cv-entry">
                                    <div class="cv-entry-head">
                                        <h3 class="cv-entry-title">{{ $entry['title'] ?? '' }}</h3>
                                        @if($entry['dates'] ?? '')<span class="cv-entry-dates">{{ $entry['dates'] }}</span>@endif
                                    </div>
                                    @if($entry['url'] ?? '')
                                        <p class="cv-entry-sub"><a href="{{ $entry['url'] }}" class="cv-entry-link" dir="ltr">{{ parse_url($entry['url'], PHP_URL_HOST) ?: $entry['url'] }}</a></p>
                                    @endif
                                    @if(count($bullets))
                                        <ul class="cv-bullets">@foreach($bullets as $b)<li>{{ $b }}</li>@endforeach</ul>
                                    @elseif($entry['description'] ?? '')
                                        <p class="cv-profile-text">{{ $entry['description'] }}</p>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </section>
                @endif

                @php $previewEducation = old('education_items', $educationItems); @endphp
                @if(collect($previewEducation)->contains(fn ($i) => ($i['title'] ?? '') !== '' || ($i['institution'] ?? '') !== ''))
                    <section class="cv-main-section">
                        <h2 class="cv-main-heading">{{ __('general.cv_education_heading') }}</h2>
                        @foreach($previewEducation as $entry)
                            @if(($entry['title'] ?? '') !== '' || ($entry['institution'] ?? '') !== '')
                                @php $bullets = \App\Http\Controllers\ProfileController::descriptionBullets($entry['description'] ?? ''); @endphp
                                <div class="cv-entry">
                                    <div class="cv-entry-head">
                                        <h3 class="cv-entry-title">{{ $entry['title'] ?? '' }}</h3>
                                        @if($entry['dates'] ?? '')<span class="cv-entry-dates">{{ $entry['dates'] }}</span>@endif
                                    </div>
                                    @if($entry['institution'] ?? '')<p class="cv-entry-sub"><em>{{ $entry['institution'] }}</em></p>@endif
                                    @if(count($bullets))
                                        <ul class="cv-bullets">@foreach($bullets as $b)<li>{{ $b }}</li>@endforeach</ul>
                                    @elseif($entry['description'] ?? '')
                                        <p class="cv-profile-text">{{ $entry['description'] }}</p>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </section>
                    </td></tr></tbody>
                    <tfoot><tr><td class="cv-print-spacer-bottom"></td></tr></tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('cvRepeater', (initial, blank) => ({
        items: (initial && initial.length) ? initial : [{ ...blank }],
        add() { this.items.push({ ...blank }); },
        remove(i) { if (this.items.length > 1) this.items.splice(i, 1); },
    }));
});

function previewCvAvatar(input) {
    const file = input.files?.[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const el = document.getElementById('cv-avatar-preview');
        if (!el) return;
        if (el.tagName === 'IMG') {
            el.src = e.target.result;
        } else {
            const img = document.createElement('img');
            img.id = 'cv-avatar-preview';
            img.src = e.target.result;
            img.className = 'w-20 h-20 rounded-full object-cover border-2 border-white shadow-sm';
            el.replaceWith(img);
        }
        const cvPhoto = document.querySelector('#cv-preview .cv-photo');
        const cvPlaceholder = document.querySelector('#cv-preview .cv-photo-placeholder');
        if (cvPhoto) {
            cvPhoto.src = e.target.result;
        } else if (cvPlaceholder) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.alt = '';
            img.className = 'cv-photo';
            cvPlaceholder.replaceWith(img);
        }
    };
    reader.readAsDataURL(file);
}
</script>
@endpush
@endsection
