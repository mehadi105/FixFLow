<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'role', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_CUSTOMER = 'customer';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_TECHNICIAN = 'technician';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Repair requests submitted by this user (as a customer).
     */
    public function repairRequests(): HasMany
    {
        return $this->hasMany(RepairRequest::class);
    }

    /**
     * Repair requests assigned to this user (as a technician).
     */
    public function assignedRepairRequests(): HasMany
    {
        return $this->hasMany(RepairRequest::class, 'technician_id');
    }

    /**
     * Chat messages sent by this user.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Technician job application (technicians only).
     */
    public function technicianApplication(): HasOne
    {
        return $this->hasOne(TechnicianApplication::class);
    }

    /**
     * Technicians approved by admin and available for assignment.
     */
    public static function approvedTechnicians()
    {
        return static::query()
            ->where('role', self::ROLE_TECHNICIAN)
            ->whereHas('technicianApplication', fn ($q) => $q->where('status', TechnicianApplication::STATUS_APPROVED));
    }

    public function isApprovedTechnician(): bool
    {
        if (! $this->isTechnician()) {
            return false;
        }

        return $this->technicianApplication?->isApproved() ?? false;
    }

    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isTechnician(): bool
    {
        return $this->role === self::ROLE_TECHNICIAN;
    }

    /**
     * The named dashboard route for this user's role.
     */
    public function dashboardRoute(): string
    {
        if ($this->isTechnician() && ! $this->isApprovedTechnician()) {
            return 'technician.application.status';
        }

        return match ($this->role) {
            self::ROLE_ADMIN => 'dashboard.admin',
            self::ROLE_TECHNICIAN => 'dashboard.technician',
            default => 'dashboard.customer',
        };
    }
}
