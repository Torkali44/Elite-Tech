<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $visibility = is_array($user->cv?->visibility) ? $user->cv->visibility : [];
        $cvData = is_array($user->cv?->data) ? $user->cv->data : [];

        return view('settings.index', compact('visibility', 'cvData'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'title' => 'nullable|string|max:120',
            'bio' => 'nullable|string|max:2000',
            'portfolio_url' => 'nullable|url|max:255',
            'avatar' => 'nullable|image|max:2048',
            'current_password' => ['required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'available_for_hire' => 'nullable|boolean',
            'location' => 'nullable|string|max:120',
            'employment_type' => 'nullable|in:full_time,part_time,contract',
            'work_style' => 'nullable|in:remote,hybrid,onsite',
            'target_salary' => 'nullable|string|max:80',
            'show_email' => 'nullable|boolean',
            'show_phone' => 'nullable|boolean',
        ]);

        $wasApproved = $user->kyc_status === 'approved';
        $oldTitle = (string) ($user->title ?? '');
        $oldPortfolio = (string) ($user->portfolio_url ?? '');

        $updates = [
            'name' => $data['name'],
            'email' => $data['email'],
            'available_for_hire' => $request->boolean('available_for_hire'),
            'location' => $data['location'] ?? $user->location,
        ];

        if (array_key_exists('bio', $data)) {
            $updates['bio'] = $data['bio'];
        }

        if (! empty($data['password'])) {
            $updates['password'] = $data['password'];
        }

        if ($request->hasFile('avatar')) {
            $updates['avatar'] = $request->file('avatar')->store('avatars/'.$user->id, 'public');
        }

        $reasons = [];

        if (array_key_exists('title', $data) && $data['title'] !== null) {
            $newTitle = trim((string) $data['title']);
            if ($newTitle !== '' && strcasecmp($newTitle, $oldTitle) !== 0) {
                $reasons[] = 'تغيير المسمى الوظيفي الرئيسي';
            }
            $updates['title'] = $data['title'];
        }

        if (array_key_exists('portfolio_url', $data)) {
            $newPortfolio = trim((string) ($data['portfolio_url'] ?? ''));
            if ($newPortfolio !== $oldPortfolio && ($newPortfolio !== '' || $oldPortfolio !== '')) {
                $reasons[] = 'تعديل رابط سابقة الأعمال';
            }
            $updates['portfolio_url'] = $data['portfolio_url'] ?: null;
        }

        $user->update($updates);

        // تفضيلات الظهور في منتدى التوظيف (تُحفظ في CV visibility)
        $cv = Cv::firstOrCreate(['user_id' => $user->id], ['data' => []]);
        $cvData = is_array($cv->data) ? $cv->data : [];
        if (! empty($data['target_salary'])) {
            $cvData['expected_salary'] = $data['target_salary'];
        }
        if (! empty($data['location'])) {
            $cvData['location'] = $data['location'];
        }
        $cv->forceFill([
            'data' => $cvData,
            'visibility' => [
                'show_email' => $request->boolean('show_email'),
                'show_phone' => $request->boolean('show_phone'),
                'employment_type' => $data['employment_type'] ?? 'full_time',
                'work_style' => $data['work_style'] ?? 'remote',
                'target_salary' => $data['target_salary'] ?? ($cvData['expected_salary'] ?? ''),
            ],
        ])->save();

        if ($wasApproved && $reasons !== []) {
            $user->fresh()->flagForKycRereview('إعادة تقييم بسبب: '.implode(' + ', $reasons));

            return back()->with('error', 'تم سحب شارة التوثيق وإخفاؤك من المنتدى. الحساب قيد مراجعة KYC بسبب تعديل بيانات حسّاسة.');
        }

        return back()->with('ok', 'تم حفظ إعدادات الظهور والحساب. محتوى السيرة يُعدَّل من صفحة بناء الـ CV.');
    }
}
