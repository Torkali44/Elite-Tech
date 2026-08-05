<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VerificationController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('idea_seeker') && ! $user->hasRole('idea_owner')) {
            return redirect()->route('dashboard')->with('ok', __('dashboard.seeker_no_kyc'));
        }

        $purpose = $request->get('purpose', $user->kyc_purpose ?? 'publish_idea');
        $latest = $user->latestVerification;
        $effectiveKycStatus = ($user->hasRole('idea_seeker') && ($user->kyc_status ?? 'none') === 'approved')
            ? 'approved'
            : ($latest?->status ?? ($user->kyc_status ?? 'none'));

        return view('verification.kyc', compact('purpose', 'latest', 'effectiveKycStatus'));
    }

    public function submit(Request $request)
    {
        $data = $request->validate([
            'doc_type' => 'required|string|in:national_id,passport,driver_license',
            'purpose' => 'required|string|in:publish_idea,implement,jobs_forum',
            'id_front' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'id_back' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'selfie' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ], [
            'doc_type.required' => app()->getLocale()==='ar' ? 'اختر نوع المستند.' : 'Select document type.',
            'id_front.required' => app()->getLocale()==='ar' ? 'يرجى رفع صورة الجهة الأمامية للمستند.' : 'Please upload front side of the document.',
        ]);

        $user = $request->user();
        $dir = 'kyc/'.$user->id;

        // Private disk — admin-only access via secure route (not public /storage)
        $front = $request->file('id_front')->store($dir, 'local');
        $back = $request->hasFile('id_back')
            ? $request->file('id_back')->store($dir, 'local')
            : null;
        $selfie = $request->hasFile('selfie')
            ? $request->file('selfie')->store($dir, 'local')
            : null;

        Verification::create([
            'user_id' => $user->id,
            'doc_type' => $data['doc_type'],
            'purpose' => $data['purpose'],
            'id_front' => $front,
            'id_back' => $back,
            'selfie' => $selfie,
            'status' => 'pending',
        ]);

        $wasApproved = $user->kyc_status === 'approved';

        $user->forceFill([
            'kyc_status' => 'pending',
            'kyc_purpose' => $data['purpose'],
            'rejection_reason' => null,
            'show_in_jobs_forum' => false,
            'wants_jobs_forum' => $data['purpose'] === 'jobs_forum' ? true : $user->wants_jobs_forum,
        ])->save();

        $msg = $wasApproved
            ? (app()->getLocale()==='ar' ? 'تم رفع وثيقة جديدة — سُحبت شارة التوثيق وأُخفيت من المنتدى إلى حين إعادة المراجعة.' : 'New document uploaded — verification badge temporarily removed until review.')
            : (app()->getLocale()==='ar' ? 'تم إرسال طلب التحقق للمراجعة. ستصلك النتيجة في لوحة التحكم.' : 'Verification request submitted for review. Results will appear in your dashboard.');

        return redirect()->route('dashboard')->with('ok', $msg);
    }

    /**
     * Resolve a KYC path from private or legacy public disk.
     */
    public static function resolveDiskPath(string $path): ?array
    {
        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                return [$disk, $path];
            }
        }

        return null;
    }
}
