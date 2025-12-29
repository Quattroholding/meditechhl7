<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientInvoice extends BaseModel
{
    protected $fillable = [
        'invoice_number',
        'client_id',
        'subscription_id',
        'subtotal',
        'discount_amount',
        'discount_percentage',
        'discount_reason',
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
        $this->total = $this->subtotal - $this->discount_amount;
        $this->save();
    }

    public function applyDiscount(float $amount, ?string $reason = null): bool
    {
        $this->discount_amount = $amount;
        $this->discount_reason = $reason;
        $this->total = max(0, $this->subtotal - $amount);

        return $this->save();
    }

    public static function generateInvoiceNumber(): string
    {
        $format = config('subscriptions.invoice_number_format', 'SUB-{YEAR}{MONTH}-{SEQUENCE}');

        $year = now()->format('Y');
        $month = now()->format('m');
        $sequence = static::where('invoice_number', 'like', "SUB-{$year}{$month}-%")->count() + 1;

        return str_replace(
            ['{YEAR}', '{MONTH}', '{SEQUENCE}'],
            [$year, $month, str_pad($sequence, 4, '0', STR_PAD_LEFT)],
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

            // Activate subscription if it's pending activation
            if ($result && $this->subscription) {
                if ($this->subscription->status === \App\Enums\SubscriptionStatus::PENDING_ACTIVATION) {
                    $this->subscription->activate();
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
