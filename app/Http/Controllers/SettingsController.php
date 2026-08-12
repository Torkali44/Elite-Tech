<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Cv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

    public function update(UpdateSettingsRequest $request)
    {
        $user = $request->user();

        $data = $request->validated();

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
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
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
