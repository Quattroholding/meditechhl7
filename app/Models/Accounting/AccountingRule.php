<?php

namespace App\Models\Accounting;

use App\Enums\AccountType;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountingRule extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'account_type',
        'nature',
        'requires_cost_center',
        'is_system_account',
        'is_control_account',
        'closes_period',
        'default_debit_account_id',
        'default_credit_account_id',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'account_type' => AccountType::class,
        'nature' => 'string',
        'requires_cost_center' => 'boolean',
        'is_system_account' => 'boolean',
        'is_control_account' => 'boolean',
        'closes_period' => 'boolean',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function defaultDebitAccount(): BelongsTo
    {
        return $this->belongsTo(AccountingAccount::class, 'default_debit_account_id');
    }

    public function defaultCreditAccount(): BelongsTo
    {
        return $this->belongsTo(AccountingAccount::class, 'default_credit_account_id');
    }
}
