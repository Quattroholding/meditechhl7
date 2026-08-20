<?php

namespace App\Services;

use App\Enums\SubscriptionStatus;
use App\Models\Client;
use App\Models\ClientInvoice;
use App\Models\ClientSubscription;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    public function __construct(
        protected ClientInvoiceService $invoiceService,
        protected ReferralService $referralService,
        protected DiscountService $discountService
    ) {}

    public function create(Client $client, Package $package, array $options = []): ClientSubscription
    {
        return DB::transaction(function () use ($client, $package, $options) {
            $trialDays = (int) ($options['trial_days'] ?? config('subscriptions.default_trial_days', 0));
            $freeMonths = (int) ($options['free_months'] ?? 0);
            $extraDoctors = $options['extra_doctors'] ?? 0;
            $billingDay = $options['billing_day'] ?? null;

            $subscription = new ClientSubscription;
            $subscription->client_id = $client->id;
            $subscription->package_id = $package->id;
            $subscription->extra_doctors_count = $extraDoctors;
            $subscription->metadata = $options['metadata'] ?? [];

            if ($billingDay) {
                $client->subscription_billing_day = min(28, max(1, $billingDay));
                $client->save();
            }

            if ($trialDays > 0 || $freeMonths > 0) {
                $subscription->status = SubscriptionStatus::TRIAL;
                $trialEnd = now()->addDays($trialDays)->addMonths($freeMonths);
                $subscription->trial_ends_at = $trialEnd;
                $subscription->next_billing_date = $trialEnd;
            } else {
                $subscription->status = SubscriptionStatus::PENDING_ACTIVATION;
                $subscription->next_billing_date = now();
            }

            $subscription->save();

            if (isset($options['referral_code'])) {
                $this->referralService->applyReferralCode($client, $options['referral_code']);
            }

            if ($subscription->status === SubscriptionStatus::PENDING_ACTIVATION) {
                $this->invoiceService->generate($subscription);
            }

            Log::info("Subscription created for client {$client->id}", [
                'subscription_id' => $subscription->id,
                'package_id' => $package->id,
            ]);

            return $subscription->fresh();
        });
    }

    public function activate(ClientSubscription $subscription): bool
    {
        if (! in_array($subscription->status, [SubscriptionStatus::PENDING_ACTIVATION, SubscriptionStatus::SUSPENDED])) {
            return false;
        }

        $activated = $subscription->activate();

        if ($activated) {
            Log::info('Subscription activated', ['subscription_id' => $subscription->id]);
        }

        return $activated;
    }

    public function cancel(ClientSubscription $subscription, string $reason, bool $immediate = false): bool
    {
        $cancelled = $subscription->cancel($reason, $immediate);

        if ($cancelled) {
            Log::info('Subscription cancelled', [
                'subscription_id' => $subscription->id,
                'reason' => $reason,
                'immediate' => $immediate,
            ]);
        }

        return $cancelled;
    }

    public function suspend(ClientSubscription $subscription): bool
    {
        $suspended = $subscription->suspend();

        if ($suspended) {
            Log::info('Subscription suspended', ['subscription_id' => $subscription->id]);
        }

        return $suspended;
    }

    public function reactivate(ClientSubscription $subscription): ?ClientInvoice
    {
        return DB::transaction(function () use ($subscription) {
            // Determine the suspension date: use suspended_at if available, otherwise updated_at
            $graceDaysForOldInvoices = config('subscriptions.grace_days_for_old_invoices', 30);
            $suspensionDate = $subscription->suspended_at ?? $subscription->updated_at;

            // Convert to Carbon if needed and calculate days since suspension
            if (! ($suspensionDate instanceof Carbon)) {
                $suspensionDate = Carbon::parse($suspensionDate);
            }
            $daysSinceSuspension = $suspensionDate->diffInDays(now());
            $shouldCancelOldInvoices = $daysSinceSuspension > $graceDaysForOldInvoices;

            // Cancel old unpaid invoices if subscription was suspended for too long
            if ($shouldCancelOldInvoices) {
                $oldInvoices = $subscription->invoices()
                    ->whereIn('status', ['pending', 'overdue'])
                    ->where('due_date', '<', now()->subDays($graceDaysForOldInvoices))
                    ->get();

                foreach ($oldInvoices as $invoice) {
                    $invoice->cancel('Factura cancelada automáticamente por reactivación tardía. El cliente no usó el servicio durante el período de suspensión.');
                    Log::info('Old invoice cancelled during reactivation', [
                        'subscription_id' => $subscription->id,
                        'invoice_id' => $invoice->id,
                        'invoice_due_date' => $invoice->due_date,
                    ]);
                }
            }

            // Reset billing period for new subscription cycle
            // When reactivating after suspension, start fresh from today
            $subscription->status = SubscriptionStatus::PENDING_ACTIVATION;
            $subscription->paused_at = null;
            $subscription->suspended_at = null;
            $subscription->current_period_starts_at = now();
            $subscription->current_period_ends_at = $subscription->calculatePeriodEnd();
            $subscription->next_billing_date = $subscription->current_period_ends_at;

            // Clear frozen dates from metadata
            $metadata = $subscription->metadata ?? [];
            unset($metadata['frozen_next_billing_date']);
            $subscription->metadata = $metadata;
            $subscription->save();

            // Generate new invoice for current period starting from today
            // The subscription will be activated once the invoice is paid (in ClientInvoiceService::markAsPaidIfComplete)
            $newInvoice = $this->invoiceService->generate($subscription);

            Log::info('Subscription reactivated', [
                'subscription_id' => $subscription->id,
                'cancelled_old_invoices' => $shouldCancelOldInvoices,
                'new_invoice_id' => $newInvoice?->id,
            ]);

            return $newInvoice;
        });
    }

    public function changePlan(ClientSubscription $subscription, Package $newPackage, bool $prorate = true): ClientSubscription
    {
        return DB::transaction(function () use ($subscription, $newPackage, $prorate) {
            $oldPackage = $subscription->package;

            $subscription->package_id = $newPackage->id;

            if ($prorate && $subscription->current_period_ends_at) {
                $daysRemaining = now()->diffInDays($subscription->current_period_ends_at, false);

                if ($daysRemaining > 0) {
                    $oldDailyRate = $oldPackage->base_price / $oldPackage->billing_period_days;
                    $newDailyRate = $newPackage->base_price / $newPackage->billing_period_days;

                    $creditAmount = $oldDailyRate * $daysRemaining;
                    $chargeAmount = $newDailyRate * $daysRemaining;
                    $difference = $chargeAmount - $creditAmount;

                    // Store proration adjustment in subscription metadata for next invoice
                    $metadata = $subscription->metadata ?? [];
                    $metadata['pending_proration'] = [
                        'amount' => round($difference, 2),
                        'description' => $difference > 0
                            ? "Prorated charge for upgrade from {$oldPackage->name} to {$newPackage->name}"
                            : "Prorated credit for downgrade from {$oldPackage->name} to {$newPackage->name}",
                        'old_package' => $oldPackage->name,
                        'new_package' => $newPackage->name,
                        'days_remaining' => $daysRemaining,
                        'created_at' => now()->toDateTimeString(),
                    ];
                    $subscription->metadata = $metadata;

                    Log::info('Proration adjustment calculated', [
                        'subscription_id' => $subscription->id,
                        'old_package' => $oldPackage->name,
                        'new_package' => $newPackage->name,
                        'days_remaining' => $daysRemaining,
                        'adjustment_amount' => round($difference, 2),
                    ]);
                }
            }

            $subscription->save();

            Log::info('Subscription plan changed', [
                'subscription_id' => $subscription->id,
                'old_package' => $oldPackage->name,
                'new_package' => $newPackage->name,
                'prorate' => $prorate,
            ]);

            return $subscription->fresh();
        });
    }

    public function updateExtraDoctors(ClientSubscription $subscription, int $count): bool
    {
        $oldCount = $subscription->extra_doctors_count;
        $subscription->extra_doctors_count = max(0, $count);
        $saved = $subscription->save();

        if ($saved) {
            Log::info('Extra doctors updated', [
                'subscription_id' => $subscription->id,
                'old_count' => $oldCount,
                'new_count' => $count,
            ]);
        }

        return $saved;
    }

    public function extendTrial(ClientSubscription $subscription, int $days): bool
    {
        $extended = $subscription->extendTrial($days);

        if ($extended) {
            Log::info('Trial extended', [
                'subscription_id' => $subscription->id,
                'days' => $days,
                'new_trial_end' => $subscription->trial_ends_at,
            ]);
        }

        return $extended;
    }

    public function giftSubscription(Client $client, Package $package, int $freeMonths): ClientSubscription
    {
        return $this->create($client, $package, [
            'free_months' => $freeMonths,
            'metadata' => [
                'is_gift' => true,
                'gift_months' => $freeMonths,
            ],
        ]);
    }

    public function processRenewals(): int
    {
        $subscriptions = ClientSubscription::needsBilling()->get();
        $count = 0;

        foreach ($subscriptions as $subscription) {
            try {
                if ($subscription->status === SubscriptionStatus::TRIAL && $subscription->trial_ends_at->isPast()) {
                    $subscription->status = SubscriptionStatus::ACTIVE;
                    $subscription->save();
                }

                $invoice = $this->invoiceService->generate($subscription);

                if ($invoice) {
                    $subscription->updateBillingPeriod();

                    // Cambiar estatus a PAST_DUE cuando se genera la factura
                    // La suscripción tiene 7 días de gracia para pagar
                    $subscription->status = SubscriptionStatus::PAST_DUE;
                    $subscription->save();

                    $count++;

                    Log::info('Renewal invoice generated', [
                        'subscription_id' => $subscription->id,
                        'invoice_id' => $invoice->id,
                        'new_status' => 'past_due',
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Failed to process renewal for subscription {$subscription->id}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $count;
    }

    public function processExpired(): int
    {
        $gracePeriodDays = config('subscriptions.grace_period_days', 7);
        $expirationDays = config('subscriptions.expiration_after_suspension_days', 30);

        $expiredTrials = ClientSubscription::where('status', SubscriptionStatus::TRIAL)
            ->whereDate('trial_ends_at', '<', today()->subDays($gracePeriodDays))
            ->get();

        $suspendedExpired = ClientSubscription::where('status', SubscriptionStatus::SUSPENDED)
            ->where(function ($query) use ($expirationDays) {
                // Use suspended_at if available, otherwise fallback to updated_at
                $query->whereDate('suspended_at', '<', today()->subDays($expirationDays))
                    ->orWhere(function ($q) use ($expirationDays) {
                        $q->whereNull('suspended_at')
                            ->whereDate('updated_at', '<', today()->subDays($expirationDays));
                    });
            })
            ->get();

        $count = 0;

        foreach ($expiredTrials as $subscription) {
            $subscription->status = SubscriptionStatus::EXPIRED;
            $subscription->save();
            $count++;

            Log::info('Trial subscription expired', ['subscription_id' => $subscription->id]);
        }

        foreach ($suspendedExpired as $subscription) {
            $subscription->status = SubscriptionStatus::EXPIRED;
            $subscription->save();
            $count++;

            Log::info('Suspended subscription expired', ['subscription_id' => $subscription->id]);
        }

        return $count;
    }
}
