<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Quotation extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fhir_id',
        'quotation_number',
        'client_company_name',
        'client_ruc',
        'client_phone',
        'client_email',
        'client_address',
        'issue_date',
        'expiration_date',
        'currency',
        'subtotal',
        'itbms',
        'itbms_rate',
        'total',
        'status',
        'notes',
        'terms_and_conditions',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'expiration_date' => 'date',
            'subtotal' => 'decimal:2',
            'itbms' => 'decimal:2',
            'itbms_rate' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->fhir_id)) {
                $model->fhir_id = 'quotation-'.Str::uuid();
            }
            if (empty($model->quotation_number)) {
                $model->quotation_number = self::generateQuotationNumber();
            }
            if (empty($model->expiration_date) && ! empty($model->issue_date)) {
                $model->expiration_date = Carbon::parse($model->issue_date)->addDays(30);
            }
        });
    }

    // Relaciones
    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Métodos de negocio
    public static function generateQuotationNumber(): string
    {
        $year = now()->year;
        $lastQuotation = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastQuotation ? ((int) substr($lastQuotation->quotation_number, -5)) + 1 : 1;

        return sprintf('Q-%d-%05d', $year, $number);
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->itbms = $this->subtotal * ($this->itbms_rate / 100);
        $this->total = $this->subtotal + $this->itbms;
        $this->save();
    }

    public function isExpired(): bool
    {
        return $this->expiration_date && $this->expiration_date->isPast();
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'sent']);
    }

    public function markAsSent(): void
    {
        $this->status = 'sent';
        $this->save();
    }

    public function markAsAccepted(): void
    {
        $this->status = 'accepted';
        $this->save();
    }

    public function markAsRejected(): void
    {
        $this->status = 'rejected';
        $this->save();
    }

    public function cancel(): void
    {
        $this->status = 'cancelled';
        $this->save();
    }

    // Accessors
    public function getFormattedSubtotalAttribute(): string
    {
        return number_format($this->subtotal, 2);
    }

    public function getFormattedItbmsAttribute(): string
    {
        return number_format($this->itbms, 2);
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total, 2);
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'draft' => '<span class="badge badge-secondary">Borrador</span>',
            'sent' => '<span class="badge badge-info">Enviada</span>',
            'accepted' => '<span class="badge badge-success">Aceptada</span>',
            'rejected' => '<span class="badge badge-danger">Rechazada</span>',
            'expired' => '<span class="badge badge-warning">Expirada</span>',
            'cancelled' => '<span class="badge badge-dark">Cancelada</span>',
        ];

        return $badges[$this->status] ?? $this->status;
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'draft' => 'Borrador',
            'sent' => 'Enviada',
            'accepted' => 'Aceptada',
            'rejected' => 'Rechazada',
            'expired' => 'Expirada',
            'cancelled' => 'Cancelada',
        ];

        return $labels[$this->status] ?? $this->status;
    }
}
