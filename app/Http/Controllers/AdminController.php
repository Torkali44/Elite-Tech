<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Idea;
use App\Models\IdeaComment;
use App\Models\ImplementRequest;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    private function logAdminAction(string $action, Model $subject, ?array $changes = null): void
    {
        if (auth()->check()) {
            ActivityLog::create([
                'admin_id'     => auth()->id(),
                'action'       => $action,
                'subject_type' => get_class($subject),
                'subject_id'   => $subject->getKey(),
                'changes'      => $changes,
            ]);
        }
    }

    public function dashboard()
    {
        $ideasPublished = Idea::where('status', 'published')->count();
        $implementStarted = ImplementRequest::count();
        $conversion = $ideasPublished > 0
            ? round(($implementStarted / $ideasPublished) * 100, 1)
            : 0;

        $avgKycHours = $this->getAverageKycHours();

        $stats = [
            'users' => User::count(),
            'ideas' => Idea::count(),
            'published' => $ideasPublished,
            'conversion' => $conversion,
            'avg_kyc_sla' => $avgKycHours !== null ? ($avgKycHours . ' ساعة') : '—',
        ];

        $recentUsers = User::latest()->take(6)->get();
        $pendingKyc = Verification::with('user')->where('status', 'pending')->latest()->take(6)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'pendingKyc'));
    }

    public function users(Request $request)
    {
        $query = User::query()->latest();

        if ($request->filled('kyc')) {
            $query->where('kyc_status', $request->kyc);
        }
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($b) use ($q) {
                $b->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('id', $q);
            });
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function ideas(Request $request)
    {
        $query = Idea::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $ideas = $query->paginate(20)->withQueryString();

        return view('admin.ideas', compact('ideas'));
    }

    public function verifications(Request $request)
    {
        $query = Verification::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending');
        }

        $verifications = $query->paginate(20)->withQueryString();

        return view('admin.verifications', compact('verifications'));
    }

    public function implementations(Request $request)
    {
        $query = ImplementRequest::with(['user', 'idea.user'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // withQueryString() preserves filter params in pagination links.
        $requests = $query->paginate(20)->withQueryString();

        return view('admin.implementations', compact('requests'));
    }

    public function reports()
    {
        $byRole = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')->pluck('total', 'role');

        $ideasPublished = Idea::where('status', 'published')->count();
        $ideasTotal = Idea::count();
        $implementStarted = ImplementRequest::count();
        $conversion = $ideasPublished > 0
            ? round(($implementStarted / $ideasPublished) * 100, 1)
            : 0;

        $avgKycHours = $this->getAverageKycHours();

        $newUsersDaily = User::where('created_at', '>=', now()->subDays(14))
            ->select(DB::raw('date(created_at) as d'), DB::raw('count(*) as c'))
            ->groupBy('d')->orderBy('d')->get();

        return view('admin.reports', compact(
            'byRole', 'ideasPublished', 'ideasTotal', 'implementStarted',
            'conversion', 'avgKycHours', 'newUsersDaily'
        ));
    }

    /**
     * Compute average KYC review time in hours at the database level.
     */
    private function getAverageKycHours(): ?float
    {
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $avgKycMinutes = Verification::whereNotNull('reviewed_at')
                ->selectRaw('AVG((strftime(\'%s\', reviewed_at) - strftime(\'%s\', created_at)) / 60) as avg_minutes')
                ->value('avg_minutes');
        } else {
            $avgKycMinutes = Verification::whereNotNull('reviewed_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, reviewed_at)) as avg_minutes')
                ->value('avg_minutes');
        }

        return $avgKycMinutes !== null ? round((float) $avgKycMinutes / 60, 1) : null;
    }

    public function approveVerification($id)
    {
        $v = Verification::with('user')->findOrFail($id);
        $user = $v->user;

        $v->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        $user->forceFill([
            'kyc_status' => 'approved',
            'rejection_reason' => null,
            'is_suspended' => false,
            'show_in_jobs_forum' => $v->purpose === 'jobs_forum' || $user->wants_jobs_forum
                ? true
                : ($v->purpose === 'reevaluation' ? $user->wants_jobs_forum : $user->show_in_jobs_forum),
            'available_for_hire' => $v->purpose === 'jobs_forum' ? true : $user->available_for_hire,
        ])->save();

        // After reevaluation approval, restore forum visibility if they wanted it
        if ($v->purpose === 'reevaluation' && $user->wants_jobs_forum) {
            $user->forceFill(['show_in_jobs_forum' => true])->save();
        }

        $this->logAdminAction('approve_kyc', $v, ['kyc_status' => 'approved']);

        return back()->with('ok', 'تمت الموافقة وتفعيل صلاحيات المستخدم.');
    }

    public function rejectVerification($id, Request $request)
    {
        $data = $request->validate([
            'reason' => 'required|string|min:3|max:1000',
        ]);

        $v = Verification::with('user')->findOrFail($id);

        $v->update([
            'status' => 'rejected',
            'rejection_reason' => $data['reason'],
            'reviewed_at' => now(),
        ]);

        $v->user->forceFill([
            'kyc_status' => 'rejected',
            'rejection_reason' => $data['reason'],
            'show_in_jobs_forum' => false,
        ])->save();

        $this->logAdminAction('reject_kyc', $v, ['kyc_status' => 'rejected', 'reason' => $data['reason']]);

        return back()->with('ok', 'تم رفض الطلب وإرسال سبب الرفض للمستخدم.');
    }

    public function suspendUser($id, Request $request)
    {
        $data = $request->validate([
            'admin_notes' => 'nullable|string|max:5000',
        ]);

        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return back()->with('ok', 'لا يمكن تعليق حساب الإدارة.');
        }

        $user->forceFill([
            'is_suspended'     => true,
            'kyc_status'       => 'suspended',
            'show_in_jobs_forum' => false,
        ])->save();

        if (! empty($data['admin_notes'])) {
            $user->forceFill(['admin_notes' => $data['admin_notes']])->save();
        }

        $this->logAdminAction('suspend_user', $user, ['is_suspended' => true]);

        return back()->with('ok', 'تم تعليق الحساب وسحب شارة KYC.');
    }

    public function saveUserNotes($id, Request $request)
    {
        $data = $request->validate(['admin_notes' => 'nullable|string|max:5000']);
        $user = User::findOrFail($id);
        $user->update(['admin_notes' => $data['admin_notes']]);
        $this->logAdminAction('save_notes', $user, ['admin_notes' => $data['admin_notes']]);

        return back()->with('ok', 'تم حفظ الملاحظات الداخلية.');
    }

    public function publishIdea($id)
    {
        $idea = Idea::findOrFail($id);
        $idea->update(['status' => 'published', 'admin_notes' => null]);
        $this->logAdminAction('publish_idea', $idea, ['status' => 'published']);

        return back()->with('ok', 'تم نشر الفكرة للعامة.');
    }

    public function returnIdea($id, Request $request)
    {
        $data = $request->validate(['note' => 'required|string|min:3|max:2000']);
        $idea = Idea::findOrFail($id);

        $idea->update([
            'status' => 'draft',
            'admin_notes' => $data['note'],
        ]);

        $this->logAdminAction('return_idea', $idea, ['status' => 'draft', 'note' => $data['note']]);

        return back()->with('ok', 'أُعيدت الفكرة كمسودة مع الملاحظات.');
    }

    /**
     * Secure KYC document stream — admin session only (never public /storage).
     */
    public function document($id, string $field)
    {
        abort_unless(in_array($field, ['id_front', 'id_back', 'selfie'], true), 404);

        $v = Verification::findOrFail($id);
        $path = $v->{$field};
        abort_unless($path, 404);

        $resolved = \App\Http\Controllers\VerificationController::resolveDiskPath($path);
        abort_unless($resolved, 404);

        [$disk, $file] = $resolved;

        return Storage::disk($disk)->response($file);
    }

    public function approveImplementation($id)
    {
        $r = ImplementRequest::with(['idea', 'user'])->findOrFail($id);
        $r->update(['status' => 'approved']);
        $this->logAdminAction('approve_implement', $r, ['status' => 'approved']);

        return back()->with('ok', 'تمت الموافقة على طلب التنفيذ من لوحة الإدارة.');
    }

    public function rejectImplementation($id, Request $request)
    {
        $data = $request->validate(['reason' => 'required|string|min:3|max:1000']);
        $r = ImplementRequest::findOrFail($id);
        $r->update([
            'status' => 'rejected',
            'note' => trim(($r->note ? $r->note."\n" : '').'رفض الإدارة: '.$data['reason']),
        ]);

        $this->logAdminAction('reject_implement', $r, ['status' => 'rejected', 'reason' => $data['reason']]);

        return back()->with('ok', 'تم رفض طلب التنفيذ مع تسجيل السبب.');
    }

    public function activateUser($id)
    {
        $user = User::findOrFail($id);

        $user->forceFill([
            'is_suspended' => false,
            'kyc_status'   => $user->kyc_status === 'suspended' ? 'none' : ($user->kyc_status ?? 'none'),
        ])->save();

        $this->logAdminAction('unsuspend_user', $user, ['is_suspended' => false]);

        return back()->with('ok', 'تم إعادة تفعيل الحساب بنجاح.');
    }

    public function showIdea($id)
    {
        $idea = Idea::with(['user', 'comments.user', 'implementRequests.user'])->findOrFail($id);

        return view('admin.ideas_show', compact('idea'));
    }

    public function showImplementation($id)
    {
        $request = ImplementRequest::with(['user', 'idea.user'])->findOrFail($id);

        return view('admin.implementations_show', compact('request'));
    }

    public function deleteComment($id)
    {
        $comment = IdeaComment::findOrFail($id);
        $comment->delete();
        $this->logAdminAction('delete_comment', $comment);

        return back()->with('ok', 'تم حذف التعليق المخالف.');
    }
}
