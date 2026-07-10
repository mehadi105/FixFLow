<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_PAID = 'paid';

    protected $fillable = [
        'invoice_number',
        'repair_request_id',
        'user_id',
        'service_charge',
        'parts_cost',
        'discount',
        'total',
        'payment_status',
        'payment_method',
        'stripe_checkout_session_id',
        'stripe_payment_intent_id',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'service_charge' => 'decimal:2',
            'parts_cost' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * The repair request this invoice belongs to.
     */
    public function repairRequest(): BelongsTo
    {
        return $this->belongsTo(RepairRequest::class);
    }

    /**
     * The customer being billed.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Subtotal before discount (service + parts).
     */
    public function getSubtotalAttribute(): float
    {
        return (float) $this->service_charge + (float) $this->parts_cost;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::STATUS_PAID;
    }

    public function isDraft(): bool
    {
        return $this->payment_status === self::STATUS_DRAFT;
    }

    /**
     * Customer may pay only after admin sends the invoice.
     */
    public function isPayable(): bool
    {
        return $this->payment_status === self::STATUS_UNPAID;
    }
}
