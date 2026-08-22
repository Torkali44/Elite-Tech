<?php

namespace App\Http\Controllers;

use App\Http\Requests\KycSubmissionRequest;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VerificationController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('idea_seeker') && ! $user->hasRole('idea_owner') && $request->get('purpose') !== 'implement') {
            return redirect()->route('dashboard')->with('ok', __('dashboard.seeker_no_kyc'));
        }

        $purpose = $request->get('purpose', $user->kyc_purpose ?? 'publish_idea');
        $latest = $user->latestVerification;
        $effectiveKycStatus = ($user->hasRole('idea_seeker') && ($user->kyc_status ?? 'none') === 'approved')
            ? 'approved'
            : ($latest?->status ?? ($user->kyc_status ?? 'none'));

        return view('verification.kyc', compact('purpose', 'latest', 'effectiveKycStatus'));
    }

    public function submit(KycSubmissionRequest $request)
    {
        $data = $request->validated();

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
     * KYC-03: Resolve a KYC path from the private local disk ONLY.
     * The public disk fallback was removed — KYC documents must never be publicly accessible.
     *
     * If legacy files exist on the public disk they must be manually migrated to
     * storage/app/private/kyc/{user_id}/ and then removed from storage/app/public/.
     */
    public static function resolveDiskPath(string $path): ?array
    {
        if (Storage::disk('local')->exists($path)) {
            return ['local', $path];
        }

        return null;
    }
}
