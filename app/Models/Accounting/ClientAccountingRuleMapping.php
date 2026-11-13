<?php

namespace App\Models\Accounting;

use App\Models\BaseModel;
use App\Models\Client;
use App\Models\MedicalSpeciality;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientAccountingRuleMapping extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'rule_type',
        'accounting_rule_id',
        'accounting_account_id',
        'status',
        'medical_speciality_id',
        'payment_source',
        'priority',
        'is_default',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'rule_type' => 'string',
        'status' => 'string',
        'payment_source' => 'string',
        'is_default' => 'boolean',
        'priority' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function accountingRule(): BelongsTo
    {
        return $this->belongsTo(AccountingRule::class);
    }

    public function accountingAccount(): BelongsTo
    {
        return $this->belongsTo(AccountingAccount::class);
    }

    public function medicalSpeciality(): BelongsTo
    {
        return $this->belongsTo(MedicalSpeciality::class);
    }

    /**
     * Scope para obtener la regla de ingresos aplicable.
     * Primero busca regla específica por especialidad, luego default.
     */
    public function scopeForIncome(Builder $query, int $clientId, ?int $specialtyId = null): Builder
    {
        return $query
            ->where('client_id', $clientId)
            ->where('rule_type', 'income')
            ->where('status', 'active')
            ->where(function (Builder $q) use ($specialtyId) {
                // Primero buscar regla específica para la especialidad
                $q->where('medical_speciality_id', $specialtyId)
                    // Si no existe, buscar regla default
                    ->orWhere(function (Builder $q2) {
                        $q2->whereNull('medical_speciality_id')
                            ->where('is_default', true);
                    });
            })
            ->orderByRaw(
                'CASE
                    WHEN medical_speciality_id IS NOT NULL THEN 1
                    ELSE 2
                END'
            );
    }

    /**
     * Scope para obtener la regla de cuentas por cobrar aplicable.
     * Primero busca regla específica por payment_source, luego default.
     */
    public function scopeForReceivable(Builder $query, int $clientId, ?string $paymentSource = null): Builder
    {
        return $query
            ->where('client_id', $clientId)
            ->where('rule_type', 'receivable')
            ->where('status', 'active')
            ->where(function (Builder $q) use ($paymentSource) {
                // Primero buscar regla específica para payment_source
                $q->where('payment_source', $paymentSource)
                    // Si no existe, buscar regla default
                    ->orWhere(function (Builder $q2) {
                        $q2->whereNull('payment_source')
                            ->where('is_default', true);
                    });
            })
            ->orderByRaw(
                'CASE
                    WHEN payment_source IS NOT NULL THEN 1
                    ELSE 2
                END'
            );
    }
}
