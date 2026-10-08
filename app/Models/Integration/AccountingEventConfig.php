<?php

namespace App\Models\Integration;

use App\Models\Accounting\AccountingAccount;
use App\Models\BaseModel;
use App\Models\Finance\CostCenter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountingEventConfig extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'accounting_event_id',
        'debit_account_id',
        'credit_account_id',
        'default_cost_center_id',
        'auto_generate',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'auto_generate' => 'boolean',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones

    public function accountingEvent(): BelongsTo
    {
        return $this->belongsTo(AccountingEvent::class);
    }

    public function debitAccount(): BelongsTo
    {
        return $this->belongsTo(AccountingAccount::class, 'debit_account_id');
    }

    public function creditAccount(): BelongsTo
    {
        return $this->belongsTo(AccountingAccount::class, 'credit_account_id');
    }

    public function defaultCostCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class, 'default_cost_center_id');
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

    public function shouldAutoGenerate(): bool
    {
        return $this->auto_generate === true && $this->isActive();
    }

    public function isValid(): bool
    {
        return $this->debit_account_id !== $this->credit_account_id
            && $this->debitAccount()->exists()
            && $this->creditAccount()->exists();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAutoGenerate($query)
    {
        return $query->where('auto_generate', true)->where('status', 'active');
    }
}
