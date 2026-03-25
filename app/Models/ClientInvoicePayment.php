<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientInvoicePayment extends BaseModel
{
    public static function boot()
    {
        parent::boot();

        // Global scope to filter payments by client
        static::addGlobalScope('client_filter', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();

                // Admin and contabilidad can see all payments
                if ($user->hasRole(['admin', 'contabilidad'])) {
                    return;
                }

                // Other users only see their client's payments
                $client = $user->getCurrentClient();
                if ($client) {
                    $builder->whereHas('invoice', function ($query) use ($client) {
                        $query->where('client_id', $client->id);
                    });
                }
            }
        });
    }

    protected $fillable = [
        'client_invoice_id',
        'amount',
        'payment_method',
        'payment_reference',
        'payment_date',
        'payment_gateway',
        'gateway_transaction_id',
        'status',
        'processed_by',
        'notes',
        'receipt_file_path',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_method' => PaymentMethod::class,
            'payment_date' => 'date',
            'status' => PaymentStatus::class,
            'metadata' => 'array',
        ];
    }

    // Relationships
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(ClientInvoice::class, 'client_invoice_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Scopes
    public function scopeCompleted(Builder $query): void
    {
        $query->where('status', PaymentStatus::COMPLETED->value);
    }

    public function scopeByMethod(Builder $query, PaymentMethod $method): void
    {
        $query->where('payment_method', $method->value);
    }

    public function scopeByDateRange(Builder $query, $startDate, $endDate): void
    {
        $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    // Methods
    public function markAsCompleted(?int $verifiedBy = null): bool
    {
        return \DB::transaction(function () use ($verifiedBy) {
            $this->status = PaymentStatus::COMPLETED;

            if ($verifiedBy) {
                $this->notes = ($this->notes ? $this->notes."\n" : '').'Verificado por: '.User::find($verifiedBy)->full_name.' el '.now()->format('d/m/Y H:i');
            }

            $saved = $this->save();

            if ($saved) {
                // Este método puede fallar y causará rollback automático
                $this->invoice->updatePaymentStatus();
            }

            return $saved;
        });
    }

    public function markAsFailed(?string $reason = null, ?int $rejectedBy = null): bool
    {
        return \DB::transaction(function () use ($reason, $rejectedBy) {
            $this->status = PaymentStatus::REJECTED;

            if ($reason) {
                $this->notes = ($this->notes ? $this->notes."\n" : '').'Rechazado: '.$reason;
            }

            if ($rejectedBy) {
                $this->notes = ($this->notes ? $this->notes."\n" : '').'Rechazado por: '.User::find($rejectedBy)->full_name.' el '.now()->format('d/m/Y H:i');
            }

            return $this->save();
        });
    }
}
