<?php

namespace App\Services;

use App\Enums\InvoiceItemType;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\ClientInvoice;
use App\Models\ClientInvoiceItem;
use App\Models\ClientInvoicePayment;
use App\Models\ClientSubscription;
use App\Notifications\InvoiceGeneratedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientInvoiceService
{
    public function __construct(
        protected DiscountService $discountService
    ) {}

    public function generate(ClientSubscription $subscription): ?ClientInvoice
    {
        return DB::transaction(function () use ($subscription) {
            $periodStart = $subscription->current_period_ends_at ?? now();
            $periodEnd = $periodStart->copy()->addDays($subscription->package->billing_period_days);

            $invoice = new ClientInvoice;
            $invoice->client_id = $subscription->client_id;
            $invoice->subscription_id = $subscription->id;
            $invoice->period_start = $periodStart;
            $invoice->period_end = $periodEnd;
            $invoice->due_date = $periodStart->copy()->addDays(7);
            $invoice->status = InvoiceStatus::PENDING;
            $invoice->subtotal = 0;
            $invoice->tax_rate = 0.07; // 7% ITBMS Panama
            $invoice->tax_amount = 0;
            $invoice->total = 0;
            $invoice->save();

            $this->calculateItems($invoice, $subscription);

            $this->applyAvailableDiscounts($invoice);

            Log::info('Invoice generated', [
                'invoice_id' => $invoice->id,
                'subscription_id' => $subscription->id,
                'total' => $invoice->total,
            ]);

            // Send notification to admin client or doctor
            $notifiableUser = InvoiceGeneratedNotification::getNotifiableUser($invoice);
            if ($notifiableUser) {
                $delay = now()->plus(minutes: 1);
                $notifiableUser->notify((new InvoiceGeneratedNotification($invoice))->delay($delay));

                Log::info('Invoice notification sent', [
                    'invoice_id' => $invoice->id,
                    'user_id' => $notifiableUser->id,
                    'user_email' => $notifiableUser->email,
                ]);
            } else {
                Log::warning('No notifiable user found for invoice', [
                    'invoice_id' => $invoice->id,
                    'client_id' => $invoice->client_id,
                ]);
            }

            return $invoice->fresh();
        });
    }

    public function generateForPeriod(ClientSubscription $subscription, Carbon $startDate, Carbon $endDate): ClientInvoice
    {
        return DB::transaction(function () use ($subscription, $startDate, $endDate) {
            $invoice = new ClientInvoice;
            $invoice->client_id = $subscription->client_id;
            $invoice->subscription_id = $subscription->id;
            $invoice->period_start = $startDate;
            $invoice->period_end = $endDate;
            $invoice->due_date = $startDate->copy()->addDays(7);
            $invoice->status = InvoiceStatus::PENDING;
            $invoice->subtotal = 0;
            $invoice->tax_rate = 0.07; // 7% ITBMS Panama
            $invoice->tax_amount = 0;
            $invoice->total = 0;
            $invoice->save();

            $this->calculateItems($invoice, $subscription);

            $this->applyAvailableDiscounts($invoice);

            // Send notification to admin client or doctor
            $notifiableUser = InvoiceGeneratedNotification::getNotifiableUser($invoice);
            if ($notifiableUser) {
                $notifiableUser->notify(new InvoiceGeneratedNotification($invoice));

                Log::info('Invoice notification sent', [
                    'invoice_id' => $invoice->id,
                    'user_id' => $notifiableUser->id,
                    'user_email' => $notifiableUser->email,
                ]);
            }

            return $invoice->fresh();
        });
    }

    public function calculateItems(ClientInvoice $invoice, ClientSubscription $subscription): void
    {
        $invoice->items()->delete();

        $baseItem = new ClientInvoiceItem;
        $baseItem->client_invoice_id = $invoice->id;
        $baseItem->description = "{$subscription->package->name} - Base Subscription ({$subscription->package->max_doctors_included} doctors)";
        $baseItem->quantity = 1;
        $baseItem->unit_price = $subscription->package->base_price;
        $baseItem->total = $subscription->package->base_price;
        $baseItem->type = InvoiceItemType::SUBSCRIPTION_BASE;
        $baseItem->save();

        if ($subscription->extra_doctors_count > 0) {
            $extraItem = new ClientInvoiceItem;
            $extraItem->client_invoice_id = $invoice->id;
            $extraItem->description = "Extra Doctors ({$subscription->extra_doctors_count})";
            $extraItem->quantity = $subscription->extra_doctors_count;
            $extraItem->unit_price = $subscription->package->price_per_extra_doctor;
            $extraItem->total = $subscription->extra_doctors_count * $subscription->package->price_per_extra_doctor;
            $extraItem->type = InvoiceItemType::EXTRA_DOCTORS;
            $extraItem->save();
        }

        // Apply pending proration adjustment if exists
        $metadata = $subscription->metadata ?? [];
        if (isset($metadata['pending_proration'])) {
            $proration = $metadata['pending_proration'];
            $amount = (float) $proration['amount'];
            $description = $proration['description'] ?? 'Proration adjustment';

            $prorationItem = new ClientInvoiceItem;
            $prorationItem->client_invoice_id = $invoice->id;
            $prorationItem->description = $description;
            $prorationItem->quantity = 1;
            $prorationItem->unit_price = $amount;
            $prorationItem->total = $amount;
            $prorationItem->type = $amount >= 0 ? InvoiceItemType::ADJUSTMENT : InvoiceItemType::CREDIT;
            $prorationItem->save();

            // Clear the pending proration after applying
            unset($metadata['pending_proration']);
            $subscription->metadata = $metadata;
            $subscription->save();

            Log::info('Proration adjustment applied to invoice', [
                'invoice_id' => $invoice->id,
                'subscription_id' => $subscription->id,
                'adjustment_amount' => $amount,
            ]);
        }

        $invoice->calculateTotal();
    }

    public function applyAvailableDiscounts(ClientInvoice $invoice): void
    {
        $discounts = $this->discountService->getAvailableDiscounts($invoice->client);

        $totalDiscount = 0;
        $discountReasons = [];

        foreach ($discounts as $discount) {
            $amount = $discount->apply($invoice);
            $totalDiscount += $amount;
            $discountReasons[] = $discount->reason;
        }

        if ($totalDiscount > 0) {
            $invoice->applyDiscount($totalDiscount, implode(', ', $discountReasons));

            Log::info('Discounts applied to invoice', [
                'invoice_id' => $invoice->id,
                'discount_amount' => $totalDiscount,
                'reasons' => $discountReasons,
            ]);
        }
    }

    public function recordPayment(ClientInvoice $invoice, float $amount, string $method, array $details = []): ClientInvoicePayment
    {
        return DB::transaction(function () use ($invoice, $amount, $method, $details) {
            $payment = new ClientInvoicePayment;
            $payment->client_invoice_id = $invoice->id;
            $payment->amount = $amount;
            $payment->payment_method = $method;
            $payment->payment_reference = $details['reference'] ?? null;
            $payment->payment_date = $details['date'] ?? now();
            $payment->payment_gateway = $details['gateway'] ?? null;
            $payment->gateway_transaction_id = $details['transaction_id'] ?? null;
            $payment->status = PaymentStatus::PENDING;
            $payment->processed_by = $details['processed_by'] ?? auth()->id();
            $payment->notes = $details['notes'] ?? null;
            $payment->metadata = $details['metadata'] ?? null;
            $payment->save();

            if ($payment->payment_method !== PaymentMethod::ACH->value) {
                $payment->markAsCompleted();
            }

            $this->markAsPaidIfComplete($invoice);

            Log::info('Payment recorded', [
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
                'amount' => $amount,
                'method' => $method,
            ]);

            return $payment->fresh();
        });
    }

    public function markAsPaidIfComplete(ClientInvoice $invoice): bool
    {
        $totalPaid = $invoice->getTotalPaid();

        if ($totalPaid >= $invoice->total) {
            $marked = $invoice->markAsPaid();

            if ($marked && $invoice->subscription) {
                $subscription = $invoice->subscription;

                if ($subscription->status->value === 'pending_activation') {
                    $subscription->activate();
                } elseif ($subscription->status->value === 'suspended') {
                    $subscription->resume();
                } elseif ($subscription->status->value === 'past_due') {
                    // Reactivar suscripción cuando se paga dentro del periodo de gracia
                    $subscription->status = \App\Enums\SubscriptionStatus::ACTIVE;
                    $subscription->save();

                    Log::info('Subscription reactivated from past_due', [
                        'subscription_id' => $subscription->id,
                        'invoice_id' => $invoice->id,
                    ]);
                }
            }

            return $marked;
        }

        return false;
    }

    public function generateInvoiceNumber(): string
    {
        return ClientInvoice::generateInvoiceNumber();
    }

    public function sendByEmail(ClientInvoice $invoice): void
    {
        // TODO: Implement email sending
        Log::info('Invoice email would be sent', ['invoice_id' => $invoice->id]);
    }

    public function processOverdue(): int
    {
        $overdueInvoices = ClientInvoice::whereIn('status', [InvoiceStatus::PENDING->value, InvoiceStatus::PARTIALLY_PAID->value])
            ->whereDate('due_date', '<', today())
            ->get();

        $count = 0;
        $gracePeriodDays = config('subscriptions.grace_period_days', 7);

        foreach ($overdueInvoices as $invoice) {
            $invoice->markAsOverdue();
            $count++;

            if ($invoice->subscription) {
                $subscription = $invoice->subscription;
                $daysPastDue = now()->diffInDays($invoice->due_date);

                // Si pasó el periodo de gracia (7 días) y está en PAST_DUE, suspender
                if ($daysPastDue > $gracePeriodDays && $subscription->status->value === 'past_due') {
                    $subscription->suspend();

                    Log::info('Subscription suspended due to overdue invoice', [
                        'invoice_id' => $invoice->id,
                        'subscription_id' => $subscription->id,
                        'days_past_due' => $daysPastDue,
                        'previous_status' => 'past_due',
                    ]);
                }
            }
        }

        return $count;
    }

    public function cancel(ClientInvoice $invoice, string $reason): bool
    {
        $cancelled = $invoice->cancel($reason);

        if ($cancelled) {
            Log::info('Invoice cancelled', [
                'invoice_id' => $invoice->id,
                'reason' => $reason,
            ]);
        }

        return $cancelled;
    }
}
