<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClientSubscription extends BaseModel
{
    protected $fillable = [
        'client_id',
        'package_id',
        'status',
        'trial_ends_at',
        'current_period_starts_at',
        'current_period_ends_at',
        'next_billing_date',
        'cancelled_at',
        'cancellation_reason',
        'paused_at',
        'extra_doctors_count',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => SubscriptionStatus::class,
            'trial_ends_at' => 'datetime',
            'current_period_starts_at' => 'datetime',
            'current_period_ends_at' => 'datetime',
            'next_billing_date' => 'datetime',
            'cancelled_at' => 'datetime',
            'paused_at' => 'datetime',
            'extra_doctors_count' => 'integer',
            'metadata' => 'array',
        ];
    }

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(ClientInvoice::class, 'subscription_id');
    }

    public function currentInvoice(): HasOne
    {
        return $this->hasOne(ClientInvoice::class, 'subscription_id')
            ->whereIn('status', ['pending', 'overdue'])
            ->latest();
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(SubscriptionDiscount::class, 'subscription_id');
    }

    public function activeDiscounts(): HasMany
    {
        return $this->hasMany(SubscriptionDiscount::class, 'subscription_id')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            });
    }

    // Scopes
    public function scopeActive(Builder $query): void
    {
        $query->where('status', SubscriptionStatus::ACTIVE->value);
    }

    public function scopeTrial(Builder $query): void
    {
        $query->where('status', SubscriptionStatus::TRIAL->value);
    }

    public function scopePastDue(Builder $query): void
    {
        $query->where('status', SubscriptionStatus::PAST_DUE->value);
    }

    public function scopeByStatus(Builder $query, SubscriptionStatus $status): void
    {
        $query->where('status', $status->value);
    }

    public function scopeNeedsBilling(Builder $query): void
    {
        $query->whereIn('status', [SubscriptionStatus::ACTIVE->value, SubscriptionStatus::TRIAL->value])
            ->whereNotNull('next_billing_date')
            ->where('next_billing_date', '<=', now());
    }

    // Accessors
    protected function isOnTrial(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === SubscriptionStatus::TRIAL
                && $this->trial_ends_at
                && $this->trial_ends_at->isFuture()
        );
    }

    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn () => in_array($this->status, [
                SubscriptionStatus::ACTIVE,
                SubscriptionStatus::TRIAL,
            ])
        );
    }

    protected function daysUntilRenewal(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->next_billing_date) {
                    return null;
                }

                return now()->diffInDays($this->next_billing_date, false);
            }
        );
    }

    protected function isCancelled(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === SubscriptionStatus::CANCELLED
                || $this->cancelled_at !== null
        );
    }

    // Methods
    public function activate(): bool
    {
        $this->status = SubscriptionStatus::ACTIVE;
        $this->current_period_starts_at = now();
        $this->current_period_ends_at = $this->calculatePeriodEnd();
        $this->next_billing_date = $this->current_period_ends_at;

        return $this->save();
    }

    public function cancel(string $reason, bool $immediately = false): bool
    {
        $this->cancelled_at = now();
        $this->cancellation_reason = $reason;

        if ($immediately) {
            $this->status = SubscriptionStatus::CANCELLED;
            $this->next_billing_date = null;
        }

        return $this->save();
    }

    public function suspend(): bool
    {
        $this->status = SubscriptionStatus::SUSPENDED;

        return $this->save();
    }

    public function resume(): bool
    {
        if ($this->status === SubscriptionStatus::SUSPENDED) {
            $this->status = SubscriptionStatus::ACTIVE;
            $this->paused_at = null;

            return $this->save();
        }

        return false;
    }

    public function isInGracePeriod(): bool
    {
        if (! $this->current_period_ends_at) {
            return false;
        }

        $gracePeriodDays = config('subscriptions.grace_period_days', 7);
        $gracePeriodEnd = $this->current_period_ends_at->addDays($gracePeriodDays);

        return now()->lessThanOrEqualTo($gracePeriodEnd);
    }

    public function shouldGenerateInvoice(): bool
    {
        return in_array($this->status, [
            SubscriptionStatus::ACTIVE,
            SubscriptionStatus::TRIAL,
        ])
            && $this->next_billing_date
            && $this->next_billing_date->isPast();
    }

    public function extendTrial(int $days): bool
    {
        if ($this->status !== SubscriptionStatus::TRIAL) {
            return false;
        }

        $currentTrialEnd = $this->trial_ends_at ?? now();
        $this->trial_ends_at = $currentTrialEnd->addDays($days);

        return $this->save();
    }

    public function calculateCurrentPrice(): float
    {
        return $this->package->calculatePrice($this->extra_doctors_count);
    }

    protected function calculatePeriodEnd(): Carbon
    {
        $start = $this->current_period_starts_at ?? now();
        $days = $this->package->billing_period_days;

        return $start->copy()->addDays($days);
    }

    public function updateBillingPeriod(): void
    {
        $this->current_period_starts_at = $this->current_period_ends_at ?? now();
        $this->current_period_ends_at = $this->calculatePeriodEnd();
        $this->next_billing_date = $this->current_period_ends_at;
        $this->save();
    }
}
