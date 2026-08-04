<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    private const CV_KEYS = [
        'title', 'summary', 'skills', 'experience', 'education', 'portfolio_url',
        'phone', 'location', 'linkedin', 'github', 'languages', 'certifications',
        'projects', 'years_experience', 'availability', 'expected_salary',
    ];

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
                default => '',
            };
            $value = $raw[$key] ?? $fallback;
            $data[$key] = in_array($key, ['skills', 'languages', 'certifications'], true)
                ? self::asSkills($value)
                : self::asString($value);
        }

        return view('profile.cv-builder', compact('data'));
    }

    public function saveCv(Request $request)
    {
        $payload = $request->validate([
            'title' => 'nullable|string|max:120',
            'summary' => 'nullable|string|max:2000',
            'skills' => 'nullable|string|max:1000',
            'experience' => 'nullable|string|max:5000',
            'education' => 'nullable|string|max:2000',
            'portfolio_url' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:40',
            'location' => 'nullable|string|max:120',
            'linkedin' => 'nullable|url|max:255',
            'github' => 'nullable|url|max:255',
            'languages' => 'nullable|string|max:500',
            'certifications' => 'nullable|string|max:1000',
            'projects' => 'nullable|string|max:5000',
            'years_experience' => 'nullable|string|max:20',
            'availability' => 'nullable|string|max:80',
            'expected_salary' => 'nullable|string|max:80',
            'join_forum' => 'nullable|boolean',
        ]);

        $user = $request->user();
        $oldCv = is_array($user->cv?->data) ? $user->cv->data : [];
        $oldTitle = self::asString($oldCv['title'] ?? $user->title);
        $oldPortfolio = self::asString($oldCv['portfolio_url'] ?? $user->portfolio_url);
        $oldLinkedin = self::asString($oldCv['linkedin'] ?? '');
        $oldGithub = self::asString($oldCv['github'] ?? '');
        $wasApproved = $user->kyc_status === 'approved';

        $data = [
            'title' => self::asString($payload['title'] ?? ''),
            'summary' => self::asString($payload['summary'] ?? ''),
            'skills' => self::asSkills($payload['skills'] ?? ''),
            'experience' => self::asString($payload['experience'] ?? ''),
            'education' => self::asString($payload['education'] ?? ''),
            'portfolio_url' => self::asString($payload['portfolio_url'] ?? ''),
            'phone' => self::asString($payload['phone'] ?? ''),
            'location' => self::asString($payload['location'] ?? ''),
            'linkedin' => self::asString($payload['linkedin'] ?? ''),
            'github' => self::asString($payload['github'] ?? ''),
            'languages' => self::asSkills($payload['languages'] ?? ''),
            'certifications' => self::asSkills($payload['certifications'] ?? ''),
            'projects' => self::asString($payload['projects'] ?? ''),
            'years_experience' => self::asString($payload['years_experience'] ?? ''),
            'availability' => self::asString($payload['availability'] ?? ''),
            'expected_salary' => self::asString($payload['expected_salary'] ?? ''),
        ];

        Cv::updateOrCreate(['user_id' => $user->id], ['data' => $data]);

        $user->forceFill([
            'title' => $data['title'] !== '' ? $data['title'] : $user->title,
            'bio' => $data['summary'] !== '' ? $data['summary'] : $user->bio,
            'portfolio_url' => $data['portfolio_url'] !== '' ? $data['portfolio_url'] : null,
            'location' => $data['location'] !== '' ? $data['location'] : $user->location,
        ])->save();

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

            return redirect()->route('dashboard')
                ->with('error', 'تم سحب شارة التوثيق مؤقتاً وإخفاؤك من المنتدى بسبب تعديل بيانات حسّاسة.');
        }

        if ($request->boolean('join_forum') && ! $user->fresh()->isKycApproved()) {
            $user->forceFill(['wants_jobs_forum' => true])->save();

            return redirect()->route('verification.kyc', ['purpose' => 'jobs_forum'])
                ->with('ok', 'تم حفظ السيرة. للظهور في المنتدى أكمل KYC.');
        }

        return back()->with('ok', 'تم حفظ السيرة الذاتية.');
    }

    public function show($id)
    {
        $user = User::with('cv')->findOrFail($id);

        return view('profile.show', compact('user'));
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
