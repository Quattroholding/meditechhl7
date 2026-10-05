<?php

namespace App\Models\Finance;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected $casts = [
        'percentage' => 'decimal:2',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relaciones

    public function supplierInvoice(): BelongsTo
    {
        return $this->belongsTo(SupplierInvoice::class);
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    // Métodos de negocio

    public function calculateAmount(float $invoiceTotal): float
    {
        return ($invoiceTotal * $this->percentage) / 100;
    }

    public function updateAmount(float $invoiceTotal): void
    {
        $this->amount = $this->calculateAmount($invoiceTotal);
        $this->save();
    }
}
