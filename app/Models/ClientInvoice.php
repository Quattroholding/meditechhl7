<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientInvoice extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'client_id',
        'subscription_id',
        'subtotal',
        'discount_amount',
        'discount_percentage',
        'discount_reason',
        'tax_rate',
        'tax_amount',
        'total',
        'status',
        'due_date',
        'paid_at',
        'period_start',
        'period_end',
        'notes',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'tax_rate' => 'decimal:4',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'status' => InvoiceStatus::class,
            'due_date' => 'date',
            'paid_at' => 'datetime',
            'period_start' => 'date',
            'period_end' => 'date',
            'metadata' => 'array',
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (! $invoice->invoice_number) {
                $invoice->invoice_number = static::generateInvoiceNumber();
            }
        });

        // Global scope to filter invoices by client
        static::addGlobalScope('client_filter', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();

                // Admin and contabilidad can see all invoices
                if ($user->hasRole(['admin', 'contabilidad'])) {
                    return;
                }

                // Other users only see their client's invoices
                $client = $user->getCurrentClient();
                if ($client) {
                    $builder->where('client_invoices.client_id', $client->id);
                }
            }
        });
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

    public function items(): HasMany
    {
        return $this->hasMany(ClientInvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ClientInvoicePayment::class);
    }

    public function appliedDiscounts(): HasMany
    {
        return $this->hasMany(SubscriptionDiscount::class, 'source_id')
            ->where('source', 'invoice');
    }

    // Scopes
    public function scopePending(Builder $query): void
    {
        $query->where('status', InvoiceStatus::PENDING->value);
    }

    public function scopePaid(Builder $query): void
    {
        $query->where('status', InvoiceStatus::PAID->value);
    }

    public function scopeOverdue(Builder $query): void
    {
        $query->where('status', InvoiceStatus::OVERDUE->value);
    }

    public function scopeByStatus(Builder $query, InvoiceStatus $status): void
    {
        $query->where('status', $status->value);
    }

    public function scopeForPeriod(Builder $query, Carbon $start, Carbon $end): void
    {
        $query->where('period_start', '>=', $start)
            ->where('period_end', '<=', $end);
    }

    public function scopeDueToday(Builder $query): void
    {
        $query->where('due_date', '<=', today())
            ->whereIn('status', [
                InvoiceStatus::PENDING->value,
                InvoiceStatus::PARTIALLY_PAID->value,
            ]);
    }

    // Accessors
    protected function isPaid(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === InvoiceStatus::PAID
        );
    }

    protected function isOverdue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === InvoiceStatus::OVERDUE
                || ($this->due_date && $this->due_date->isPast() && ! $this->is_paid)
        );
    }

    protected function amountDue(): Attribute
    {
        return Attribute::make(
            get: fn () => max(0, (float) $this->total - $this->getTotalPaid())
        );
    }

    protected function formattedNumber(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->invoice_number
        );
    }

    // Methods
    public function markAsPaid(): bool
    {
        $this->status = InvoiceStatus::PAID;
        $this->paid_at = now();

        return $this->save();
    }

    public function markAsOverdue(): bool
    {
        if ($this->status->isPayable()) {
            $this->status = InvoiceStatus::OVERDUE;

            return $this->save();
        }

        return false;
    }

    public function cancel(?string $reason = null): bool
    {
        $this->status = InvoiceStatus::CANCELLED;

        if ($reason) {
            $this->notes = ($this->notes ? $this->notes."\n" : '')."Cancelled: {$reason}";
        }

        return $this->save();
    }

    public function calculateTotal(): void
    {
        $this->subtotal = $this->items()->sum('total');

        // Calculate taxable amount (subtotal minus discount)
        $taxableAmount = $this->subtotal - $this->discount_amount;

        // Calculate ITBMS tax (7% by default on taxable amount)
        $this->tax_amount = round($taxableAmount * ($this->tax_rate ?? 0.07), 2);

        // Total = taxable amount + tax
        $this->total = $taxableAmount + $this->tax_amount;

        $this->save();
    }

    public function applyDiscount(float $amount, ?string $reason = null): bool
    {
        $this->discount_amount = $amount;
        $this->discount_reason = $reason;

        // Calculate taxable amount (subtotal minus discount)
        $taxableAmount = max(0, $this->subtotal - $amount);

        // Calculate ITBMS tax (7% by default on taxable amount)
        $this->tax_amount = round($taxableAmount * ($this->tax_rate ?? 0.07), 2);

        // Total = taxable amount + tax
        $this->total = $taxableAmount + $this->tax_amount;

        return $this->save();
    }

    public static function generateInvoiceNumber(): string
    {
        $format = config('subscriptions.invoice_number_format', 'SUB-{YEAR}{MONTH}-{SEQUENCE}');

        $year = now()->format('Y');
        $month = now()->format('m');
        $prefix = "SUB-{$year}{$month}-";

        // Add small random delay to reduce race condition likelihood
        usleep(random_int(1000, 5000));

        // Buscar el último número de secuencia para este período GLOBALMENTE (sin scope)
        // porque el constraint único es global
        // Use FOR UPDATE to lock the row and prevent race conditions
        $lastInvoice = static::withoutGlobalScope('client_filter')
            ->where('invoice_number', 'like', $prefix.'%')
            ->orderByRaw('CAST(SUBSTRING(invoice_number, '.strlen($prefix).' + 1) AS UNSIGNED) DESC')
            ->lockForUpdate()
            ->first();

        if ($lastInvoice) {
            // Extraer el número de secuencia del último invoice_number
            preg_match('/'.preg_quote($prefix, '/').'(\d+)/', $lastInvoice->invoice_number, $matches);
            $sequence = isset($matches[1]) ? (int) $matches[1] + 1 : 1;
        } else {
            $sequence = 1;
        }

        // Add random offset to reduce collision probability in high-concurrency scenarios
        $randomOffset = random_int(0, 2);
        $sequence += $randomOffset;

        // Intentar generar un número único (retry loop por si hay race condition)
        $maxAttempts = 100;
        $attempt = 0;

        do {
            $currentSequence = $sequence + $attempt;
            $invoiceNumber = str_replace(
                ['{YEAR}', '{MONTH}', '{SEQUENCE}'],
                [$year, $month, str_pad($currentSequence, 4, '0', STR_PAD_LEFT)],
                $format
            );

            // Verificar si ya existe GLOBALMENTE (sin scope)
            $exists = static::withoutGlobalScope('client_filter')
                ->where('invoice_number', $invoiceNumber)
                ->exists();

            if (! $exists) {
                return $invoiceNumber;
            }

            $attempt++;
        } while ($attempt < $maxAttempts);

        // Si después de 100 intentos no se puede generar un número único,
        // agregar timestamp para garantizar unicidad
        return str_replace(
            ['{YEAR}', '{MONTH}', '{SEQUENCE}'],
            [$year, $month, str_pad($sequence + $attempt, 4, '0', STR_PAD_LEFT).'-'.now()->timestamp],
            $format
        );
    }

    public function getTotalPaid(): float
    {
        return (float) $this->payments()
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getRemainingBalance(): float
    {
        return max(0, (float) $this->total - $this->getTotalPaid());
    }

    public function updatePaymentStatus(): bool
    {
        $totalPaid = $this->getTotalPaid();

        if ($totalPaid >= $this->total) {
            $result = $this->markAsPaid();

            // Reactivate/activate subscription based on current status
            if ($result && $this->subscription) {
                $subscription = $this->subscription;

                if ($subscription->status === \App\Enums\SubscriptionStatus::PENDING_ACTIVATION) {
                    $subscription->activate();

                    // Confirmar referral si el cliente fue referido y es su primera factura pagada
                    $this->confirmReferralIfApplicable();

                    // Enviar notificación de agradecimiento al cliente
                    $notifiableUser = \App\Notifications\InvoiceGeneratedNotification::getNotifiableUser($this);
                    if ($notifiableUser) {
                        $notifiableUser->notify(new \App\Notifications\SubscriptionActivatedNotification($subscription));
                    }
                } elseif ($subscription->status === \App\Enums\SubscriptionStatus::SUSPENDED) {
                    // Reactivate from suspended status
                    $subscription->resume();

                    \Log::info('Subscription resumed from suspended', [
                        'subscription_id' => $subscription->id,
                        'invoice_id' => $this->id,
                    ]);
                } elseif ($subscription->status === \App\Enums\SubscriptionStatus::PAST_DUE) {
                    // Reactivate from past_due status
                    $subscription->status = \App\Enums\SubscriptionStatus::ACTIVE;
                    $subscription->grace_period_ends_at = null;
                    $subscription->retry_count = 0;
                    $subscription->next_retry_at = null;
                    $subscription->save();

                    \Log::info('Subscription reactivated from past_due', [
                        'subscription_id' => $subscription->id,
                        'invoice_id' => $this->id,
                    ]);
                }
            }

            return $result;
        } elseif ($totalPaid > 0) {
            $this->status = InvoiceStatus::PARTIALLY_PAID;

            return $this->save();
        }

        return false;
    }

    /**
     * Confirmar referral si el cliente fue referido y esta es su primera factura pagada
     */
    protected function confirmReferralIfApplicable(): void
    {
        $client = $this->client;

        // Verificar si el cliente fue referido
        if (! $client->referred_by_client_id) {
            return;
        }

        // Buscar el referral pendiente
        $referral = ClientReferral::where('referred_client_id', $client->id)
            ->where('status', \App\Enums\ReferralStatus::PENDING)
            ->first();

        if (! $referral) {
            return;
        }

        // Confirmar el referral (esto aplicará automáticamente la recompensa al referrer)
        $referralService = app(\App\Services\ReferralService::class);
        $confirmed = $referralService->confirmReferral($referral);

        if ($confirmed) {
            \Log::info('Referral confirmed automatically after first invoice payment', [
                'referral_id' => $referral->id,
                'referred_client_id' => $client->id,
                'referrer_client_id' => $referral->referrer_client_id,
                'invoice_id' => $this->id,
            ]);
        }
    }

    /**
     * Check if there is a pending payment for the full amount of the invoice
     * This prevents users from submitting duplicate payments while one is being processed
     */
    public function hasPendingPaymentForFullAmount(): bool
    {
        return $this->payments()
            ->where('status', PaymentStatus::PENDING->value)
            ->where('amount', '>=', $this->amount_due)
            ->exists();
    }
}
