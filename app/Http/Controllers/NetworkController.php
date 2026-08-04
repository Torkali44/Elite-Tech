<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class NetworkController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $tab = $request->get('tab', 'inbox');
        $withId = (int) $request->get('with', 0);

        $partnerIds = Message::query()
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)->orWhere('recipient_id', $user->id);
            })
            ->when($tab === 'archive', fn ($q) => $q->where('archived', true), fn ($q) => $q->where('archived', false))
            ->get(['sender_id', 'recipient_id'])
            ->map(fn ($m) => $m->sender_id === $user->id ? $m->recipient_id : $m->sender_id)
            ->unique()
            ->values();

        $threads = collect();
        foreach ($partnerIds as $pid) {
            $partner = User::find($pid);
            if (! $partner) {
                continue;
            }

            $last = Message::query()
                ->where(function ($q) use ($user, $pid) {
                    $q->where(fn ($qq) => $qq->where('sender_id', $user->id)->where('recipient_id', $pid))
                        ->orWhere(fn ($qq) => $qq->where('sender_id', $pid)->where('recipient_id', $user->id));
                })
                ->when($tab === 'archive', fn ($q) => $q->where('archived', true), fn ($q) => $q->where('archived', false))
                ->latest()
                ->first();

            if (! $last) {
                continue;
            }

            $unread = Message::query()
                ->where('sender_id', $pid)
                ->where('recipient_id', $user->id)
                ->whereNull('read_at')
                ->where('archived', false)
                ->count();

            $threads->push([
                'id' => $pid,
                'partner' => $partner,
                'preview' => $last->body,
                'time' => $last->created_at,
                'unread' => $unread,
                'archived' => (bool) $last->archived,
            ]);
        }

        $threads = $threads->sortByDesc(fn ($t) => $t['time'])->values();

        if (! $withId && $threads->isNotEmpty()) {
            $withId = $threads->first()['id'];
        }

        $messages = collect();
        $activePartner = null;

        if ($withId && $withId === (int) $user->id) {
            return redirect()
                ->route('network.index', ['tab' => $tab])
                ->with('popup', 'لا يمكنك فتح محادثة مع نفسك. اختر عضواً آخر من الدليل.');
        }

        if ($withId) {
            $activePartner = User::find($withId);
            $messages = Message::query()
                ->where(function ($q) use ($user, $withId) {
                    $q->where(fn ($qq) => $qq->where('sender_id', $user->id)->where('recipient_id', $withId))
                        ->orWhere(fn ($qq) => $qq->where('sender_id', $withId)->where('recipient_id', $user->id));
                })
                ->orderBy('created_at')
                ->get();

            Message::query()
                ->where('sender_id', $withId)
                ->where('recipient_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        $directory = User::query()
            ->where('id', '!=', $user->id)
            ->where(function ($q) {
                $q->where('show_in_jobs_forum', true)
                    ->orWhere('kyc_status', 'approved')
                    ->orWhereIn('role', ['idea_owner', 'developer']);
            })
            ->orderBy('name')
            ->limit(40)
            ->get();

        return view('network.index', compact('threads', 'messages', 'activePartner', 'withId', 'tab', 'directory'));
    }

    public function start(Request $request)
    {
        $data = $request->validate([
            'recipient_id' => 'required|integer|exists:users,id',
            'body' => 'required|string|min:2|max:2000',
        ]);

        if ((int) $data['recipient_id'] === (int) $request->user()->id) {
            return redirect()
                ->route('network.index')
                ->with('popup', 'لا يمكنك إرسال رسالة لنفسك. اختر عضواً آخر.');
        }

        Message::create([
            'sender_id' => $request->user()->id,
            'recipient_id' => $data['recipient_id'],
            'body' => $data['body'],
            'archived' => false,
        ]);

        return redirect()
            ->route('network.index', ['with' => $data['recipient_id']])
            ->with('ok', 'تم إرسال رسالتك.');
    }

    public function reply($id, Request $request)
    {
        $partnerId = (int) $id;

        if (! User::whereKey($partnerId)->exists()) {
            return redirect()->route('network.index')->with('popup', 'المحادثة غير موجودة.');
        }

        if ($partnerId === (int) $request->user()->id) {
            return redirect()
                ->route('network.index')
                ->with('popup', 'لا يمكنك إرسال رسالة لنفسك.');
        }

        $data = $request->validate(['body' => 'required|string|min:1|max:2000']);

        Message::create([
            'sender_id' => $request->user()->id,
            'recipient_id' => $partnerId,
            'body' => $data['body'],
            'archived' => false,
        ]);

        return redirect()
            ->route('network.index', ['with' => $partnerId])
            ->with('ok', 'تم إرسال الرد.');
    }

    public function archive($id, Request $request)
    {
        $user = $request->user();
        $partnerId = (int) $id;

        Message::query()
            ->where(function ($q) use ($user, $partnerId) {
                $q->where(fn ($qq) => $qq->where('sender_id', $user->id)->where('recipient_id', $partnerId))
                    ->orWhere(fn ($qq) => $qq->where('sender_id', $partnerId)->where('recipient_id', $user->id));
            })
            ->update(['archived' => true]);

        return redirect()->route('network.index', ['tab' => 'archive'])->with('ok', 'تم أرشفة المحادثة.');
    }
}
