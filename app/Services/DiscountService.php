<?php

namespace App\Services;

use App\Enums\DiscountSource;
use App\Models\Client;
use App\Models\ClientInvoice;
use App\Models\ClientReferral;
use App\Models\SubscriptionDiscount;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DiscountService
{
    public function create(Client $client, array $data): SubscriptionDiscount
    {
        $discount = new SubscriptionDiscount;
        $discount->client_id = $client->id;
        $discount->subscription_id = $data['subscription_id'] ?? null;
        $discount->discount_type = $data['discount_type'];
        $discount->discount_value = $data['discount_value'];
        $discount->reason = $data['reason'];
        $discount->source = $data['source'];
        $discount->source_id = $data['source_id'] ?? null;
        $discount->applies_to_invoices = $data['applies_to_invoices'] ?? 1;
        $discount->valid_from = $data['valid_from'] ?? null;
        $discount->valid_until = $data['valid_until'] ?? null;
        $discount->is_active = $data['is_active'] ?? true;
        $discount->save();

        Log::info('Discount created', [
            'discount_id' => $discount->id,
            'client_id' => $client->id,
            'type' => $discount->discount_type->value,
            'value' => $discount->discount_value,
        ]);

        return $discount;
    }

    public function applyToInvoice(SubscriptionDiscount $discount, ClientInvoice $invoice): float
    {
        $amount = $discount->apply($invoice);

        if ($amount > 0) {
            Log::info('Discount applied to invoice', [
                'discount_id' => $discount->id,
                'invoice_id' => $invoice->id,
                'amount' => $amount,
            ]);
        }

        return $amount;
    }

    public function getAvailableDiscounts(Client $client): Collection
    {
        return SubscriptionDiscount::where('client_id', $client->id)
            ->available()
            ->get();
    }

    public function createReferralDiscount(Client $client, ClientReferral $referral): SubscriptionDiscount
    {
        return $this->create($client, [
            'discount_type' => $referral->referrer_reward_type->value,
            'discount_value' => $referral->referrer_reward_value,
            'reason' => 'Referral reward',
            'source' => DiscountSource::REFERRAL->value,
            'source_id' => $referral->id,
            'applies_to_invoices' => 1,
        ]);
    }

    public function createPromoDiscount(Client $client, float $value, string $type, int $invoicesCount = 1): SubscriptionDiscount
    {
        return $this->create($client, [
            'discount_type' => $type,
            'discount_value' => $value,
            'reason' => 'Promotional discount',
            'source' => DiscountSource::PROMOTION->value,
            'applies_to_invoices' => $invoicesCount,
        ]);
    }

    public function createCourtesyDiscount(Client $client, float $value, string $type, string $reason): SubscriptionDiscount
    {
        return $this->create($client, [
            'discount_type' => $type,
            'discount_value' => $value,
            'reason' => $reason,
            'source' => DiscountSource::COURTESY->value,
            'applies_to_invoices' => 1,
        ]);
    }
}
