<?php

namespace App\Models;

use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ReferralCode extends BaseModel
{
    protected $fillable = [
        'client_id',
        'code',
        'discount_type',
        'discount_value',
        'max_uses',
        'times_used',
        'is_active',
        'valid_from',
        'valid_until',
    ];

    protected function casts(): array
    {
        return [
            'discount_type' => DiscountType::class,
            'discount_value' => 'decimal:2',
            'max_uses' => 'integer',
            'times_used' => 'integer',
            'is_active' => 'boolean',
            'valid_from' => 'date',
            'valid_until' => 'date',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($code) {
            if (! $code->code) {
                $code->code = static::generateUniqueCode();
            }
        });
    }

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(ClientReferral::class);
    }

    public function activeReferrals(): HasMany
    {
        return $this->hasMany(ClientReferral::class)
            ->whereIn('status', ['confirmed', 'rewarded']);
    }

    // Scopes
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeValid(Builder $query): void
    {
        $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            });
    }

    public function scopeAvailable(Builder $query): void
    {
        $query->valid()
            ->where(function ($q) {
                $q->whereNull('max_uses')
                    ->orWhereRaw('times_used < max_uses');
            });
    }

    // Methods
    public function isValid(): bool
    {
        if (! $this->is_active) {
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

    public function canBeUsed(): bool
    {
        if (! $this->isValid()) {
            return false;
        }

        if ($this->max_uses && $this->times_used >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function incrementUsage(): bool
    {
        $this->increment('times_used');

        if ($this->max_uses && $this->times_used >= $this->max_uses) {
            $this->is_active = false;
            $this->save();
        }

        return true;
    }

    public static function generateUniqueCode(): string
    {
        $prefix = config('subscriptions.referral_code_prefix', 'REF');

        do {
            $code = $prefix.'-'.strtoupper(Str::random(8));
        } while (static::where('code', $code)->exists());

        return $code;
    }
}
