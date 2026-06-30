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
