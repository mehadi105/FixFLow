<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RepairRequest extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_DIAGNOSING = 'diagnosing';
    public const STATUS_REPAIRING = 'repairing';
    public const STATUS_COMPLETED = 'completed';

    public const FULFILLMENT_AWAITING_INVOICE = 'awaiting_invoice';
    public const FULFILLMENT_AWAITING_PAYMENT = 'awaiting_payment';
    public const FULFILLMENT_AWAITING_CHOICE = 'awaiting_choice';
    public const FULFILLMENT_READY_FOR_PICKUP = 'ready_for_pickup';
    public const FULFILLMENT_OUT_FOR_DELIVERY = 'out_for_delivery';
    public const FULFILLMENT_FULFILLED = 'fulfilled';

    public const FULFILLMENT_METHOD_PICKUP = 'pickup';
    public const FULFILLMENT_METHOD_DELIVERY = 'delivery';

    /**
     * Ordered list of statuses, used for the timeline and filters.
     *
     * @var array<int, string>
     */
    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_ASSIGNED,
        self::STATUS_DIAGNOSING,
        self::STATUS_REPAIRING,
        self::STATUS_COMPLETED,
    ];

    protected $fillable = [
        'reference',
        'user_id',
        'technician_id',
        'device_type',
        'brand',
        'model',
        'serial_number',
        'issue_description',
        'diagnosis_notes',
        'priority',
        'status',
        'fulfillment_status',
        'fulfillment_method',
        'delivery_address',
        'image_path',
    ];

    /**
     * The customer who submitted the request.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The technician assigned to the request.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    /**
     * The invoice issued for this request (if any).
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * The warranty issued for this request (if any).
     */
    public function warranty(): HasOne
    {
        return $this->hasOne(Warranty::class);
    }

    /**
     * Chat messages on this repair request thread.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Whether the given user may access this request's chat thread.
     */
    public function hasChatParticipant(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCustomer() && $this->user_id === $user->id) {
            return true;
        }

        if ($user->isTechnician() && $this->technician_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Human-friendly device label (e.g. "Apple iPhone 14 Pro").
     */
    public function getDeviceLabelAttribute(): string
    {
        return trim(($this->brand ? $this->brand.' ' : '').($this->model ?? '')) ?: $this->device_type;
    }

    public function fulfillmentLabel(): string
    {
        return match ($this->fulfillment_status) {
            self::FULFILLMENT_AWAITING_INVOICE => 'Awaiting invoice review',
            self::FULFILLMENT_AWAITING_PAYMENT => 'Awaiting payment',
            self::FULFILLMENT_AWAITING_CHOICE => 'Choose pickup or delivery',
            self::FULFILLMENT_READY_FOR_PICKUP => 'Ready for pickup',
            self::FULFILLMENT_OUT_FOR_DELIVERY => 'Out for delivery',
            self::FULFILLMENT_FULFILLED => $this->fulfillment_method === self::FULFILLMENT_METHOD_DELIVERY
                ? 'Delivered'
                : 'Picked up',
            default => 'Not started',
        };
    }

    public function canChooseFulfillment(): bool
    {
        return $this->status === self::STATUS_COMPLETED
            && $this->invoice?->isPaid()
            && $this->fulfillment_status === self::FULFILLMENT_AWAITING_CHOICE;
    }

    public function canCompleteFulfillment(): bool
    {
        return in_array($this->fulfillment_status, [
            self::FULFILLMENT_READY_FOR_PICKUP,
            self::FULFILLMENT_OUT_FOR_DELIVERY,
        ], true);
    }

    /**
     * Primary contact shown in the inbox for the given viewer.
     */
    public function inboxContactFor(User $viewer): ?User
    {
        if ($viewer->isCustomer()) {
            return $this->technician;
        }

        if ($viewer->isTechnician()) {
            return $this->customer;
        }

        return $this->customer;
    }
}
