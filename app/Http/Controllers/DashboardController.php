<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use App\Models\ImplementRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Single query for ideas with aggregated counts.
        $ideaStats = \App\Models\Idea::selectRaw(
            "COUNT(*) as total,
             SUM(status = 'published') as published,
             COALESCE(SUM(likes_count), 0) as likes"
        )
            ->where('user_id', $user->id)
            ->first();

        $myIdeas = \App\Models\Idea::where('user_id', $user->id)->latest()->take(5)->get();

        $stats = [
            'ideas'      => (int) ($ideaStats->total ?? 0),
            'published'  => (int) ($ideaStats->published ?? 0),
            'likes'      => (int) ($ideaStats->likes ?? 0),
            'implements' => ImplementRequest::where('user_id', $user->id)->count(),
            'incoming'   => ImplementRequest::whereHas('idea', fn ($q) => $q->where('user_id', $user->id))
                ->where('status', 'pending')->count(),
        ];

        $latestKyc = $user->latestVerification;

        return view('dashboards.home', compact('myIdeas', 'stats', 'latestKyc'));
    }

    public function ideaOwner()
    {
        $ideas = Idea::where('user_id', auth()->id())->latest()->get();

        return view('dashboards.idea-owner', compact('ideas'));
    }

    public function implementRequests()
    {
        $requests = ImplementRequest::with(['user', 'idea'])
            ->whereHas('idea', fn ($q) => $q->where('user_id', auth()->id()))
            ->latest()
            ->paginate(20);

        return view('dashboards.implement-requests', compact('requests'));
    }

    public function respondImplement($id, Request $request)
    {
        $data = $request->validate([
            'action' => 'required|in:approved,rejected',
            'note' => 'nullable|string|max:1000',
        ]);

        $req = ImplementRequest::with('idea')->findOrFail($id);
        abort_unless($req->idea && $req->idea->user_id === auth()->id(), 403);

        $req->update([
            'status' => $data['action'],
            'note' => $data['note'] ?: $req->note,
        ]);

        $msg = $data['action'] === 'approved'
            ? 'قبلت طلب الانضمام/التنفيذ. يمكنك التواصل عبر الرسائل.'
            : 'تم رفض طلب التنفيذ.';

        return back()->with('ok', $msg);
    }

    public function ideaSeeker()
    {
        return view('dashboards.idea-seeker');
    }

    public function developer()
    {
        return view('dashboards.developer');
    }
}
