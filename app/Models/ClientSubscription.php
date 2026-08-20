<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClientSubscription extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'package_id',
        'payment_token_id',
        'status',
        'trial_ends_at',
        'current_period_starts_at',
        'current_period_ends_at',
        'next_billing_date',
        'cancelled_at',
        'cancellation_reason',
        'paused_at',
        'suspended_at',
        'grace_period_ends_at',
        'last_billed_at',
        'next_retry_at',
        'retry_count',
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
            'suspended_at' => 'datetime',
            'grace_period_ends_at' => 'datetime',
            'last_billed_at' => 'datetime',
            'next_retry_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
            'extra_doctors_count' => 'integer',
            'retry_count' => 'integer',
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

    public function paymentToken()
    {
        return $this->belongsTo(PaymentToken::class);
    }

    public function billingCycles()
    {
        return $this->hasMany(SubscriptionBillingCycle::class, 'subscription_id');
    }

    public function latestInvoice()
    {
        return $this->hasOne(ClientInvoice::class)
            ->latestOfMany();
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
            ->whereDate('next_billing_date', '<=', today());
    }

    /**
     * Scope para suscripciones activas o en periodo de gracia
     * Incluye: active, trial, y past_due (durante periodo de gracia de 7 días)
     * El comando processOverdue() automáticamente cambia past_due a suspended después de 7 días
     */
    public function scopeActiveOrInGracePeriod(Builder $query): void
    {
        $query->whereIn('status', [
            SubscriptionStatus::ACTIVE->value,
            SubscriptionStatus::TRIAL->value,
            SubscriptionStatus::PAST_DUE->value,
        ]);
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

                return (int) now()->diffInDays($this->next_billing_date, false);
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

        // Si es una reactivación y no una activación inicial
        $metadata = $this->metadata ?? [];
        if (isset($metadata['frozen_next_billing_date'])) {
            // Restaurar desde metadata y recalcular
            if ($this->current_period_ends_at) {
                $this->next_billing_date = $this->current_period_ends_at;
            } else {
                $this->current_period_starts_at = now();
                $this->current_period_ends_at = $this->calculatePeriodEnd();
                $this->next_billing_date = $this->current_period_ends_at;
            }

            unset($metadata['frozen_next_billing_date']);
            $this->metadata = $metadata;
        } else {
            // Activación inicial
            $this->current_period_starts_at = now();
            $this->current_period_ends_at = $this->calculatePeriodEnd();
            $this->next_billing_date = $this->current_period_ends_at;
        }

        // Limpiar retry count y grace period
        $this->retry_count = 0;
        $this->next_retry_at = null;
        $this->grace_period_ends_at = null;

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
        $this->suspended_at = now();

        // Congelar next_billing_date para que no se generen más facturas
        if ($this->next_billing_date) {
            $metadata = $this->metadata ?? [];
            $metadata['frozen_next_billing_date'] = $this->next_billing_date->toDateTimeString();
            $this->metadata = $metadata;
            $this->next_billing_date = null;
        }

        return $this->save();
    }

    public function resume(): bool
    {
        if ($this->status === SubscriptionStatus::SUSPENDED) {
            $this->status = SubscriptionStatus::ACTIVE;
            $this->paused_at = null;
            $this->suspended_at = null;

            // Restaurar y recalcular next_billing_date
            $metadata = $this->metadata ?? [];
            if (isset($metadata['frozen_next_billing_date'])) {
                // Recalcular desde el período actual
                if ($this->current_period_ends_at) {
                    $this->next_billing_date = $this->current_period_ends_at;
                } else {
                    // Si no hay período actual, empezar uno nuevo
                    $this->current_period_starts_at = now();
                    $this->current_period_ends_at = $this->calculatePeriodEnd();
                    $this->next_billing_date = $this->current_period_ends_at;
                }

                // Limpiar metadata
                unset($metadata['frozen_next_billing_date']);
                $this->metadata = $metadata;
            }

            return $this->save();
        }

        return false;
    }

    public function isInGracePeriod(): bool
    {
        if (! $this->current_period_ends_at) {
            return false;
        }

        $gracePeriodDays = (int) config('subscriptions.grace_period_days', 7);
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

    public function calculatePeriodEnd(): Carbon
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
