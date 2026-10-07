<?php

namespace App\Models\Accounting;

use App\Models\BaseModel;
use App\Models\Scopes\AccountingAccountScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountingAccount extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'client_id',
        'code',
        'name',
        'description',
        'account_type',
        'parent_id',
        'level',
        'allows_transaction',
        'status',
        'balance',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'account_type' => 'string',
        'level' => 'integer',
        'allows_transaction' => 'boolean',
        'status' => 'string',
        'balance' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new AccountingAccountScope);
    }

    // Relaciones

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AccountingAccount::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(AccountingAccount::class, 'parent_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    // Métodos de negocio

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isAsset(): bool
    {
        return $this->account_type === 'asset';
    }

    public function isLiability(): bool
    {
        return $this->account_type === 'liability';
    }

    public function isEquity(): bool
    {
        return $this->account_type === 'equity';
    }

    public function isIncome(): bool
    {
        return $this->account_type === 'income';
    }

    public function isExpense(): bool
    {
        return $this->account_type === 'expense';
    }

    public function isCost(): bool
    {
        return $this->account_type === 'cost';
    }

    public function updateBalance(float $amount, string $type = 'debit'): void
    {
        if ($type === 'debit') {
            $this->balance += $amount;
        } else {
            $this->balance -= $amount;
        }
        $this->save();
    }

    /**
     * Recalcula el balance desde todas las líneas del diario asociadas
     */
    public function recalculateBalance(): void
    {
        $balance = 0;

        $lines = $this->journalEntryLines()->get();

        foreach ($lines as $line) {
            if ($line->debit > 0) {
                $balance += $line->debit;
            } else {
                $balance -= $line->credit;
            }
        }

        $this->update(['balance' => $balance]);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('account_type', $type);
    }

    public function getAllChildren()
    {
        return $this->children()->with('children')->get();
    }
}
