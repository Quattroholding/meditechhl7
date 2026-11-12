<?php

namespace App\Models\Accounting;

use App\Models\BaseModel;
use App\Models\Client;
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
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'rule_type' => 'string',
        'status' => 'string',
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
}
