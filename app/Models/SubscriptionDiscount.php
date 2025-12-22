<?php

namespace App\Models;

use App\Enums\DiscountSource;
use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionDiscount extends Model
{
    protected $fillable = [
        'client_id',
        'subscription_id',
        'discount_type',
        'discount_value',
        'reason',
        'source',
        'source_id',
        'applies_to_invoices',
        'invoices_applied',
        'is_active',
        'valid_from',
        'valid_until',
    ];

    protected function casts(): array
    {
        return [
            'discount_type' => DiscountType::class,
            'discount_value' => 'decimal:2',
            'source' => DiscountSource::class,
            'applies_to_invoices' => 'integer',
            'invoices_applied' => 'integer',
            'is_active' => 'boolean',
            'valid_from' => 'date',
            'valid_until' => 'date',
        ];
    }

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(ClientSubscription::class, 'subscription_id');
    }

    // Scopes
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeAvailable(Builder $query): void
    {
        $query->where('is_active', true)
            ->whereRaw('invoices_applied < applies_to_invoices')
            ->where(function ($q) {
                $q->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            });
    }

    public function scopeBySource(Builder $query, DiscountSource $source): void
    {
        $query->where('source', $source->value);
    }

    // Methods
    public function apply(ClientInvoice $invoice): float
    {
        if (! $this->isAvailable()) {
            return 0;
        }

        $discountAmount = 0;

        if ($this->discount_type === DiscountType::PERCENTAGE) {
            $discountAmount = ($invoice->subtotal * $this->discount_value) / 100;
        } elseif ($this->discount_type === DiscountType::FIXED_AMOUNT) {
            $discountAmount = (float) $this->discount_value;
        }

        $discountAmount = min($discountAmount, $invoice->subtotal);

        $this->markAsUsed();

        return $discountAmount;
    }

    public function isAvailable(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->invoices_applied >= $this->applies_to_invoices) {
            return false;
        }

        if ($this->valid_from && $this->valid_from->isFuture()) {
            return false;
        }

        if ($this->valid_until && $this->valid_until->isPast()) {
            return false;
        }

        return true;
    }

    public function markAsUsed(): void
    {
        $this->increment('invoices_applied');

        if ($this->invoices_applied >= $this->applies_to_invoices) {
            $this->is_active = false;
            $this->save();
        }
    }

    public function calculateDiscountAmount(float $subtotal): float
    {
        if ($this->discount_type === DiscountType::PERCENTAGE) {
            return ($subtotal * $this->discount_value) / 100;
        }

        if ($this->discount_type === DiscountType::FIXED_AMOUNT) {
            return min((float) $this->discount_value, $subtotal);
        }

        return 0;
    }
}
