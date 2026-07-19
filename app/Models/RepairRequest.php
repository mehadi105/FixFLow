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
    public const STATUS_QUOTED = 'quoted';
    public const STATUS_REPAIRING = 'repairing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_DECLINED = 'declined';

    public const DEFAULT_DIAGNOSIS_FEE = 25.00;
    public const DEFAULT_SERVICE_CHARGE = 85.00;

    public const FULFILLMENT_AWAITING_INVOICE = 'awaiting_invoice';
    public const FULFILLMENT_AWAITING_PAYMENT = 'awaiting_payment';
    public const FULFILLMENT_AWAITING_CHOICE = 'awaiting_choice';
    public const FULFILLMENT_READY_FOR_PICKUP = 'ready_for_pickup';
    public const FULFILLMENT_OUT_FOR_DELIVERY = 'out_for_delivery';
    public const FULFILLMENT_FULFILLED = 'fulfilled';

    public const FULFILLMENT_METHOD_PICKUP = 'pickup';
    public const FULFILLMENT_METHOD_DELIVERY = 'delivery';

    /**
     * Ordered list of statuses for filters and the happy-path timeline.
     *
     * @var array<int, string>
     */
    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_ASSIGNED,
        self::STATUS_DIAGNOSING,
        self::STATUS_QUOTED,
        self::STATUS_REPAIRING,
        self::STATUS_COMPLETED,
        self::STATUS_DECLINED,
    ];

    /**
     * Timeline steps for an approved repair (declined branches after quoted).
     *
     * @var array<int, string>
     */
    public const TIMELINE_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_ASSIGNED,
        self::STATUS_DIAGNOSING,
        self::STATUS_QUOTED,
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
        'quote_service_charge',
        'quote_parts_cost',
        'quote_discount',
        'diagnosis_fee',
        'quote_notes',
        'quoted_at',
        'quote_responded_at',
        'priority',
        'status',
        'fulfillment_status',
        'fulfillment_method',
        'delivery_address',
        'image_path',
    ];

    protected function casts(): array
    {
        return [
            'quote_service_charge' => 'decimal:2',
            'quote_parts_cost' => 'decimal:2',
            'quote_discount' => 'decimal:2',
            'diagnosis_fee' => 'decimal:2',
            'quoted_at' => 'datetime',
            'quote_responded_at' => 'datetime',
        ];
    }

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

    public function quoteTotal(): float
    {
        return max(0, (float) $this->quote_service_charge + (float) $this->quote_parts_cost - (float) $this->quote_discount);
    }

    public function hasQuote(): bool
    {
        return $this->quoted_at !== null
            && $this->quote_service_charge !== null
            && $this->quote_parts_cost !== null;
    }

    public function canSubmitQuote(): bool
    {
        return in_array($this->status, [
            self::STATUS_DIAGNOSING,
            self::STATUS_QUOTED,
        ], true) && filled($this->diagnosis_notes);
    }

    public function canRespondToQuote(): bool
    {
        return $this->status === self::STATUS_QUOTED && $this->hasQuote();
    }

    /**
     * Status options workers may set from the status dropdown.
     *
     * @return array<int, string>
     */
    public function availableStatusTransitions(): array
    {
        return match ($this->status) {
            self::STATUS_PENDING => [
                self::STATUS_PENDING,
                self::STATUS_ASSIGNED,
                self::STATUS_DIAGNOSING,
            ],
            self::STATUS_ASSIGNED => [
                self::STATUS_ASSIGNED,
                self::STATUS_DIAGNOSING,
            ],
            self::STATUS_DIAGNOSING => [
                self::STATUS_DIAGNOSING,
            ],
            self::STATUS_QUOTED => [
                self::STATUS_QUOTED,
                self::STATUS_DIAGNOSING,
            ],
            self::STATUS_REPAIRING => [
                self::STATUS_REPAIRING,
                self::STATUS_COMPLETED,
            ],
            self::STATUS_COMPLETED => [
                self::STATUS_COMPLETED,
            ],
            self::STATUS_DECLINED => [
                self::STATUS_DECLINED,
            ],
            default => [$this->status],
        };
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
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_DECLINED], true)
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

    public function isClosedWithoutRepair(): bool
    {
        return $this->status === self::STATUS_DECLINED;
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
