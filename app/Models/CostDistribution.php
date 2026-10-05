<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CostDistribution extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'supplier_invoice_id',
        'cost_center_id',
        'percentage',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'uuid' => 'string',
            'percentage' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (! $model->uuid) {
                $model->uuid = Str::uuid();
            }

            // Calcular monto automáticamente si no está asignado
            if (! $model->amount && $model->supplierInvoice) {
                $model->amount = $model->supplierInvoice->total_amount * ($model->percentage / 100);
            }
        });

        static::updating(function ($model) {
            // Recalcular monto si cambia porcentaje o total de factura
            if ($model->isDirty('percentage') && $model->supplierInvoice) {
                $model->amount = $model->supplierInvoice->total_amount * ($model->percentage / 100);
            }
        });
    }

    // ===== Relationships =====

    public function supplierInvoice(): BelongsTo
    {
        return $this->belongsTo(SupplierInvoice::class);
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    // ===== Methods =====

    public function getPercentageFormatted(): string
    {
        return number_format($this->percentage, 2).'%';
    }

    public function getAmountFormatted(): string
    {
        return number_format($this->amount, 2);
    }

    public static function validateDistributions(array $distributions): bool
    {
        $totalPercentage = collect($distributions)->sum('percentage');

        return abs($totalPercentage - 100) < 0.01;
    }
}
