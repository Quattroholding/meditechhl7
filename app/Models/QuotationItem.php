<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id',
        'service_type_id',
        'service_name',
        'service_description',
        'quantity',
        'unit_price',
        'subtotal',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->calculateSubtotal();
        });

        static::updating(function ($model) {
            $model->calculateSubtotal();
        });

        static::saved(function ($model) {
            $model->quotation->calculateTotals();
        });

        static::deleted(function ($model) {
            $model->quotation->calculateTotals();
        });
    }

    // Relaciones
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    // Métodos de negocio
    public function calculateSubtotal(): void
    {
        $this->subtotal = $this->quantity * $this->unit_price;
    }

    // Accessors
    public function getFormattedUnitPriceAttribute(): string
    {
        return number_format($this->unit_price, 2);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return number_format($this->subtotal, 2);
    }
}
