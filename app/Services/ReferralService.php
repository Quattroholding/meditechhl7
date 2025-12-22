<?php

namespace App\Services;

use App\Enums\DiscountSource;
use App\Enums\ReferralStatus;
use App\Models\Client;
use App\Models\ClientReferral;
use App\Models\ReferralCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReferralService
{
    public function __construct(
        protected DiscountService $discountService
    ) {}

    public function generateCode(Client $client, array $config = []): ReferralCode
    {
        $code = new ReferralCode;
        $code->client_id = $client->id;
        $code->discount_type = $config['discount_type'] ?? config('subscriptions.default_referred_benefit.type', 'percentage_discount');
        $code->discount_value = $config['discount_value'] ?? config('subscriptions.default_referred_benefit.value', 10);
        $code->max_uses = $config['max_uses'] ?? null;
        $code->valid_from = $config['valid_from'] ?? null;
        $code->valid_until = $config['valid_until'] ?? null;
        $code->is_active = $config['is_active'] ?? true;
        $code->save();

        Log::info('Referral code generated', [
            'client_id' => $client->id,
            'code' => $code->code,
        ]);

        return $code;
    }

    public function applyReferralCode(Client $newClient, string $codeString): ?ClientReferral
    {
        return DB::transaction(function () use ($newClient, $codeString) {
            $code = ReferralCode::where('code', $codeString)
                ->available()
                ->first();

            if (! $code) {
                Log::warning('Invalid referral code used', [
                    'client_id' => $newClient->id,
                    'code' => $codeString,
                ]);

                return null;
            }

            if ($code->client_id === $newClient->id) {
                Log::warning('Client tried to use own referral code', [
                    'client_id' => $newClient->id,
                    'code' => $codeString,
                ]);

                return null;
            }

            $referral = new ClientReferral;
            $referral->referrer_client_id = $code->client_id;
            $referral->referred_client_id = $newClient->id;
            $referral->referral_code_id = $code->id;
            $referral->status = ReferralStatus::PENDING;

            $rewardConfig = config('subscriptions.default_referrer_reward', []);
            $referral->referrer_reward_type = $rewardConfig['type'] ?? 'percentage_discount';
            $referral->referrer_reward_value = $rewardConfig['value'] ?? 10;

            $referral->save();

            $newClient->referral_code_used = $codeString;
            $newClient->referred_by_client_id = $code->client_id;
            $newClient->save();

            $code->incrementUsage();

            $this->discountService->create($newClient, [
                'discount_type' => $code->discount_type->value,
                'discount_value' => $code->discount_value,
                'reason' => "Referral code: {$codeString}",
                'source' => DiscountSource::REFERRAL->value,
                'source_id' => $referral->id,
                'applies_to_invoices' => 1,
            ]);

            Log::info('Referral code applied', [
                'referral_id' => $referral->id,
                'referrer_id' => $code->client_id,
                'referred_id' => $newClient->id,
                'code' => $codeString,
            ]);

            return $referral;
        });
    }

    public function confirmReferral(ClientReferral $referral): bool
    {
        if ($referral->status !== ReferralStatus::PENDING) {
            return false;
        }

        return DB::transaction(function () use ($referral) {
            $confirmed = $referral->confirm();

            if ($confirmed) {
                $this->applyReferrerReward($referral);

                Log::info('Referral confirmed', ['referral_id' => $referral->id]);
            }

            return $confirmed;
        });
    }

    public function applyReferrerReward(ClientReferral $referral): void
    {
        if ($referral->referrer_reward_applied) {
            return;
        }

        $referrerClient = $referral->referrer;

        $invoicesCount = config('subscriptions.default_referrer_reward.invoices', 1);

        $this->discountService->create($referrerClient, [
            'discount_type' => $referral->referrer_reward_type->value,
            'discount_value' => $referral->referrer_reward_value,
            'reason' => "Referral reward for referring client #{$referral->referred_client_id}",
            'source' => DiscountSource::REFERRAL->value,
            'source_id' => $referral->id,
            'applies_to_invoices' => $invoicesCount,
        ]);

        $referral->applyReferrerReward();

        Log::info('Referrer reward applied', [
            'referral_id' => $referral->id,
            'referrer_id' => $referral->referrer_client_id,
        ]);
    }

    public function getClientStats(Client $client): array
    {
        $code = $client->referralCode ?? $this->generateCode($client);

        $referrals = ClientReferral::where('referrer_client_id', $client->id)->get();

        return [
            'code' => $code->code,
            'total_referrals' => $referrals->count(),
            'pending_referrals' => $referrals->where('status', ReferralStatus::PENDING)->count(),
            'confirmed_referrals' => $referrals->where('status', ReferralStatus::CONFIRMED)->count(),
            'rewarded_referrals' => $referrals->where('status', ReferralStatus::REWARDED)->count(),
            'total_rewards_earned' => $referrals->where('referrer_reward_applied', true)->sum('referrer_reward_value'),
        ];
    }

    public function validateCode(string $code): bool
    {
        $referralCode = ReferralCode::where('code', $code)->first();

        return $referralCode && $referralCode->canBeUsed();
    }

    public function deactivateCode(ReferralCode $code): bool
    {
        $code->is_active = false;
        $saved = $code->save();

        if ($saved) {
            Log::info('Referral code deactivated', ['code_id' => $code->id]);
        }

        return $saved;
    }
}
