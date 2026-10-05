<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class JournalEntryLine extends BaseModel
{
    protected $table = 'journal_entry_lines';

    protected $fillable = [
        'uuid',
        'journal_entry_id',
        'accounting_account_id',
        'cost_center_id',
        'branch_id',
        'debit',
        'credit',
        'description',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (! $model->uuid) {
                $model->uuid = Str::uuid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'debit' => 'decimal:2',
            'credit' => 'decimal:2',
        ];
    }

    // ===== Relationships =====

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function accountingAccount(): BelongsTo
    {
        return $this->belongsTo(AccountingAccount::class);
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // ===== Methods =====

    public function getAmount(): float
    {
        return max((float) $this->debit, (float) $this->credit);
    }

    public function isDebit(): bool
    {
        return $this->debit > 0;
    }

    public function isCredit(): bool
    {
        return $this->credit > 0;
    }
}
