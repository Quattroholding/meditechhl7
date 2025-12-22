<?php

namespace App\Models;

use App\Enums\ReferralStatus;
use App\Enums\ReferrerRewardType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientReferral extends Model
{
    protected $fillable = [
        'referrer_client_id',
        'referred_client_id',
        'referral_code_id',
        'status',
        'referrer_reward_type',
        'referrer_reward_value',
        'referrer_reward_applied',
        'referrer_reward_invoice_id',
        'referred_reward_applied',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReferralStatus::class,
            'referrer_reward_type' => ReferrerRewardType::class,
            'referrer_reward_value' => 'decimal:2',
            'referrer_reward_applied' => 'boolean',
            'referred_reward_applied' => 'boolean',
            'confirmed_at' => 'datetime',
        ];
    }

    // Relationships
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'referrer_client_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'referred_client_id');
    }

    public function code(): BelongsTo
    {
        return $this->belongsTo(ReferralCode::class, 'referral_code_id');
    }

    public function rewardInvoice(): BelongsTo
    {
        return $this->belongsTo(ClientInvoice::class, 'referrer_reward_invoice_id');
    }

    // Scopes
    public function scopePending(Builder $query): void
    {
        $query->where('status', ReferralStatus::PENDING->value);
    }

    public function scopeConfirmed(Builder $query): void
    {
        $query->where('status', ReferralStatus::CONFIRMED->value);
    }

    public function scopeRewarded(Builder $query): void
    {
        $query->where('status', ReferralStatus::REWARDED->value);
    }

    // Methods
    public function confirm(): bool
    {
        if ($this->status === ReferralStatus::PENDING) {
            $this->status = ReferralStatus::CONFIRMED;
            $this->confirmed_at = now();

            return $this->save();
        }

        return false;
    }

    public function applyReferrerReward(): bool
    {
        if ($this->referrer_reward_applied) {
            return false;
        }

        $this->referrer_reward_applied = true;

        return $this->save();
    }

    public function markAsRewarded(): bool
    {
        if ($this->status === ReferralStatus::CONFIRMED && $this->referrer_reward_applied) {
            $this->status = ReferralStatus::REWARDED;

            return $this->save();
        }

        return false;
    }
}
