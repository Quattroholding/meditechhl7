<?php

namespace App\Models\Treasury;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'destination_type',
        'default_bank_id',
        'default_cash_register_id',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'destination_type' => 'string',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relaciones

    public function defaultBank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'default_bank_id');
    }

    public function defaultCashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class, 'default_cash_register_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Métodos de negocio

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isBank(): bool
    {
        return $this->destination_type === 'bank';
    }

    public function isCash(): bool
    {
        return $this->destination_type === 'cash';
    }

    public function getDestination()
    {
        if ($this->isBank()) {
            return $this->defaultBank;
        }

        return $this->defaultCashRegister;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeBank($query)
    {
        return $query->where('destination_type', 'bank');
    }

    public function scopeCash($query)
    {
        return $query->where('destination_type', 'cash');
    }
}
