<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMessageRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    /**
     * Display the list of conversations for the authenticated user.
     */
    public function index(Request $request): View
    {
        $authId = $request->user()->id;

        $partnerIds = Message::query()
            ->where('sender_id', $authId)
            ->orWhere('recipient_id', $authId)
            ->get(['sender_id', 'recipient_id'])
            ->map(fn (Message $message) => $message->sender_id === $authId ? $message->recipient_id : $message->sender_id)
            ->unique();

        $conversations = User::query()
            ->whereIn('id', $partnerIds)
            ->get()
            ->map(function (User $partner) use ($authId) {
                $lastMessage = Message::query()
                    ->where(function ($query) use ($authId, $partner) {
                        $query->where('sender_id', $authId)->where('recipient_id', $partner->id);
                    })
                    ->orWhere(function ($query) use ($authId, $partner) {
                        $query->where('sender_id', $partner->id)->where('recipient_id', $authId);
                    })
                    ->latest()
                    ->first();

                $unreadCount = Message::query()
                    ->where('sender_id', $partner->id)
                    ->where('recipient_id', $authId)
                    ->whereNull('read_at')
                    ->count();

                return (object) [
                    'partner' => $partner,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                ];
            })
            ->sortByDesc(fn ($conversation) => $conversation->last_message?->created_at)
            ->values();

        return view('messages.index', [
            'conversations' => $conversations,
        ]);
    }

    /**
     * Display the message thread with the given user.
     */
    public function show(Request $request, User $user): View
    {
        $authId = $request->user()->id;

        abort_if($authId === $user->id, 404);

        $messages = Message::query()
            ->where(function ($query) use ($authId, $user) {
                $query->where('sender_id', $authId)->where('recipient_id', $user->id);
            })
            ->orWhere(function ($query) use ($authId, $user) {
                $query->where('sender_id', $user->id)->where('recipient_id', $authId);
            })
            ->oldest()
            ->get();

        Message::query()
            ->where('sender_id', $user->id)
            ->where('recipient_id', $authId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('messages.show', [
            'partner' => $user,
            'messages' => $messages,
        ]);
    }

    /**
     * Send a new message to the given user.
     */
    public function store(StoreMessageRequest $request, User $user): RedirectResponse
    {
        abort_if($request->user()->id === $user->id, 404);

        Message::query()->create([
            'sender_id' => $request->user()->id,
            'recipient_id' => $user->id,
            'body' => $request->validated('body'),
        ]);

        return back();
    }

    /**
     * Start a new conversation by looking up the recipient by email.
     */
    public function send(SendMessageRequest $request): RedirectResponse
    {
        $recipient = User::query()->where('email', $request->validated('to'))->firstOrFail();

        Message::query()->create([
            'sender_id' => $request->user()->id,
            'recipient_id' => $recipient->id,
            'body' => $request->validated('body'),
        ]);

        return redirect()->route('messages.show', $recipient);
    }
}
