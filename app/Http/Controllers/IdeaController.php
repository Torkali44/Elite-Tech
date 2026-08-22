<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIdeaRequest;
use App\Http\Requests\UpdateIdeaRequest;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SystemAlert;
use App\Models\Idea;
use App\Models\IdeaComment;
use App\Models\ImplementRequest;
use Illuminate\Http\Request;

class IdeaController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all');
        $sort = $request->get('sort', 'newest');
        $user = $request->user();

        $query = Idea::with(['user', 'parent.user'])
            ->withCount('comments');

        // Tabs
        if ($tab === 'my') {
            if (! $user) {
                return redirect()->route('login')->with('error', 'سجّل الدخول لعرض أفكارك.');
            }
            $query->where('user_id', $user->id)
                ->whereIn('status', ['published', 'pending', 'draft']);
        } elseif ($tab === 'favorites') {
            if (! $user) {
                return redirect()->route('login')->with('error', 'سجّل الدخول لعرض المفضلة.');
            }
            $query->where('status', 'published')
                ->whereHas('favoritedBy', fn ($q) => $q->where('user_id', $user->id));
        } else {
            // all / community → published community ideas
            $query->where('status', 'published');
        }

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($b) use ($q) {
                $b->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('category', 'like', "%{$q}%");
            });
        }

        if ($request->filled('category') && $request->category !== 'الكل') {
            $query->where('category', $request->category);
        }

        if ($request->filled('budget')) {
            $query->where('budget', $request->budget);
        }

        if ($sort === 'popular') {
            $query->orderByDesc('likes_count')->orderByDesc('id');
        } else {
            $query->latest();
        }

        if ($user) {
            $favIds = $user->favoriteIdeas()->pluck('ideas.id')->all();
            $query->withExists(['favoritedBy as is_favorited' => fn ($q) => $q->where('users.id', $user->id)]);
        } else {
            $favIds = [];
        }

        $ideas = $query->paginate(12)->withQueryString();
        $categories = Idea::where('status', 'published')->distinct()->orderBy('category')->pluck('category');

        $stats = [
            'total' => Idea::where('status', 'published')->count(),
            'contributors' => Idea::where('status', 'published')->distinct('user_id')->count('user_id'),
            'top_category' => Idea::where('status', 'published')
                ->selectRaw('category, count(*) as c')
                ->groupBy('category')
                ->orderByDesc('c')
                ->value('category') ?: '—',
        ];

        return view('ideas.index', compact('ideas', 'categories', 'tab', 'sort', 'stats', 'favIds'));
    }

    public function toggleFavorite(Idea $idea)
    {
        abort_unless($idea->status === 'published', 404);
        $user = auth()->user();

        if ($user->favoriteIdeas()->where('idea_id', $idea->id)->exists()) {
            $user->favoriteIdeas()->detach($idea->id);
            $liked = false;
        } else {
            $user->favoriteIdeas()->syncWithoutDetaching([$idea->id]);
            $liked = true;
        }

        if (request()->wantsJson()) {
            return response()->json(['favorited' => $liked]);
        }

        return back()->with('ok', $liked ? 'أُضيفت للمفضلة.' : 'أُزيلت من المفضلة.');
    }

    public function show(Idea $idea)
    {
        $isOwner = auth()->check() && auth()->id() === $idea->user_id;
        abort_unless($idea->status === 'published' || $isOwner, 404);

        $idea->load(['user', 'comments.user', 'parent.user'])->loadCount('comments');

        $userRequest = auth()->check()
            ? ImplementRequest::where('idea_id', $idea->id)->where('user_id', auth()->id())->first()
            : null;

        $isFavorited = auth()->check()
            ? auth()->user()->favoriteIdeas()->where('idea_id', $idea->id)->exists()
            : false;

        return view('ideas.show', compact('idea', 'userRequest', 'isFavorited'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        if (! $user->hasRole('idea_owner')) {
            return redirect()
                ->route('auth.path')
                ->with('error', 'لنشر فكرة اختر مسار «صاحب فكرة» أولاً.');
        }

        if (! $user->isKycApproved()) {
            return redirect()
                ->route('verification.kyc', ['purpose' => 'publish_idea'])
                ->with('error', 'أهلية النشر تتطلب اجتياز التحقق (KYC) أولاً.');
        }

        $parents = Idea::with('user')
            ->where('status', 'published')
            ->latest()
            ->limit(50)
            ->get();

        $prefill = null;
        if ($request->filled('parent_id')) {
            $prefill = Idea::with('user')->where('status', 'published')->find($request->parent_id);
        }

        return view('ideas.create', [
            'idea' => null,
            'parents' => $parents,
            'prefill' => $prefill,
        ]);
    }

    public function store(StoreIdeaRequest $request)
    {
        $user = $request->user();

        if (! $user->hasRole('idea_owner') || ! $user->isKycApproved()) {
            return redirect()
                ->route('verification.kyc', ['purpose' => 'publish_idea'])
                ->with('error', 'لا يمكن إرسال فكرة قبل مسار صاحب فكرة وموافقة KYC.');
        }

        $data = $request->validated();



        $parentId = null;
        if (($data['based_on_previous'] ?? 'no') === 'yes') {
            $parentId = $data['parent_id'] ?? null;
            if (! $parentId || ! Idea::where('status', 'published')->whereKey($parentId)->exists()) {
                return back()->withInput()->withErrors([
                    'parent_id' => 'اختر الفكرة الأصلية من بنك الأفكار.',
                ]);
            }
        }

        $idea = Idea::create([
            'user_id'      => $user->id,
            'forked_from'  => $parentId,
            'title'        => $data['title'],
            'category'     => $data['category'],
            'description'  => $this->composeDescription($data),
            'feasibility'  => $data['feasibility'] ?? null,
            'technologies' => array_values(array_filter($data['technologies'] ?? [])),
            'budget'       => $data['budget'] ?? null,
        ]);
        // System-controlled fields set via direct assignment (not user mass-assignment)
        $idea->status      = ($data['intent'] ?? 'draft') === 'pending' ? 'pending' : 'draft';
        $idea->admin_notes = null;
        $idea->likes_count = 0;
        $idea->save();

        if ($idea->status === 'pending') {
            Notification::send(User::getAdmins(), new SystemAlert(
                'فكرة جديدة للمراجعة',
                "تم تقديم فكرة «{$idea->title}» للمراجعة من قبل {$user->name}.",
                route('admin.ideas.show', $idea->id),
                'lightbulb'
            ));
        }

        $msg = $idea->status === 'draft'
            ? 'تم حفظ الفكرة كمسودة. يمكنك إرسالها للمراجعة لاحقاً.'
            : 'تم إرسال الفكرة للمراجعة الإدارية. ستظهر في بنك الأفكار بعد القبول.';

        return redirect()->route('dashboard.ideaOwner')->with('ok', $msg);
    }

    /** إرسال مسودة للمراجعة الإدارية (خطوة النشر) */
    public function submitDraft(Idea $idea)
    {
        $user = auth()->user();
        abort_unless($user && $user->id === $idea->user_id, 404);

        if (! $user->hasRole('idea_owner') || ! $user->isKycApproved()) {
            return redirect()
                ->route('verification.kyc', ['purpose' => 'publish_idea'])
                ->with('popup', 'إرسال الفكرة للنشر يتطلب مسار صاحب فكرة وموافقة KYC.');
        }

        if ($idea->status === 'published') {
            return back()->with('popup', 'هذه الفكرة منشورة بالفعل.');
        }

        if ($idea->status === 'pending') {
            return back()->with('popup', 'الفكرة قيد المراجعة الإدارية بالفعل.');
        }

        // System-controlled fields set via direct assignment (not user mass-assignment)
        $idea->status      = 'pending';
        $idea->admin_notes = null;
        $idea->save();

        Notification::send(User::getAdmins(), new SystemAlert(
            'فكرة جديدة للمراجعة',
            "تم تقديم فكرة «{$idea->title}» للمراجعة من قبل {$user->name}.",
            route('admin.ideas.show', $idea->id),
            'lightbulb'
        ));

        return back()->with('ok', 'تم إرسال الفكرة للمراجعة الإدارية. ستظهر في بنك الأفكار بعد الموافقة.');
    }

    public function edit(Idea $idea)
    {
        abort_unless(auth()->check() && auth()->id() === $idea->user_id, 404);

        if (! auth()->user()->isKycApproved() && $idea->status !== 'draft') {
            return redirect()->route('verification.kyc', ['purpose' => 'publish_idea']);
        }

        $idea->load('parent');
        $parents = Idea::with('user')->where('status', 'published')->latest()->limit(50)->get();
        $parsed = $this->parseDescription($idea->description);

        return view('ideas.create', [
            'idea' => $idea,
            'parents' => $parents,
            'prefill' => null,
            'parsed' => $parsed,
        ]);
    }

    public function update(UpdateIdeaRequest $request, Idea $idea)
    {
        abort_unless($request->user()->id === $idea->user_id, 404);

        if (! $request->user()->isKycApproved()) {
            return redirect()->route('verification.kyc', ['purpose' => 'publish_idea'])
                ->with('error', 'أعد اجتياز KYC قبل إرسال الفكرة للمراجعة.');
        }

        $data = $request->validated();



        $parentId = $idea->forked_from;
        if (($data['based_on_previous'] ?? 'no') === 'yes') {
            $parentId = $data['parent_id'] ?? $idea->forked_from;
        } elseif (($data['based_on_previous'] ?? null) === 'no') {
            $parentId = null;
        }

        $intent = $data['intent'] ?? 'draft';

        $idea->fill([
            'forked_from'  => $parentId,
            'title'        => $data['title'],
            'category'     => $data['category'],
            'description'  => $this->composeDescription($data),
            'feasibility'  => $data['feasibility'] ?? null,
            'technologies' => array_values(array_filter($data['technologies'] ?? [])),
            'budget'       => $data['budget'] ?? null,
        ]);
        // System-controlled fields set via direct assignment (not user mass-assignment)
        $idea->status      = $intent === 'pending' ? 'pending' : 'draft';
        $idea->admin_notes = $intent === 'pending' ? null : $idea->getOriginal('admin_notes');
        $idea->save();

        $msg = $intent === 'draft'
            ? 'تم تحديث المسودة.'
            : 'تم إرسال الفكرة للمراجعة الإدارية.';

        return redirect()->route('dashboard.ideaOwner')->with('ok', $msg);
    }

    public function forkConfirm(Idea $idea)
    {
        abort_unless($idea->status === 'published', 404);

        $user = auth()->user();

        if (! $user->hasRole('idea_owner')) {
            return redirect()->route('auth.path')
                ->with('error', 'لاستنساخ فكرة ونشر تطويرك اختر مسار «صاحب فكرة» ثم أكمل KYC.');
        }

        if (! $user->isKycApproved()) {
            return redirect()->route('verification.kyc', ['purpose' => 'publish_idea'])
                ->with('error', 'الاستنساخ يتطلب KYC قبل إنشاء المسودة.');
        }

        $idea->load('user');

        return view('ideas.fork-confirm', compact('idea'));
    }

    public function fork(Idea $idea)
    {
        abort_unless($idea->status === 'published', 404);

        $user = auth()->user();

        if (! $user->hasRole('idea_owner')) {
            return redirect()->route('auth.path')
                ->with('error', 'لاستنساخ فكرة اختر مسار صاحب فكرة.');
        }

        if (! $user->isKycApproved()) {
            return redirect()->route('verification.kyc', ['purpose' => 'publish_idea'])
                ->with('error', 'الاستنساخ والنشر يتطلبان KYC.');
        }

        $source = $idea->load('user');

        $copy = $source->replicate(['likes_count', 'admin_notes', 'status']);
        $copy->user_id = $user->id;
        $copy->forked_from = $source->id;
        $copy->title = 'تطوير: '.$source->title;
        $copy->status = 'draft';
        $copy->likes_count = 0;
        $copy->admin_notes = null;
        $copy->save();

        return redirect()->route('ideas.edit', $copy->id)
            ->with('ok', 'تم إنشاء مسودة مستلهمة من الفكرة الأصلية مع حفظ حقوق صاحبها. أكملها ثم أرسلها للمراجعة.');
    }

    public function implementForm(Idea $idea)
    {
        abort_unless($idea->status === 'published', 404);

        $user = auth()->user();

        if (! $user->isKycApproved()) {
            return redirect()
                ->route('verification.kyc', ['purpose' => 'implement', 'idea' => $idea->id])
                ->with('error', 'الرغبة في التنفيذ تتطلب اجتياز KYC لضمان الجدية.');
        }

        $idea->load('user');
        $existing = ImplementRequest::where('idea_id', $idea->id)->where('user_id', $user->id)->first();

        return view('ideas.implement', compact('idea', 'existing'));
    }

    /** GET /ideas/{idea}/comment → صفحة التفاصيل (التعليق يتم بـ POST) */
    public function commentPage(Idea $idea)
    {
        return redirect()->route('ideas.show', $idea->id)->withFragment('comments');
    }

    public function comment(Idea $idea, Request $request)
    {
        abort_unless($idea->status === 'published', 404);

        $data = $request->validate(['body' => 'required|string|max:2000']);

        IdeaComment::create([
            'idea_id' => $idea->id,
            'user_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        return redirect()->route('ideas.show', $idea->id)
            ->withFragment('comments')
            ->with('ok', 'تم إضافة تعليقك.');
    }

    public function implement(Idea $idea, Request $request)
    {
        abort_unless($idea->status === 'published', 404);

        $user = $request->user();

        if (! $user->isKycApproved()) {
            return redirect()
                ->route('verification.kyc', ['purpose' => 'implement', 'idea' => $idea->id])
                ->with('error', 'الرغبة في التنفيذ تتطلب اجتياز KYC لضمان الجدية.');
        }

        $data = $request->validate([
            'via' => 'required|in:elite_tech,idea_owner',
            'note' => 'nullable|string|max:1000',
            'agree_terms' => 'accepted',
        ], [
            'agree_terms.accepted' => 'يجب الموافقة على اتفاقية الاستخدام للمتابعة.',
        ]);

        $req = ImplementRequest::updateOrCreate(
            ['idea_id' => $idea->id, 'user_id' => $user->id],
            ['via' => $data['via'], 'note' => $data['note'] ?? null, 'status' => 'pending']
        );

        $idea->load('user');

        // Notify Admin
        Notification::send(User::getAdmins(), new SystemAlert(
            'طلب تنفيذ جديد',
            "قدم {$user->name} طلب تنفيذ للفكرة «{$idea->title}».",
            route('admin.implementations.show', $req->id),
            'cog'
        ));

        // Notify Idea Owner
        if ($idea->user_id !== $user->id) {
            $idea->user->notify(new SystemAlert(
                'طلب تنفيذ لفكرتك',
                "هناك مطور مهتم بتنفيذ فكرتك «{$idea->title}».",
                route('dashboard.implementRequests'),
                'check'
            ));
        }

        return redirect()->route('ideas.show', $idea->id)
            ->with('ok', 'تم تسجيل رغبتك في التنفيذ. سيتم مراجعة الطلب.');
    }

    private function validateIdea(Request $request, bool $updating = false): array
    {
        return $request->validate([
            'based_on_previous' => 'nullable|in:yes,no',
            'parent_id' => 'nullable|integer|exists:ideas,id',
            'title' => 'required|string|max:120',
            'summary' => 'required|string|max:300',
            'problem' => 'required|string|min:20',
            'solution' => 'required|string|min:20',
            'category' => 'required|string|max:80',
            'budget' => 'nullable|numeric|min:0',
            'technologies' => 'nullable|array',
            'technologies.*' => 'string|max:60',
            'feasibility' => 'nullable|string|max:5000',
            'ip_agreement' => $updating ? 'nullable' : 'accepted',
            'intent' => 'nullable|in:draft,pending',
        ], [
            'problem.min' => 'وضّح المشكلة بوضوح (20 حرفاً على الأقل).',
            'solution.min' => 'وضّح الحل بوضوح (20 حرفاً على الأقل).',
            'summary.required' => 'الوصف المختصر مطلوب ضمن معايير القبول.',
            'ip_agreement.accepted' => 'يجب الموافقة على إقرار الملكية الفكرية وآلية الـ Fork.',
        ]);
    }

    private function composeDescription(array $data): string
    {
        return trim($data['summary'])."\n\n"
            ."المشكلة:\n".trim($data['problem'])."\n\n"
            ."الحل:\n".trim($data['solution'])."\n\n"
            ."المتطلبات التقنية:\n".(
                ! empty($data['technologies'])
                    ? implode(', ', $data['technologies'])
                    : 'غير محددة'
            );
    }

    private function parseDescription(?string $description): array
    {
        $description = (string) $description;
        $summary = $description;
        $problem = '';
        $solution = '';

        if (preg_match('/^(.*?)\n\nالمشكلة:\n(.*?)\n\nالحل:\n(.*?)(?:\n\nالمتطلبات التقنية:\n.*)?$/s', $description, $m)) {
            $summary = trim($m[1]);
            $problem = trim($m[2]);
            $solution = trim($m[3]);
        }

        return compact('summary', 'problem', 'solution');
    }
}
