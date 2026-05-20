<?php

namespace App\Services;

use App\Enums\InvoiceItemType;
use App\Enums\InvoiceStatus;
use App\Enums\NeoPaymentStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\ClientInvoice;
use App\Models\ClientInvoiceItem;
use App\Models\ClientInvoicePayment;
use App\Models\ClientSubscription;
use App\Notifications\InvoiceGeneratedNotification;
use App\Notifications\PaymentFailedNotification;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientInvoiceService
{
    public function __construct(
        protected DiscountService $discountService,
        protected ?NeoPaymentsService $neoPaymentsService = null
    ) {
        if (config('services.neopayments.enabled') && ! $this->neoPaymentsService) {
            $this->neoPaymentsService = app(NeoPaymentsService::class);
        }
    }

    public function generate(ClientSubscription $subscription): ?ClientInvoice
    {
        return DB::transaction(function () use ($subscription) {
            $periodStart = $subscription->current_period_ends_at ?? now();
            $periodEnd = $periodStart->copy()->addDays($subscription->package->billing_period_days);

            // Retry logic to handle race conditions with invoice number generation
            $maxAttempts = 5;
            $attempt = 0;
            $invoice = null;

            while ($attempt < $maxAttempts) {
                try {
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

                    break;
                } catch (QueryException $e) {
                    if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'invoice_number_unique')) {
                        $attempt++;

                        if ($attempt >= $maxAttempts) {
                            Log::error('Failed to generate unique invoice number after max attempts', [
                                'subscription_id' => $subscription->id,
                                'attempts' => $attempt,
                                'error' => $e->getMessage(),
                            ]);
                            throw $e;
                        }

                        Log::warning('Duplicate invoice number detected, retrying', [
                            'subscription_id' => $subscription->id,
                            'attempt' => $attempt,
                        ]);

                        usleep(100000 * $attempt);

                        continue;
                    }

                    throw $e;
                }
            }

            $this->calculateItems($invoice, $subscription);

            $this->applyAvailableDiscounts($invoice);

            Log::info('Invoice generated', [
                'invoice_id' => $invoice->id,
                'subscription_id' => $subscription->id,
                'total' => $invoice->total,
            ]);

            // Attempt automatic credit card payment
            $payment = $this->attemptAutomaticPayment($invoice);

            // Check if payment was successful during registration
            $paymentSuccessDuringRegistration = $payment && $payment->status->value === 'completed' && $invoice->status->value === 'paid';

            // Send invoice notification only if payment was NOT successful during registration
            // (If paid immediately, the user will get welcome + subscription active notification instead)
            if (! $paymentSuccessDuringRegistration) {
                $notifiableUser = InvoiceGeneratedNotification::getNotifiableUser($invoice);
                if ($notifiableUser) {
                    $delay = now()->plus(minutes: 1);
                    $notification = new InvoiceGeneratedNotification($invoice);

                    $notifiableUser->notify($notification->delay($delay));

                    Log::info('Invoice notification sent', [
                        'invoice_id' => $invoice->id,
                        'user_id' => $notifiableUser->id,
                        'user_email' => $notifiableUser->email,
                        'payment_attempted' => $payment !== null,
                    ]);
                }
            } else {
                Log::info('Invoice notification skipped (paid during registration)', [
                    'invoice_id' => $invoice->id,
                    'payment_id' => $payment->id,
                ]);
            }

            return $invoice->fresh();
        });
    }

    public function generateForPeriod(ClientSubscription $subscription, Carbon $startDate, Carbon $endDate): ClientInvoice
    {
        return DB::transaction(function () use ($subscription, $startDate, $endDate) {
            // Retry logic to handle race conditions with invoice number generation
            $maxAttempts = 5;
            $attempt = 0;
            $invoice = null;

            while ($attempt < $maxAttempts) {
                try {
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

                    break;
                } catch (QueryException $e) {
                    if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'invoice_number_unique')) {
                        $attempt++;

                        if ($attempt >= $maxAttempts) {
                            Log::error('Failed to generate unique invoice number after max attempts', [
                                'subscription_id' => $subscription->id,
                                'attempts' => $attempt,
                                'error' => $e->getMessage(),
                            ]);
                            throw $e;
                        }

                        Log::warning('Duplicate invoice number detected, retrying', [
                            'subscription_id' => $subscription->id,
                            'attempt' => $attempt,
                        ]);

                        usleep(100000 * $attempt);

                        continue;
                    }

                    throw $e;
                }
            }

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
                    $subscription->status = SubscriptionStatus::ACTIVE;
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
        $overdueInvoices = ClientInvoice::whereIn('status', [
            InvoiceStatus::PENDING->value,
            InvoiceStatus::PARTIALLY_PAID->value,
            InvoiceStatus::OVERDUE->value,
        ])
            ->whereDate('due_date', '<', today())
            ->get();

        $count = 0;
        $gracePeriodDays = (int) config('subscriptions.grace_period_days', 7);

        foreach ($overdueInvoices as $invoice) {
            // Mark as overdue only if not already overdue
            if ($invoice->status !== InvoiceStatus::OVERDUE) {
                $invoice->markAsOverdue();
                $count++;
            }

            if ($invoice->subscription) {
                $subscription = $invoice->subscription;
                $daysPastDue = abs($invoice->due_date->diffInDays(now(), false));

                // If subscription is active/trial with overdue invoice, mark as past_due
                if (in_array($subscription->status->value, ['active', 'trial']) && $daysPastDue > 0) {
                    $subscription->status = SubscriptionStatus::PAST_DUE;
                    $subscription->grace_period_ends_at = $invoice->due_date->copy()->addDays($gracePeriodDays);
                    $subscription->save();

                    Log::info('Subscription marked as past_due', [
                        'invoice_id' => $invoice->id,
                        'subscription_id' => $subscription->id,
                        'grace_period_ends_at' => $subscription->grace_period_ends_at,
                    ]);
                }

                // If grace period not set but subscription is past_due, set it now
                if ($subscription->status->value === 'past_due' && ! $subscription->grace_period_ends_at) {
                    $subscription->grace_period_ends_at = $invoice->due_date->copy()->addDays($gracePeriodDays);
                    $subscription->save();

                    Log::info('Grace period set for existing past_due subscription', [
                        'subscription_id' => $subscription->id,
                        'grace_period_ends_at' => $subscription->grace_period_ends_at,
                    ]);
                }

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

    public function attemptAutomaticPayment(ClientInvoice $invoice): ?ClientInvoicePayment
    {
        if (! config('services.neopayments.enabled') || ! $this->neoPaymentsService) {
            return null;
        }

        $defaultCard = $invoice->client->defaultCreditCard;

        if (! $defaultCard) {
            Log::info('No default credit card found for automatic payment', [
                'invoice_id' => $invoice->id,
                'client_id' => $invoice->client_id,
            ]);

            return null;
        }

        if ($defaultCard->isExpired()) {
            Log::warning('Default credit card is expired', [
                'invoice_id' => $invoice->id,
                'client_id' => $invoice->client_id,
                'card_id' => $defaultCard->id,
                'exp_month' => $defaultCard->exp_month,
                'exp_year' => $defaultCard->exp_year,
            ]);

            return null;
        }

        try {
            // Check if this is during registration (invoice just created, within last minute)
            // Use getRawOriginal to bypass BaseModel date mutator
            $rawCreatedAt = $invoice->getRawOriginal('created_at');
            $invoiceCreatedAt = Carbon::parse($rawCreatedAt);
            $secondsSinceCreation = $invoiceCreatedAt->diffInSeconds(now());
            $isRegistration = $secondsSinceCreation <= 60; // Within last 60 seconds

            Log::info('Checking if during registration', [
                'invoice_id' => $invoice->id,
                'raw_created_at' => $rawCreatedAt,
                'seconds_since_creation' => $secondsSinceCreation,
                'is_registration' => $isRegistration,
            ]);

            $metadata = [
                'invoice_id' => $invoice->id,
                'client_id' => $invoice->client_id,
                'subscription_id' => $invoice->subscription_id,
                'registration' => $isRegistration,
            ];

            // During registration, use single attempt without retries for faster feedback
            if ($isRegistration) {
                Log::info('Using single payment attempt (registration mode)', [
                    'invoice_id' => $invoice->id,
                ]);

                $result = $this->neoPaymentsService->processPayment(
                    $defaultCard->card_token,
                    $invoice->total,
                    $metadata
                );
            } else {
                Log::info('Using payment with retry (normal mode)', [
                    'invoice_id' => $invoice->id,
                ]);

                $result = $this->neoPaymentsService->processPaymentWithRetry(
                    $defaultCard->card_token,
                    $invoice->total,
                    $metadata
                );
            }

            $status = $this->neoPaymentsService->mapTransactionStatus($result['status'] ?? 'FAILED');

            $payment = $this->recordPayment($invoice, $invoice->total, PaymentMethod::CREDIT_CARD->value, [
                'gateway' => 'neopayments',
                'transaction_id' => $result['id'] ?? $result['identifier'] ?? null,
                'metadata' => $result,
                'processed_by' => null,
            ]);

            if ($status->isSuccess()) {
                // Load invoice relationship before marking as completed
                $payment->load('invoice');
                $payment->markAsCompleted();

                Log::info('Automatic credit card payment successful', [
                    'invoice_id' => $invoice->id,
                    'payment_id' => $payment->id,
                    'transaction_id' => $result['id'] ?? $result['identifier'] ?? null,
                ]);
            } else {
                $payment->markAsFailed("Payment {$status->label()}: ".($result['message'] ?? 'Unknown error'));

                Log::warning('Automatic credit card payment failed', [
                    'invoice_id' => $invoice->id,
                    'payment_id' => $payment->id,
                    'status' => $status->value,
                    'message' => $result['message'] ?? null,
                ]);

                $this->notifyPaymentFailed($invoice, $payment, $status);

                // If this is during registration, throw exception to rollback everything
                if (isset($metadata['registration']) && $metadata['registration'] === true) {
                    $errorMessage = 'No se pudo procesar el pago con la tarjeta proporcionada.';
                    if (isset($result['metadatas']['response_text'])) {
                        $errorMessage .= ' '.$result['metadatas']['response_text'].'.';
                    }
                    $errorMessage .= ' Por favor, verifica los datos de tu tarjeta e intenta nuevamente.';

                    throw new \Exception($errorMessage);
                }
            }

            return $payment;
        } catch (\Exception $e) {
            Log::error('Automatic payment exception', [
                'invoice_id' => $invoice->id,
                'client_id' => $invoice->client_id,
                'error' => $e->getMessage(),
                'is_registration' => $isRegistration ?? false,
            ]);

            // If this is during registration, rethrow the exception to rollback everything
            if (isset($isRegistration) && $isRegistration === true) {
                throw $e;
            }

            $payment = $this->recordPayment($invoice, $invoice->total, PaymentMethod::CREDIT_CARD->value, [
                'gateway' => 'neopayments',
                'metadata' => ['error' => $e->getMessage()],
                'processed_by' => null,
            ]);

            $payment->markAsFailed('Exception: '.$e->getMessage());

            $this->notifyPaymentFailed($invoice, $payment, NeoPaymentStatus::FAILED);

            return $payment;
        }
    }

    public function retryFailedPayment(ClientInvoicePayment $payment): bool
    {
        if (! config('services.neopayments.enabled') || ! $this->neoPaymentsService) {
            return false;
        }

        $invoice = $payment->invoice;
        $defaultCard = $invoice->client->defaultCreditCard;

        if (! $defaultCard || $defaultCard->isExpired()) {
            return false;
        }

        $retryCount = $payment->metadata['retry_count'] ?? 0;
        $maxRetries = config('services.neopayments.retry_attempts', 2);

        if ($retryCount >= $maxRetries) {
            Log::warning('Maximum retry attempts reached', [
                'payment_id' => $payment->id,
                'retry_count' => $retryCount,
            ]);

            return false;
        }

        try {
            $metadata = array_merge($payment->metadata ?? [], [
                'retry_count' => $retryCount + 1,
                'original_payment_id' => $payment->id,
            ]);

            $result = $this->neoPaymentsService->processPayment(
                $defaultCard->card_token,
                $payment->amount,
                $metadata
            );

            $status = $this->neoPaymentsService->mapTransactionStatus($result['status'] ?? 'FAILED');

            $payment->gateway_transaction_id = $result['transaction_id'] ?? $payment->gateway_transaction_id;
            $payment->metadata = array_merge($payment->metadata ?? [], [
                'retry_count' => $retryCount + 1,
                'last_retry_at' => now()->toIso8601String(),
                'last_result' => $result,
            ]);

            if ($status->isSuccess()) {
                $payment->markAsCompleted();

                Log::info('Payment retry successful', [
                    'payment_id' => $payment->id,
                    'retry_count' => $retryCount + 1,
                ]);

                return true;
            } else {
                $retryNumber = $retryCount + 1;
                $payment->markAsFailed("Retry {$retryNumber} - {$status->label()}: ".($result['message'] ?? 'Unknown error'));

                Log::warning('Payment retry failed', [
                    'payment_id' => $payment->id,
                    'retry_count' => $retryCount + 1,
                    'status' => $status->value,
                ]);

                return false;
            }
        } catch (\Exception $e) {
            Log::error('Payment retry exception', [
                'payment_id' => $payment->id,
                'retry_count' => $retryCount + 1,
                'error' => $e->getMessage(),
            ]);

            $payment->metadata = array_merge($payment->metadata ?? [], [
                'retry_count' => $retryCount + 1,
                'last_retry_at' => now()->toIso8601String(),
                'retry_error' => $e->getMessage(),
            ]);
            $payment->save();

            return false;
        }
    }

    private function notifyPaymentFailed(ClientInvoice $invoice, ClientInvoicePayment $payment, NeoPaymentStatus $status): void
    {
        $notifiableUser = InvoiceGeneratedNotification::getNotifiableUser($invoice);

        if ($notifiableUser) {
            $notifiableUser->notify(new PaymentFailedNotification($invoice, $payment, $status));

            Log::info('Payment failure notification sent', [
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
                'user_email' => $notifiableUser->email,
            ]);
        }
    }
}
