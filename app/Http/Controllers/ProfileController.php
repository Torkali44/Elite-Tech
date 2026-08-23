<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveCvRequest;
use App\Models\Cv;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    private const CV_KEYS = [
        'title', 'summary', 'skills', 'experience', 'education', 'portfolio_url',
        'phone', 'location', 'linkedin', 'github', 'languages', 'certifications',
        'projects', 'years_experience', 'availability', 'expected_salary',
        'theme_color', 'theme_font'
    ];

    public function show($id)
    {
        $user = User::with(['cv', 'ideas'])->findOrFail($id);

        return view('profile.show', compact('user'));
    }

    public function cvBuilder()
    {
        $user = auth()->user();
        $raw = is_array($user->cv?->data) ? $user->cv->data : [];

        $data = [];
        foreach (self::CV_KEYS as $key) {
            $fallback = match ($key) {
                'title' => $user->title,
                'summary' => $user->bio,
                'portfolio_url' => $user->portfolio_url,
                'location' => $user->location,
                'skills', 'languages', 'certifications' => [],
                'theme_color' => '#1e2732',
                'theme_font' => "'Segoe UI', system-ui, -apple-system, sans-serif",
                default => '',
            };
            $value = $raw[$key] ?? $fallback;
            $data[$key] = in_array($key, ['skills', 'languages', 'certifications'], true)
                ? self::asSkills($value)
                : self::asString($value);
        }

        $experienceItems = self::asExperienceItems($raw['experience_items'] ?? null, $data['experience']);
        $projectItems = self::asProjectItems($raw['project_items'] ?? null, $data['projects']);
        $educationItems = self::asEducationItems($raw['education_items'] ?? null, $data['education']);
        $avatarDataUri = self::avatarDataUri($user);

        return view('profile.cv-builder', compact(
            'data',
            'experienceItems',
            'projectItems',
            'educationItems',
            'avatarDataUri'
        ));
    }

    public function saveCv(SaveCvRequest $request)
    {
        $payload = $request->validated();

        $user = $request->user();
        $oldCv = is_array($user->cv?->data) ? $user->cv->data : [];
        $oldTitle = self::asString($oldCv['title'] ?? $user->title);
        $oldPortfolio = self::asString($oldCv['portfolio_url'] ?? $user->portfolio_url);
        $oldLinkedin = self::asString($oldCv['linkedin'] ?? '');
        $oldGithub = self::asString($oldCv['github'] ?? '');
        $wasApproved = $user->kyc_status === 'approved';

        $experienceItems = self::asExperienceItems($payload['experience_items'] ?? []);
        $projectItems = self::asProjectItems($payload['project_items'] ?? []);
        $educationItems = self::asEducationItems($payload['education_items'] ?? []);

        $data = [
            'title' => self::asString($payload['title'] ?? ''),
            'summary' => self::asString($payload['summary'] ?? ''),
            'skills' => self::asSkills($payload['skills'] ?? ''),
            'experience' => self::experienceItemsToLegacyText($experienceItems),
            'education' => self::educationItemsToLegacyText($educationItems),
            'portfolio_url' => self::asString($payload['portfolio_url'] ?? ''),
            'phone' => self::asString($payload['phone'] ?? ''),
            'location' => self::asString($payload['location'] ?? ''),
            'linkedin' => self::asString($payload['linkedin'] ?? ''),
            'github' => self::asString($payload['github'] ?? ''),
            'languages' => self::asSkills($payload['languages'] ?? ''),
            'certifications' => self::asSkills($payload['certifications'] ?? ''),
            'projects' => self::projectItemsToLegacyText($projectItems),
            'years_experience' => self::asString($payload['years_experience'] ?? ''),
            'availability' => self::asString($payload['availability'] ?? ''),
            'expected_salary' => self::asString($payload['expected_salary'] ?? ''),
            'theme_color' => self::asString(request('theme_color', '#1e2732')),
            'theme_font' => self::asString(request('theme_font', "'Segoe UI', system-ui, -apple-system, sans-serif")),
            'experience_items' => $experienceItems,
            'project_items' => $projectItems,
            'education_items' => $educationItems,
        ];

        Cv::updateOrCreate(['user_id' => $user->id], ['data' => $data]);

        $userUpdates = [
            'title' => $data['title'] !== '' ? $data['title'] : $user->title,
            'bio' => $data['summary'] !== '' ? $data['summary'] : $user->bio,
            'portfolio_url' => $data['portfolio_url'] !== '' ? $data['portfolio_url'] : null,
            'location' => $data['location'] !== '' ? $data['location'] : $user->location,
        ];

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $userUpdates['avatar'] = $request->file('avatar')->store('avatars/'.$user->id, 'public');
        }

        $user->forceFill($userUpdates)->save();

        $reasons = [];
        if ($data['title'] !== '' && strcasecmp($data['title'], $oldTitle) !== 0) {
            $reasons[] = 'تغيير المسمى الوظيفي الرئيسي';
        }
        if ($data['portfolio_url'] !== $oldPortfolio && ($data['portfolio_url'] !== '' || $oldPortfolio !== '')) {
            $reasons[] = 'تعديل رابط سابقة الأعمال';
        }
        if ($data['linkedin'] !== $oldLinkedin && ($data['linkedin'] !== '' || $oldLinkedin !== '')) {
            $reasons[] = 'تعديل رابط LinkedIn';
        }
        if ($data['github'] !== $oldGithub && ($data['github'] !== '' || $oldGithub !== '')) {
            $reasons[] = 'تعديل رابط GitHub';
        }

        if ($wasApproved && $reasons !== []) {
            $user->fresh()->flagForKycRereview('إعادة تقييم بسبب: '.implode(' + ', $reasons));

            return redirect()->route('profile.cv')
                ->with('error', 'تم سحب شارة التوثيق مؤقتاً وإخفاؤك من المنتدى بسبب تعديل بيانات حسّاسة.');
        }

        if ($request->boolean('join_forum') && ! $user->fresh()->isKycApproved()) {
            $user->forceFill(['wants_jobs_forum' => true])->save();

            return redirect()->route('profile.cv')
                ->with('ok', 'تم حفظ السيرة. للظهور في المنتدى أكمل KYC من صفحة التوثيق.');
        }

        return redirect()->route('profile.cv')->with('ok', 'تم حفظ السيرة الذاتية.');
    }

    public static function avatarDataUri(?User $user): ?string
    {
        if (! $user?->avatar || ! Storage::disk('public')->exists($user->avatar)) {
            return null;
        }

        $mime = Storage::disk('public')->mimeType($user->avatar) ?: 'image/jpeg';

        return 'data:'.$mime.';base64,'.base64_encode(Storage::disk('public')->get($user->avatar));
    }

    public static function asExperienceItems(mixed $raw, string $legacyText = ''): array
    {
        if (is_array($raw) && $raw !== []) {
            return self::normalizeExperienceItems($raw);
        }

        if ($legacyText === '') {
            return [['title' => '', 'company' => '', 'dates' => '', 'description' => '']];
        }

        $items = [];
        foreach (self::parseLegacyBlocks($legacyText) as $block) {
            $parsed = self::parseLegacyEntry($block);
            $items[] = [
                'title' => $parsed['title'],
                'company' => $parsed['sub'],
                'dates' => $parsed['dates'],
                'description' => implode("\n", $parsed['bullets']),
            ];
        }

        return $items !== [] ? $items : [['title' => '', 'company' => '', 'dates' => '', 'description' => '']];
    }

    public static function asProjectItems(mixed $raw, string $legacyText = ''): array
    {
        if (is_array($raw) && $raw !== []) {
            return self::normalizeProjectItems($raw);
        }

        if ($legacyText === '') {
            return [['title' => '', 'dates' => '', 'description' => '', 'url' => '']];
        }

        $items = [];
        foreach (self::parseLegacyBlocks($legacyText) as $block) {
            $parsed = self::parseLegacyEntry($block);
            $items[] = [
                'title' => $parsed['title'],
                'dates' => $parsed['dates'],
                'description' => implode("\n", $parsed['bullets']) ?: $parsed['sub'],
                'url' => '',
            ];
        }

        return $items !== [] ? $items : [['title' => '', 'dates' => '', 'description' => '', 'url' => '']];
    }

    public static function asEducationItems(mixed $raw, string $legacyText = ''): array
    {
        if (is_array($raw) && $raw !== []) {
            return self::normalizeEducationItems($raw);
        }

        if ($legacyText === '') {
            return [['title' => '', 'institution' => '', 'dates' => '', 'description' => '']];
        }

        $items = [];
        foreach (self::parseLegacyBlocks($legacyText) as $block) {
            $parsed = self::parseLegacyEntry($block);
            $items[] = [
                'title' => $parsed['title'],
                'institution' => $parsed['sub'],
                'dates' => $parsed['dates'],
                'description' => implode("\n", $parsed['bullets']),
            ];
        }

        return $items !== [] ? $items : [['title' => '', 'institution' => '', 'dates' => '', 'description' => '']];
    }

    private static function normalizeExperienceItems(array $raw): array
    {
        $items = array_values(array_filter(array_map(static function ($item) {
            if (! is_array($item)) {
                return null;
            }

            $normalized = [
                'title' => trim((string) ($item['title'] ?? '')),
                'company' => trim((string) ($item['company'] ?? '')),
                'dates' => trim((string) ($item['dates'] ?? '')),
                'description' => trim((string) ($item['description'] ?? '')),
            ];

            return ($normalized['title'] !== '' || $normalized['description'] !== '' || $normalized['company'] !== '')
                ? $normalized
                : null;
        }, $raw)));

        return $items !== [] ? $items : [['title' => '', 'company' => '', 'dates' => '', 'description' => '']];
    }

    private static function normalizeProjectItems(array $raw): array
    {
        $items = array_values(array_filter(array_map(static function ($item) {
            if (! is_array($item)) {
                return null;
            }

            $normalized = [
                'title' => trim((string) ($item['title'] ?? '')),
                'dates' => trim((string) ($item['dates'] ?? '')),
                'description' => trim((string) ($item['description'] ?? '')),
                'url' => trim((string) ($item['url'] ?? '')),
            ];

            return ($normalized['title'] !== '' || $normalized['description'] !== '')
                ? $normalized
                : null;
        }, $raw)));

        return $items !== [] ? $items : [['title' => '', 'dates' => '', 'description' => '', 'url' => '']];
    }

    private static function normalizeEducationItems(array $raw): array
    {
        $items = array_values(array_filter(array_map(static function ($item) {
            if (! is_array($item)) {
                return null;
            }

            $normalized = [
                'title' => trim((string) ($item['title'] ?? '')),
                'institution' => trim((string) ($item['institution'] ?? '')),
                'dates' => trim((string) ($item['dates'] ?? '')),
                'description' => trim((string) ($item['description'] ?? '')),
            ];

            return ($normalized['title'] !== '' || $normalized['institution'] !== '' || $normalized['description'] !== '')
                ? $normalized
                : null;
        }, $raw)));

        return $items !== [] ? $items : [['title' => '', 'institution' => '', 'dates' => '', 'description' => '']];
    }

    private static function parseLegacyBlocks(string $text): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R\s*\R/u', $text))));
    }

    private static function parseLegacyEntry(string $block): array
    {
        $lines = array_values(array_filter(array_map('trim', preg_split('/\R/u', $block))));
        if ($lines === []) {
            return ['title' => '', 'sub' => '', 'dates' => '', 'bullets' => []];
        }

        $title = array_shift($lines);
        $dates = '';
        $sub = '';

        if (preg_match('/^(.+?)\s*[|–—-]\s*(.+)$/u', $title, $m)) {
            $title = trim($m[1]);
            $dates = trim($m[2]);
        }

        if ($lines !== [] && ! $dates && preg_match('/^(.+?)\s*[|–—-]\s*(.+)$/u', $lines[0], $m)) {
            $sub = trim($m[1]);
            $dates = trim($m[2]);
            array_shift($lines);
        } elseif ($lines !== [] && preg_match('/^\d{4}|^(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)/ui', $lines[0])) {
            $dates = array_shift($lines);
        } elseif ($lines !== []) {
            $sub = array_shift($lines);
        }

        $bullets = [];
        foreach ($lines as $line) {
            $bullets[] = ltrim($line, "•·-* \t");
        }

        return compact('title', 'sub', 'dates', 'bullets');
    }

    private static function experienceItemsToLegacyText(array $items): string
    {
        return collect($items)->map(function ($item) {
            $lines = [];
            $head = trim($item['title'] ?? '');
            if (($item['dates'] ?? '') !== '') {
                $head .= ($head !== '' ? ' | ' : '').trim($item['dates']);
            }
            if ($head !== '') {
                $lines[] = $head;
            }
            if (($item['company'] ?? '') !== '') {
                $lines[] = trim($item['company']);
            }
            foreach (preg_split('/\R/u', trim($item['description'] ?? '')) ?: [] as $line) {
                $line = trim($line);
                if ($line !== '') {
                    $lines[] = $line;
                }
            }

            return implode("\n", $lines);
        })->filter()->implode("\n\n");
    }

    private static function projectItemsToLegacyText(array $items): string
    {
        return collect($items)->map(function ($item) {
            $lines = [];
            $head = trim($item['title'] ?? '');
            if (($item['dates'] ?? '') !== '') {
                $head .= ($head !== '' ? ' | ' : '').trim($item['dates']);
            }
            if ($head !== '') {
                $lines[] = $head;
            }
            foreach (preg_split('/\R/u', trim($item['description'] ?? '')) ?: [] as $line) {
                $line = trim($line);
                if ($line !== '') {
                    $lines[] = $line;
                }
            }

            return implode("\n", $lines);
        })->filter()->implode("\n\n");
    }

    private static function educationItemsToLegacyText(array $items): string
    {
        return collect($items)->map(function ($item) {
            $lines = [];
            $head = trim($item['title'] ?? '');
            if (($item['dates'] ?? '') !== '') {
                $head .= ($head !== '' ? ' | ' : '').trim($item['dates']);
            }
            if ($head !== '') {
                $lines[] = $head;
            }
            if (($item['institution'] ?? '') !== '') {
                $lines[] = trim($item['institution']);
            }
            foreach (preg_split('/\R/u', trim($item['description'] ?? '')) ?: [] as $line) {
                $line = trim($line);
                if ($line !== '') {
                    $lines[] = $line;
                }
            }

            return implode("\n", $lines);
        })->filter()->implode("\n\n");
    }

    public static function descriptionBullets(string $text): array
    {
        return array_values(array_filter(array_map(
            fn ($line) => ltrim(trim($line), "•·-* \t"),
            preg_split('/\R/u', $text) ?: []
        )));
    }

    public static function asString(mixed $value): string
    {
        if (is_array($value)) {
            return collect($value)
                ->flatten()
                ->filter(fn ($v) => is_scalar($v) && $v !== '')
                ->map(fn ($v) => (string) $v)
                ->implode("\n");
        }

        return trim((string) ($value ?? ''));
    }

    public static function asSkills(mixed $value): array
    {
        if (is_string($value)) {
            return array_values(array_filter(array_map('trim', explode(',', $value))));
        }
        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn ($v) => is_scalar($v) ? trim((string) $v) : '',
            $value
        )));
    }
}
