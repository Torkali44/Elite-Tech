<?php $__env->startSection('title', __('general.cv_builder_page_title')); ?>
<?php $__env->startSection('content'); ?>
<?php
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
?>

<?php $__env->startPush('head'); ?>
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
    font-size: 1.25rem;
    font-weight: 800;
    text-align: center;
    line-height: 1.3;
    margin: 0 0 4px;
    color: #fff;
    word-break: break-word;
  }
  .cv-job-title {
    font-size: .78rem;
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
    font-size: .7rem;
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
    font-size: .68rem;
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
    font-size: .66rem;
    line-height: 1.35;
    color: rgba(255,255,255,.92);
    white-space: normal;
    max-width: 100%;
  }
  .cv-sidebar-tag::before {
    content: '○ ';
    opacity: .75;
    font-size: .55rem;
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
    font-size: .88rem;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
    margin: 0 0 12px;
    padding-bottom: 7px;
    border-bottom: 2px solid var(--cv-sidebar);
    color: var(--cv-ink);
  }
  .cv-profile-text {
    font-size: .76rem;
    line-height: 1.65;
    margin: 0;
    color: #333;
  }
  .cv-entry { margin-bottom: 16px; }
  .cv-entry:last-child { margin-bottom: 0; }
  .cv-entry-head {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    gap: 12px;
    margin-bottom: 2px;
  }
  .cv-entry-title {
    font-size: .82rem;
    font-weight: 800;
    margin: 0;
    color: var(--cv-ink);
  }
  .cv-entry-dates {
    font-size: .7rem;
    color: #555;
    white-space: nowrap;
    flex-shrink: 0;
  }
  .cv-entry-sub {
    font-size: .72rem;
    color: #444;
    margin: 0 0 6px;
  }
  .cv-entry-sub em { font-style: italic; }
  .cv-entry-link {
    font-size: .68rem;
    color: #1a365d;
    text-decoration: underline;
    word-break: break-all;
  }
  .cv-bullets {
    margin: 0;
    padding-left: 16px;
    font-size: .72rem;
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

  @media print {
    @page { margin: 0; size: A4 portrait; }

    html, body {
      margin: 0 !important;
      padding: 0 !important;
      width: 100% !important;
      background: #fff !important;
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
      font-family: Calibri, 'Segoe UI', Arial, sans-serif !important;
      font-size: 10pt !important;
      line-height: 1.45 !important;
    }

    .cv-sidebar {
      width: 32% !important;
      min-height: 100vh !important;
      margin: 0 !important;
      padding: 28pt 18pt !important;
      background: #1e2732 !important;
      box-sizing: border-box !important;
    }

    .cv-main {
      flex: 1 !important;
      margin: 0 !important;
      padding: 28pt 32pt !important;
      box-sizing: border-box !important;
    }

    .cv-name { font-size: 14pt !important; font-weight: 700 !important; }
    .cv-job-title { font-size: 9.5pt !important; }
    .cv-sidebar-heading { font-size: 8.5pt !important; letter-spacing: 0.08em !important; }
    .cv-contact-item { font-size: 8pt !important; line-height: 1.4 !important; }
    .cv-sidebar-tag { font-size: 8pt !important; line-height: 1.35 !important; }
    .cv-main-heading { font-size: 11pt !important; letter-spacing: 0.06em !important; }
    .cv-profile-text { font-size: 9.5pt !important; line-height: 1.55 !important; }
    .cv-entry-title { font-size: 10pt !important; font-weight: 700 !important; }
    .cv-entry-dates { font-size: 8.5pt !important; }
    .cv-entry-sub { font-size: 9pt !important; }
    .cv-bullets { font-size: 9pt !important; line-height: 1.5 !important; }
    .cv-entry-link { font-size: 8.5pt !important; }

    .cv-photo, .cv-photo-placeholder {
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    a.cv-entry-link { color: #1a365d !important; }
  }
</style>
<?php $__env->stopPush(); ?>

<div class="mb-6 flex flex-wrap items-end justify-between gap-3 no-print">
    <div>
        <h1 class="text-2xl font-extrabold text-primary mb-1"><?php echo e(__('general.cv_builder_page_title')); ?></h1>
        <p class="text-sm text-tertiary max-w-2xl leading-relaxed"><?php echo e(__('general.cv_builder_subtitle_text')); ?></p>
    </div>
    <div class="flex gap-2">
        <a href="<?php echo e(route('settings')); ?>" class="btn-ghost text-sm"><?php echo e(__('general.cv_appearance_settings')); ?></a>
        <button type="button" onclick="window.print()" class="btn-secondary text-sm"><?php echo e(__('general.cv_export_pdf')); ?></button>
    </div>
</div>

<div class="cv-page-grid grid xl:grid-cols-2 gap-6">
    <form method="POST" action="<?php echo e(route('profile.cv')); ?>" enctype="multipart/form-data"
          class="card p-6 space-y-5 no-print" id="cv-form">
        <?php echo csrf_field(); ?>
        <?php if($errors->any()): ?>
            <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div><?php echo e($e); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <section class="space-y-3">
            <h3 class="font-extrabold text-primary border-b border-mist pb-2"><?php echo e(__('general.cv_basic_info')); ?></h3>
            <div class="flex flex-wrap items-center gap-4 p-4 rounded-lg bg-neutral border border-mist">
                <div class="shrink-0">
                    <?php if($avatarDataUri): ?>
                        <img src="<?php echo e($avatarDataUri); ?>" alt="" class="w-20 h-20 rounded-full object-cover border-2 border-white shadow-sm" id="cv-avatar-preview">
                    <?php else: ?>
                        <div class="w-20 h-20 rounded-full bg-primary/10 text-primary font-extrabold text-xl grid place-items-center border-2 border-white" id="cv-avatar-preview">
                            <?php echo e($initials ?: '?'); ?>

                        </div>
                    <?php endif; ?>
                </div>
                <div class="flex-1 min-w-[200px] space-y-2">
                    <div>
                        <div class="text-sm font-bold text-primary"><?php echo e($user->name); ?></div>
                        <div class="text-xs text-tertiary"><?php echo e(__('general.cv_name_from_account')); ?></div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-primary mb-1"><?php echo e(__('general.cv_photo_label')); ?></label>
                        <input type="file" name="avatar" accept="image/*" class="input text-sm py-2" onchange="previewCvAvatar(this)">
                    </div>
                </div>
            </div>
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-bold text-primary mb-1"><?php echo e(__('general.cv_job_title_label')); ?></label>
                    <input name="title" class="input" value="<?php echo e($g('title')); ?>" placeholder="Full-stack Developer">
                </div>
                <div>
                    <label class="block text-sm font-bold text-primary mb-1"><?php echo e(__('general.cv_years_exp')); ?></label>
                    <input name="years_experience" class="input" value="<?php echo e($g('years_experience')); ?>" placeholder="<?php echo e(__('general.cv_years_exp_placeholder')); ?>">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-primary mb-1"><?php echo e(__('general.cv_summary_label')); ?></label>
                <textarea name="summary" rows="3" class="input" placeholder="<?php echo e(__('general.cv_summary_placeholder')); ?>"><?php echo e($g('summary')); ?></textarea>
            </div>
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-bold text-primary mb-1"><?php echo e(__('general.cv_location_label')); ?></label>
                    <input name="location" class="input" value="<?php echo e($g('location', $user->location)); ?>" placeholder="<?php echo e(__('general.cv_location_placeholder')); ?>">
                </div>
                <div>
                    <label class="block text-sm font-bold text-primary mb-1"><?php echo e(__('general.cv_phone_label')); ?></label>
                    <input name="phone" class="input" value="<?php echo e($g('phone')); ?>" placeholder="+20..." dir="ltr">
                </div>
            </div>
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-bold text-primary mb-1"><?php echo e(__('general.cv_availability_label')); ?></label>
                    <select name="availability" class="input">
                        <?php
                            $availOpts = [
                                '' => __('general.cv_choose_availability'),
                                'متاح فوراً' => __('general.cv_avail_immediate'),
                                'خلال أسبوعين' => __('general.cv_avail_two_weeks'),
                                'دوام جزئي' => __('general.cv_avail_part_time'),
                                'عن بُعد فقط' => __('general.cv_avail_remote'),
                                'غير متاح حالياً' => __('general.cv_avail_not_available'),
                            ];
                        ?>
                        <?php $__currentLoopData = $availOpts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($val); ?>" <?php if($g('availability')===$val): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-primary mb-1"><?php echo e(__('general.cv_salary_label')); ?></label>
                    <input name="expected_salary" class="input" value="<?php echo e($g('expected_salary')); ?>" placeholder="<?php echo e(__('general.cv_salary_optional')); ?>">
                </div>
            </div>
        </section>

        <section class="space-y-3">
            <h3 class="font-extrabold text-primary border-b border-mist pb-2"><?php echo e(__('general.cv_skills_section')); ?></h3>
            <div>
                <label class="block text-sm font-bold text-primary mb-1"><?php echo e(__('general.cv_tech_skills')); ?></label>
                <input name="skills" class="input" value="<?php echo e($g('skills', implode(', ', $skills))); ?>" placeholder="Laravel, React, SQL">
                <p class="text-xs text-tertiary mt-1"><?php echo e(__('general.cv_comma_separated_hint')); ?></p>
            </div>
            <div>
                <label class="block text-sm font-bold text-primary mb-1"><?php echo e(__('general.cv_languages_label')); ?></label>
                <input name="languages" class="input" value="<?php echo e($g('languages', implode(', ', $languages))); ?>" placeholder="English: Proficient, العربية: متقن">
            </div>
            <div>
                <label class="block text-sm font-bold text-primary mb-1"><?php echo e(__('general.cv_certifications_label')); ?></label>
                <input name="certifications" class="input" value="<?php echo e($g('certifications', implode(', ', $certs))); ?>" placeholder="AWS, PMP...">
            </div>
        </section>

        <section class="space-y-3">
            <h3 class="font-extrabold text-primary border-b border-mist pb-2"><?php echo e(__('general.cv_links_section')); ?></h3>
            <div>
                <label class="block text-sm font-bold text-primary mb-1">Portfolio</label>
                <input type="url" name="portfolio_url" class="input" value="<?php echo e($g('portfolio_url')); ?>" placeholder="https://" dir="ltr">
            </div>
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-bold text-primary mb-1">LinkedIn</label>
                    <input type="url" name="linkedin" class="input" value="<?php echo e($g('linkedin')); ?>" placeholder="https://linkedin.com/in/..." dir="ltr">
                </div>
                <div>
                    <label class="block text-sm font-bold text-primary mb-1">GitHub</label>
                    <input type="url" name="github" class="input" value="<?php echo e($g('github')); ?>" placeholder="https://github.com/..." dir="ltr">
                </div>
            </div>
        </section>

        
        <section class="space-y-3" x-data="cvRepeater(<?php echo \Illuminate\Support\Js::from($experienceFormItems)->toHtml() ?>, { title:'', company:'', dates:'', description:'' })">
            <div class="flex items-center justify-between gap-3 border-b border-mist pb-2">
                <h3 class="font-extrabold text-primary"><?php echo e(__('general.cv_work_experience')); ?></h3>
                <button type="button" @click="add()" class="text-xs font-bold text-secondary hover:underline">+ <?php echo e(__('general.cv_add_item')); ?></button>
            </div>
            <template x-for="(item, index) in items" :key="index">
                <div class="cv-repeat-card space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-bold text-tertiary" x-text="'#' + (index + 1)"></span>
                        <button type="button" @click="remove(index)" class="text-xs text-rose-600 font-bold hover:underline" x-show="items.length > 1"><?php echo e(__('general.cv_remove_item')); ?></button>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-primary mb-1"><?php echo e(__('general.cv_exp_title_label')); ?></label>
                        <input class="input" :name="'experience_items['+index+'][title]'" x-model="item.title" placeholder="Senior Backend Developer">
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-bold text-primary mb-1"><?php echo e(__('general.cv_exp_company_label')); ?></label>
                            <input class="input" :name="'experience_items['+index+'][company]'" x-model="item.company" placeholder="Company — Country">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-primary mb-1"><?php echo e(__('general.cv_exp_dates_label')); ?></label>
                            <input class="input" :name="'experience_items['+index+'][dates]'" x-model="item.dates" placeholder="Jan 2022 – Dec 2023" dir="ltr">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-primary mb-1"><?php echo e(__('general.cv_exp_desc_label')); ?></label>
                        <textarea class="input" rows="3" :name="'experience_items['+index+'][description]'" x-model="item.description" placeholder="<?php echo e(__('general.cv_exp_desc_placeholder')); ?>"></textarea>
                    </div>
                </div>
            </template>
        </section>

        
        <section class="space-y-3" x-data="cvRepeater(<?php echo \Illuminate\Support\Js::from($projectFormItems)->toHtml() ?>, { title:'', dates:'', description:'', url:'' })">
            <div class="flex items-center justify-between gap-3 border-b border-mist pb-2">
                <h3 class="font-extrabold text-primary"><?php echo e(__('general.cv_projects_label')); ?></h3>
                <button type="button" @click="add()" class="text-xs font-bold text-secondary hover:underline">+ <?php echo e(__('general.cv_add_item')); ?></button>
            </div>
            <template x-for="(item, index) in items" :key="index">
                <div class="cv-repeat-card space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-bold text-tertiary" x-text="'#' + (index + 1)"></span>
                        <button type="button" @click="remove(index)" class="text-xs text-rose-600 font-bold hover:underline" x-show="items.length > 1"><?php echo e(__('general.cv_remove_item')); ?></button>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-bold text-primary mb-1"><?php echo e(__('general.cv_project_title_label')); ?></label>
                            <input class="input" :name="'project_items['+index+'][title]'" x-model="item.title" placeholder="Project name">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-primary mb-1"><?php echo e(__('general.cv_project_dates_label')); ?></label>
                            <input class="input" :name="'project_items['+index+'][dates]'" x-model="item.dates" placeholder="Feb 2026" dir="ltr">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-primary mb-1"><?php echo e(__('general.cv_project_desc_label')); ?></label>
                        <textarea class="input" rows="3" :name="'project_items['+index+'][description]'" x-model="item.description" placeholder="<?php echo e(__('general.cv_project_desc_placeholder')); ?>"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-primary mb-1"><?php echo e(__('general.cv_project_url_label')); ?></label>
                        <input type="url" class="input" :name="'project_items['+index+'][url]'" x-model="item.url" placeholder="https:// (<?php echo e(__('general.cv_optional')); ?>)" dir="ltr">
                    </div>
                </div>
            </template>
        </section>

        
        <section class="space-y-3" x-data="cvRepeater(<?php echo \Illuminate\Support\Js::from($educationFormItems)->toHtml() ?>, { title:'', institution:'', dates:'', description:'' })">
            <div class="flex items-center justify-between gap-3 border-b border-mist pb-2">
                <h3 class="font-extrabold text-primary"><?php echo e(__('general.cv_education_label')); ?></h3>
                <button type="button" @click="add()" class="text-xs font-bold text-secondary hover:underline">+ <?php echo e(__('general.cv_add_item')); ?></button>
            </div>
            <template x-for="(item, index) in items" :key="index">
                <div class="cv-repeat-card space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-bold text-tertiary" x-text="'#' + (index + 1)"></span>
                        <button type="button" @click="remove(index)" class="text-xs text-rose-600 font-bold hover:underline" x-show="items.length > 1"><?php echo e(__('general.cv_remove_item')); ?></button>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-primary mb-1"><?php echo e(__('general.cv_edu_degree_label')); ?></label>
                        <input class="input" :name="'education_items['+index+'][title]'" x-model="item.title" placeholder="Bachelor in Computer Science">
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-bold text-primary mb-1"><?php echo e(__('general.cv_edu_institution_label')); ?></label>
                            <input class="input" :name="'education_items['+index+'][institution]'" x-model="item.institution" placeholder="University — City">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-primary mb-1"><?php echo e(__('general.cv_edu_dates_label')); ?></label>
                            <input class="input" :name="'education_items['+index+'][dates]'" x-model="item.dates" placeholder="2020 – 2024" dir="ltr">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-primary mb-1"><?php echo e(__('general.cv_edu_desc_label')); ?></label>
                        <textarea class="input" rows="2" :name="'education_items['+index+'][description]'" x-model="item.description" placeholder="<?php echo e(__('general.cv_optional')); ?>"></textarea>
                    </div>
                </div>
            </template>
        </section>

        <label class="flex items-start gap-2 p-3 rounded-lg bg-neutral text-sm">
            <input type="checkbox" name="join_forum" value="1" class="mt-1 accent-secondary" <?php if($user->wants_jobs_forum): echo 'checked'; endif; ?>>
            <span>
                <span class="font-bold text-primary"><?php echo e(__('general.cv_show_in_forum')); ?></span>
                <span class="block text-xs text-tertiary mt-0.5"><?php echo e(__('general.cv_show_in_forum_desc')); ?></span>
            </span>
        </label>

        <button class="btn-primary w-full"><?php echo e(__('general.cv_save_btn')); ?></button>
    </form>

    
    <div class="sticky top-20 self-start" id="cv-preview">
        <div class="cv-document">
            <div class="cv-sidebar">
                <div class="cv-photo-wrap">
                    <?php if($avatarDataUri): ?>
                        <img src="<?php echo e($avatarDataUri); ?>" alt="" class="cv-photo">
                    <?php else: ?>
                        <div class="cv-photo-placeholder"><?php echo e($initials ?: '?'); ?></div>
                    <?php endif; ?>
                </div>

                <h1 class="cv-name"><?php echo e($user->name); ?></h1>
                <p class="cv-job-title"><?php echo e($g('title') ?: __('general.cv_preview_job_title_placeholder')); ?></p>

                <div class="cv-sidebar-section">
                    <h2 class="cv-sidebar-heading"><?php echo e(__('general.cv_contact_heading')); ?></h2>
                    <?php if($g('phone')): ?>
                        <div class="cv-contact-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span dir="ltr"><?php echo e($g('phone')); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="cv-contact-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span dir="ltr"><?php echo e($user->email); ?></span>
                    </div>
                    <?php if($g('location')): ?>
                        <div class="cv-contact-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span><?php echo e($g('location')); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if($g('linkedin')): ?>
                        <div class="cv-contact-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            <span dir="ltr"><?php echo e(parse_url($g('linkedin'), PHP_URL_HOST) ?: $g('linkedin')); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if($g('github')): ?>
                        <div class="cv-contact-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            <span dir="ltr"><?php echo e(parse_url($g('github'), PHP_URL_HOST) ?: $g('github')); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if($g('portfolio_url')): ?>
                        <div class="cv-contact-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
                            <span dir="ltr"><?php echo e(parse_url($g('portfolio_url'), PHP_URL_HOST) ?: $g('portfolio_url')); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if(count($skills)): ?>
                    <div class="cv-sidebar-section">
                        <h2 class="cv-sidebar-heading"><?php echo e(__('general.cv_skills_heading')); ?></h2>
                        <div class="cv-sidebar-tags">
                            <?php $__currentLoopData = $skills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="cv-sidebar-tag"><?php echo e($s); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if(count($parsedLanguages)): ?>
                    <div class="cv-sidebar-section">
                        <h2 class="cv-sidebar-heading"><?php echo e(__('general.cv_languages_heading')); ?></h2>
                        <div class="cv-sidebar-tags">
                            <?php $__currentLoopData = $parsedLanguages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="cv-sidebar-tag"><?php echo e($lang['name']); ?><?php if($lang['level']): ?>: <?php echo e($lang['level']); ?><?php endif; ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if(count($certs)): ?>
                    <div class="cv-sidebar-section">
                        <h2 class="cv-sidebar-heading"><?php echo e(__('general.cv_certifications_heading')); ?></h2>
                        <div class="cv-sidebar-tags">
                            <?php $__currentLoopData = $certs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="cv-sidebar-tag"><?php echo e($s); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="cv-main">
                <?php if($g('summary')): ?>
                    <section class="cv-main-section">
                        <h2 class="cv-main-heading"><?php echo e(__('general.cv_profile_heading')); ?></h2>
                        <p class="cv-profile-text"><?php echo e($g('summary')); ?></p>
                    </section>
                <?php endif; ?>

                <?php $previewExperience = old('experience_items', $experienceItems); ?>
                <?php if(collect($previewExperience)->contains(fn ($i) => ($i['title'] ?? '') !== '' || ($i['description'] ?? '') !== '')): ?>
                    <section class="cv-main-section">
                        <h2 class="cv-main-heading"><?php echo e(__('general.cv_experience_heading')); ?></h2>
                        <?php $__currentLoopData = $previewExperience; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(($entry['title'] ?? '') !== '' || ($entry['description'] ?? '') !== ''): ?>
                                <?php $bullets = \App\Http\Controllers\ProfileController::descriptionBullets($entry['description'] ?? ''); ?>
                                <div class="cv-entry">
                                    <div class="cv-entry-head">
                                        <h3 class="cv-entry-title"><?php echo e($entry['title'] ?? ''); ?></h3>
                                        <?php if($entry['dates'] ?? ''): ?><span class="cv-entry-dates"><?php echo e($entry['dates']); ?></span><?php endif; ?>
                                    </div>
                                    <?php if($entry['company'] ?? ''): ?><p class="cv-entry-sub"><?php echo e($entry['company']); ?></p><?php endif; ?>
                                    <?php if(count($bullets)): ?>
                                        <ul class="cv-bullets"><?php $__currentLoopData = $bullets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($b); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                                    <?php elseif($entry['description'] ?? ''): ?>
                                        <p class="cv-profile-text"><?php echo e($entry['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </section>
                <?php endif; ?>

                <?php $previewProjects = old('project_items', $projectItems); ?>
                <?php if(collect($previewProjects)->contains(fn ($i) => ($i['title'] ?? '') !== '' || ($i['description'] ?? '') !== '')): ?>
                    <section class="cv-main-section">
                        <h2 class="cv-main-heading"><?php echo e(__('general.cv_projects_heading')); ?></h2>
                        <?php $__currentLoopData = $previewProjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(($entry['title'] ?? '') !== '' || ($entry['description'] ?? '') !== ''): ?>
                                <?php $bullets = \App\Http\Controllers\ProfileController::descriptionBullets($entry['description'] ?? ''); ?>
                                <div class="cv-entry">
                                    <div class="cv-entry-head">
                                        <h3 class="cv-entry-title"><?php echo e($entry['title'] ?? ''); ?></h3>
                                        <?php if($entry['dates'] ?? ''): ?><span class="cv-entry-dates"><?php echo e($entry['dates']); ?></span><?php endif; ?>
                                    </div>
                                    <?php if($entry['url'] ?? ''): ?>
                                        <p class="cv-entry-sub"><a href="<?php echo e($entry['url']); ?>" class="cv-entry-link" dir="ltr"><?php echo e(parse_url($entry['url'], PHP_URL_HOST) ?: $entry['url']); ?></a></p>
                                    <?php endif; ?>
                                    <?php if(count($bullets)): ?>
                                        <ul class="cv-bullets"><?php $__currentLoopData = $bullets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($b); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                                    <?php elseif($entry['description'] ?? ''): ?>
                                        <p class="cv-profile-text"><?php echo e($entry['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </section>
                <?php endif; ?>

                <?php $previewEducation = old('education_items', $educationItems); ?>
                <?php if(collect($previewEducation)->contains(fn ($i) => ($i['title'] ?? '') !== '' || ($i['institution'] ?? '') !== '')): ?>
                    <section class="cv-main-section">
                        <h2 class="cv-main-heading"><?php echo e(__('general.cv_education_heading')); ?></h2>
                        <?php $__currentLoopData = $previewEducation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(($entry['title'] ?? '') !== '' || ($entry['institution'] ?? '') !== ''): ?>
                                <?php $bullets = \App\Http\Controllers\ProfileController::descriptionBullets($entry['description'] ?? ''); ?>
                                <div class="cv-entry">
                                    <div class="cv-entry-head">
                                        <h3 class="cv-entry-title"><?php echo e($entry['title'] ?? ''); ?></h3>
                                        <?php if($entry['dates'] ?? ''): ?><span class="cv-entry-dates"><?php echo e($entry['dates']); ?></span><?php endif; ?>
                                    </div>
                                    <?php if($entry['institution'] ?? ''): ?><p class="cv-entry-sub"><em><?php echo e($entry['institution']); ?></em></p><?php endif; ?>
                                    <?php if(count($bullets)): ?>
                                        <ul class="cv-bullets"><?php $__currentLoopData = $bullets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($b); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                                    <?php elseif($entry['description'] ?? ''): ?>
                                        <p class="cv-profile-text"><?php echo e($entry['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </section>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\profile\cv-builder.blade.php ENDPATH**/ ?>