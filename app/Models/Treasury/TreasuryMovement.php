<?php

namespace App\Models\Treasury;

use App\Models\Accounting\JournalEntry;
use App\Models\BaseModel;
use App\Models\Scopes\TreasuryMovementScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreasuryMovement extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'movement_number',
        'movement_date',
        'movement_type',
        'source_type',
        'source_id',
        'bank_id',
        'cash_register_id',
        'amount',
        'description',
        'reference_number',
        'journal_entry_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'movement_date' => 'date',
        'amount' => 'decimal:2',
        'movement_type' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TreasuryMovementScope);
    }

    // Relaciones

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
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

    public function isDeposit(): bool
    {
        return $this->movement_type === 'deposit';
    }

    public function isWithdrawal(): bool
    {
        return $this->movement_type === 'withdrawal';
    }

    public function isTransfer(): bool
    {
        return $this->movement_type === 'transfer';
    }

    public function isAdjustment(): bool
    {
        return $this->movement_type === 'adjustment';
    }

    public function getSource()
    {
        if (! $this->source_type) {
            return null;
        }

        $modelClass = 'App\\Models\\'.$this->source_type;
        if (class_exists($modelClass)) {
            return $modelClass::find($this->source_id);
        }

        return null;
    }

    public function scopeDeposit($query)
    {
        return $query->where('movement_type', 'deposit');
    }

    public function scopeWithdrawal($query)
    {
        return $query->where('movement_type', 'withdrawal');
    }

    public function scopeTransfer($query)
    {
        return $query->where('movement_type', 'transfer');
    }

    public function scopeAdjustment($query)
    {
        return $query->where('movement_type', 'adjustment');
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('movement_date', $date);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('movement_date', [$startDate, $endDate]);
    }
}
