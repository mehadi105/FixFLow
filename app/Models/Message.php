<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Message extends Model
{
    protected $fillable = [
        'repair_request_id',
        'user_id',
        'body',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    /**
     * The repair request this message belongs to.
     */
    public function repairRequest(): BelongsTo
    {
        return $this->belongsTo(RepairRequest::class);
    }

    /**
     * The user who sent this message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Whether the message has been read by the recipient.
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Total unread messages for the current user across their repair threads.
     */
    public static function unreadCountForUser(User $user): int
    {
        return static::unreadQueryForUser($user)->count();
    }

    /**
     * Unread counts keyed by repair_request_id for a set of requests.
     *
     * @param  array<int>|Collection<int, int>  $repairRequestIds
     * @return Collection<int, int>
     */
    public static function unreadCountsByRepairRequestForUser(User $user, array|Collection $repairRequestIds): Collection
    {
        $ids = collect($repairRequestIds)->filter()->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        return static::unreadQueryForUser($user)
            ->whereIn('repair_request_id', $ids)
            ->groupBy('repair_request_id')
            ->selectRaw('repair_request_id, count(*) as unread_count')
            ->pluck('unread_count', 'repair_request_id');
    }

    /**
     * Recent unread chat threads for the notification panel.
     *
     * @return Collection<int, array{repair_request: RepairRequest, latest_message: Message, unread_count: int}>
     */
    public static function notificationThreadsForUser(User $user, int $limit = 8): Collection
    {
        return static::unreadQueryForUser($user)
            ->with(['sender', 'repairRequest'])
            ->latest()
            ->get()
            ->groupBy('repair_request_id')
            ->take($limit)
            ->map(function (Collection $messages) {
                $latest = $messages->first();

                return [
                    'repair_request' => $latest->repairRequest,
                    'latest_message' => $latest,
                    'unread_count' => $messages->count(),
                ];
            })
            ->values();
    }

    /**
     * Inbox conversation list for the messages page (Fiverr-style).
     *
     * @return Collection<int, array{repair: RepairRequest, last_message: ?Message, unread_count: int, contact_name: string, contact_initials: string, sort_at: \Illuminate\Support\Carbon}>
     */
    public static function conversationThreadsForUser(User $user): Collection
    {
        $query = RepairRequest::query()
            ->with(['customer', 'technician']);

        if ($user->isCustomer()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isTechnician()) {
            $query->where('technician_id', $user->id);
        }

        $repairs = $query
            ->where(function ($q) {
                $q->whereHas('messages')
                    ->orWhereNotNull('technician_id');
            })
            ->get()
            ->filter(fn (RepairRequest $repair) => $repair->hasChatParticipant($user));

        $unreadCounts = static::unreadCountsByRepairRequestForUser(
            $user,
            $repairs->pluck('id')
        );

        return $repairs
            ->map(function (RepairRequest $repair) use ($user, $unreadCounts) {
                $lastMessage = $repair->messages()->with('sender')->latest()->first();
                $contact = $repair->inboxContactFor($user);

                return [
                    'repair' => $repair,
                    'last_message' => $lastMessage,
                    'unread_count' => (int) ($unreadCounts[$repair->id] ?? 0),
                    'contact_name' => $contact?->name ?? 'Awaiting assignment',
                    'contact_initials' => $contact
                        ? strtoupper(substr($contact->name, 0, 2))
                        : 'FF',
                    'sort_at' => $lastMessage?->created_at ?? $repair->updated_at,
                ];
            })
            ->sortByDesc('sort_at')
            ->values();
    }

    /**
     * Messages the user has not read from other participants.
     */
    protected static function unreadQueryForUser(User $user)
    {
        $query = static::query()
            ->where('user_id', '!=', $user->id)
            ->whereNull('read_at');

        if ($user->isCustomer()) {
            $query->whereHas('repairRequest', fn ($q) => $q->where('user_id', $user->id));
        } elseif ($user->isTechnician()) {
            $query->whereHas('repairRequest', fn ($q) => $q->where('technician_id', $user->id));
        }

        return $query;
    }

    /**
     * Shape this message for chat JSON / broadcast payloads.
     *
     * @return array<string, mixed>
     */
    public function toChatArray(int $viewerId): array
    {
        $this->loadMissing('sender');

        return [
            'id' => $this->id,
            'body' => $this->body,
            'user_id' => $this->user_id,
            'is_mine' => $this->user_id === $viewerId,
            'sender' => [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
                'role' => $this->sender->role,
                'initials' => strtoupper(substr($this->sender->name, 0, 2)),
            ],
            'created_at' => $this->created_at->toIso8601String(),
            'created_at_human' => $this->created_at->format('M d, Y g:i A'),
        ];
    }
}
