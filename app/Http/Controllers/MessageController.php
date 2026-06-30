<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\RepairRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * List messages for a repair request thread (JSON for polling / API).
     */
    public function index(Request $request, RepairRequest $repairRequest): JsonResponse
    {
        $this->authorize('participateInChat', $repairRequest);

        $messages = $repairRequest->messages()
            ->with('sender')
            ->oldest()
            ->get()
            ->map(fn (Message $message) => $message->toChatArray($request->user()->id));

        return response()->json(['messages' => $messages]);
    }

    /**
     * Post a new message to the thread.
     */
    public function store(Request $request, RepairRequest $repairRequest): RedirectResponse|JsonResponse
    {
        $this->authorize('participateInChat', $repairRequest);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = $repairRequest->messages()->create([
            'user_id' => $request->user()->id,
            'body' => trim($validated['body']),
        ]);

        $message->load('sender');
        $payload = $message->toChatArray($request->user()->id);

        broadcast(new MessageSent($payload, $repairRequest->id))->toOthers();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $payload,
            ], 201);
        }

        return back()->with('status', 'Message sent.');
    }

    /**
     * Mark messages from other participants as read.
     */
    public function markRead(Request $request, RepairRequest $repairRequest): JsonResponse
    {
        $this->authorize('participateInChat', $repairRequest);

        $repairRequest->messages()
            ->where('user_id', '!=', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }
}
