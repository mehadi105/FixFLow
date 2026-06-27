<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warranty extends Model
{
    use HasFactory;

    protected $fillable = [
        'warranty_code',
        'repair_request_id',
        'user_id',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * The repair request this warranty covers.
     */
    public function repairRequest(): BelongsTo
    {
        return $this->belongsTo(RepairRequest::class);
    }

    /**
     * The customer who owns the warranty.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Whether the warranty is still within its coverage period.
     */
    public function isActive(): bool
    {
        return $this->end_date->endOfDay()->isFuture();
    }

    /**
     * Derived status used for badges.
     */
    public function getStatusAttribute(): string
    {
        return $this->isActive() ? 'active' : 'expired';
    }
}
