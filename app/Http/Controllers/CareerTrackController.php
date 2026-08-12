<?php

namespace App\Http\Controllers;

use App\Models\CareerTrack;
use Illuminate\Http\Request;

class CareerTrackController extends Controller
{
    private const TRACK_DEFINITIONS = [
        'idea-owner' => [
            'slug' => 'idea-owner',
            'title' => 'مسار صاحب فكرة',
            'subtitle' => 'متابعة حالة تفعيل مسارك الريادي والخطوات القادمة.',
            'icon' => '💡',
        ],
        'developer' => [
            'slug' => 'developer',
            'title' => 'مسار مطور البرمجيات',
            'subtitle' => 'متابعة حالة تفعيل ملفك كمطور برمجيات نخبة.',
            'icon' => '⟨/⟩',
        ],
        'idea-seeker' => [
            'slug' => 'idea-seeker',
            'title' => 'مسار باحث عن فكرة',
            'subtitle' => 'متابعة حالة تفعيل حسابك كشريك ومستثمر في الأفكار.',
            'icon' => '🔍',
        ],
    ];

    public function index(): \Illuminate\View\View
    {
        $user = auth()->user();
        $userTracks = CareerTrack::where('user_id', $user->id)->get()->keyBy('slug');

        $tracks = collect(self::TRACK_DEFINITIONS)->map(function ($def, $slug) use ($user, $userTracks) {
            $record = $userTracks->get($slug);
            $status = $record?->status ?? ($user->isKycApproved() ? 'done' : ($user->kyc_status === 'pending' ? 'current' : 'pending'));

            $statusLabel = match ($status) {
                'done' => 'نشط',
                'current' => 'قيد المراجعة',
                'rejected' => 'مرفوض',
                default => 'قيد الانتظار',
            };

            $statusColor = match ($status) {
                'done' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                'current' => 'bg-amber-50 text-amber-600 border-amber-200',
                'rejected' => 'bg-rose-50 text-rose-600 border-rose-200',
                default => 'bg-slate-50 text-slate-600 border-slate-200',
            };

            return array_merge($def, [
                'status' => $statusLabel,
                'statusColor' => $statusColor,
            ]);
        })->values();

        return view('career-tracks.index', compact('tracks'));
    }

    public function show(string $slug): \Illuminate\View\View
    {
        abort_unless(isset(self::TRACK_DEFINITIONS[$slug]), 404);

        $user = auth()->user()->load(['ideas', 'cv', 'latestVerification']);
        $def = self::TRACK_DEFINITIONS[$slug];

        $trackRecord = CareerTrack::firstOrCreate(
            ['user_id' => $user->id, 'slug' => $slug],
            ['status' => 'pending']
        );

        $isApproved = $user->isKycApproved();
        $isPending = $user->kyc_status === 'pending';

        $statusLabel = match ($trackRecord->status) {
            'done' => 'نشط / مكتمل',
            'current' => 'قيد المراجعة',
            'rejected' => 'مرفوض',
            default => ($isApproved ? 'نشط / مكتمل' : ($isPending ? 'قيد المراجعة' : 'في انتظار الاستكمال')),
        };

        $statusColor = match ($trackRecord->status) {
            'done' => 'bg-emerald-600 text-white',
            'current' => 'bg-amber-500 text-white shadow-glow',
            'rejected' => 'bg-rose-600 text-white',
            default => ($isApproved ? 'bg-emerald-600 text-white' : ($isPending ? 'bg-amber-500 text-white shadow-glow' : 'bg-slate-600 text-white')),
        };

        $firstIdea = $user->ideas->first();

        $projectName = match ($slug) {
            'idea-owner' => $firstIdea?->title ?? 'لم يتم تقديم فكرة بعد',
            'developer' => $user->cv?->data['title'] ?? ($user->title ?: 'ملف أعمال المطور البرمجي'),
            default => 'ملف الشراكة والبحث',
        };

        $projectCategory = match ($slug) {
            'idea-owner' => $firstIdea?->category ?? 'عام',
            'developer' => 'برمجيات',
            default => 'استثمار وشراكة',
        };

        $submissionDate = match ($slug) {
            'idea-owner' => $firstIdea?->created_at?->format('Y-m-d') ?? $user->created_at->format('Y-m-d'),
            default => $user->created_at->format('Y-m-d'),
        };

        $steps = [
            [
                'title' => 'تقديم الطلب والبيانات',
                'desc' => 'تم تسجيل الحساب والبيانات الأساسية بنجاح.',
                'state' => 'done',
                'stateLabel' => 'مكتمل',
                'icon' => '✓',
            ],
            [
                'title' => 'مراجعة الهوية والتسجيل (KYC)',
                'desc' => $isApproved ? 'تم التوثيق والموافقة على الهوية.' : ($isPending ? 'الملف قيد المراجعة لدى الإدارة.' : 'بانتظار تقديم طلب التوثيق (KYC).'),
                'state' => $isApproved ? 'done' : ($isPending ? 'current' : 'pending'),
                'stateLabel' => $isApproved ? 'مكتمل' : ($isPending ? 'قيد المراجعة...' : 'معلق'),
                'icon' => $isApproved ? '✓' : ($isPending ? '🔄' : '🔒'),
            ],
            [
                'title' => 'تفعيل المسار التقني',
                'desc' => $isApproved ? 'المسار نشط ومفعل في المجتمع.' : 'سيتم التفعيل المباشر فور اعتماد التوثيق.',
                'state' => $isApproved ? 'done' : 'pending',
                'stateLabel' => $isApproved ? 'نشط' : 'في الانتظار',
                'icon' => $isApproved ? '⚡' : '🔒',
            ],
        ];

        $track = array_merge($def, [
            'statusLabel' => $statusLabel,
            'statusColor' => $statusColor,
            'projectName' => $projectName,
            'projectCategory' => $projectCategory,
            'submissionDate' => $submissionDate,
            'needsAction' => ! $isApproved && ! $isPending,
            'github' => $trackRecord->github,
            'notes' => $trackRecord->admin_notes,
            'steps' => $steps,
        ]);

        return view('career-tracks.show', compact('track'));
    }

    public function update(string $slug, Request $request): \Illuminate\Http\RedirectResponse
    {
        abort_unless(isset(self::TRACK_DEFINITIONS[$slug]), 404);

        $data = $request->validate([
            'github' => 'nullable|url|max:255',
            'notes'  => 'nullable|string|max:1000',
        ]);

        $track = CareerTrack::firstOrCreate(
            ['user_id' => auth()->id(), 'slug' => $slug]
        );

        $track->update([
            'github'      => $data['github'] ?? $track->github,
            'admin_notes' => $data['notes'] ?? $track->admin_notes,
            'status'      => 'current',
        ]);

        return redirect()->route('career-tracks.show', $slug)->with('ok', 'تم تحديث بيانات المسار بنجاح.');
    }
}
