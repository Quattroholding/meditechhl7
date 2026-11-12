<?php

namespace App\Models\Accounting;

use App\Models\BaseModel;
use App\Models\Client;
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
        'deleted_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

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
}
