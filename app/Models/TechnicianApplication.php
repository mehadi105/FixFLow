<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TechnicianApplication extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'phone',
        'years_experience',
        'specialties',
        'certification',
        'motivation',
        'document_path',
        'document_original_name',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function hasDocument(): bool
    {
        return filled($this->document_path);
    }

    public function documentLabel(): string
    {
        return $this->document_original_name
            ?: ($this->document_path ? basename($this->document_path) : 'Document');
    }

    public function hasImageDocument(): bool
    {
        if (! $this->document_path) {
            return false;
        }

        return in_array(strtolower(pathinfo($this->document_path, PATHINFO_EXTENSION)), [
            'jpg', 'jpeg', 'png', 'gif', 'webp',
        ], true);
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }
}
