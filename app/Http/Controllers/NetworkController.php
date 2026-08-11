<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NetworkController extends Controller
{
    public function index(Request $request)
    {
        $user   = $request->user();
        $tab    = $request->get('tab', 'inbox');
        $withId = (int) $request->get('with', 0);

        // ── PERF-01: Replaced N+1 loop (1+3N queries) with 4 fixed queries ──────

        // Query 1: Get last message ID per direction within each thread.
        // GROUP BY sender+recipient gives at most 2 rows per thread (A→B and B→A).
        // We normalise them to one entry per partner in PHP.
        $rawRows = DB::table('messages')
            ->where(fn ($q) => $q->where('sender_id', $user->id)->orWhere('recipient_id', $user->id))
            ->where('archived', $tab === 'archive')
            ->select(['sender_id', 'recipient_id'])
            ->selectRaw('MAX(id) as last_msg_id')
            ->groupBy('sender_id', 'recipient_id')
            ->get();

        // Normalise: one entry per partner_id, keeping the highest message ID
        $threadMap = [];   // partner_id => last_msg_id
        foreach ($rawRows as $row) {
            $partnerId = $row->sender_id === $user->id ? $row->recipient_id : $row->sender_id;
            if (! isset($threadMap[$partnerId]) || $row->last_msg_id > $threadMap[$partnerId]) {
                $threadMap[$partnerId] = $row->last_msg_id;
            }
        }

        if (! empty($threadMap)) {
            $partnerIds  = array_keys($threadMap);
            $lastMsgIds  = array_values($threadMap);

            // Query 2: Load all partner users in a single batch
            $partners = User::whereIn('id', $partnerIds)->get()->keyBy('id');

            // Query 3: Load all last messages in a single batch (Eloquent for proper casting)
            $lastMessages = Message::whereIn('id', $lastMsgIds)->get()->keyBy('id');

            // Query 4: Unread counts for all partners at once
            $unreadCounts = DB::table('messages')
                ->whereIn('sender_id', $partnerIds)
                ->where('recipient_id', $user->id)
                ->whereNull('read_at')
                ->where('archived', false)
                ->selectRaw('sender_id as partner_id, COUNT(*) as cnt')
                ->groupBy('sender_id')
                ->pluck('cnt', 'partner_id');

            $threads = collect($threadMap)
                ->map(function ($lastMsgId, $partnerId) use ($partners, $lastMessages, $unreadCounts) {
                    $partner = $partners->get($partnerId);
                    $lastMsg = $lastMessages->get($lastMsgId);

                    if (! $partner || ! $lastMsg) {
                        return null;
                    }

                    return [
                        'id'       => $partnerId,
                        'partner'  => $partner,
                        'preview'  => $lastMsg->body,
                        'time'     => $lastMsg->created_at,
                        'unread'   => (int) $unreadCounts->get($partnerId, 0),
                        'archived' => (bool) $lastMsg->archived,
                    ];
                })
                ->filter()
                ->sortByDesc(fn ($t) => $t['time'])
                ->values();
        } else {
            $threads = collect();
        }

        if (! $withId && $threads->isNotEmpty()) {
            $withId = $threads->first()['id'];
        }

        $messages      = collect();
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
