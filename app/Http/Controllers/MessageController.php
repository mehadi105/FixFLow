<?php

namespace App\Http\Controllers;

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
            ->map(fn (Message $message) => $this->formatMessage($message, $request->user()->id));

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

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->formatMessage($message, $request->user()->id),
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

    /**
     * Shape a message for JSON responses and future realtime events.
     *
     * @return array<string, mixed>
     */
    private function formatMessage(Message $message, int $currentUserId): array
    {
        return [
            'id' => $message->id,
            'body' => $message->body,
            'is_mine' => $message->user_id === $currentUserId,
            'sender' => [
                'id' => $message->sender->id,
                'name' => $message->sender->name,
                'role' => $message->sender->role,
                'initials' => strtoupper(substr($message->sender->name, 0, 2)),
            ],
            'created_at' => $message->created_at->toIso8601String(),
            'created_at_human' => $message->created_at->format('M d, Y g:i A'),
        ];
    }
}
