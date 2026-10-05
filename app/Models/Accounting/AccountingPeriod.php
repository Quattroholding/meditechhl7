<?php

namespace App\Models\Accounting;

use App\Models\BaseModel;
use App\Models\Scopes\AccountingPeriodScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountingPeriod extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'client_id',
        'name',
        'start_date',
        'end_date',
        'fiscal_year',
        'period_number',
        'status',
        'closed_at',
        'closed_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'fiscal_year' => 'integer',
        'period_number' => 'integer',
        'status' => 'string',
        'closed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new AccountingPeriodScope);
    }

    // Relaciones

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
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

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isLocked(): bool
    {
        return $this->status === 'locked';
    }

    public function canModify(): bool
    {
        return $this->isOpen();
    }

    public function getDaysRemaining(): int
    {
        if ($this->isClosed()) {
            return 0;
        }

        return max(0, now()->diffInDays($this->end_date, false));
    }

    public function close(User $user): void
    {
        if (! $this->isOpen()) {
            throw new \Exception('Only open periods can be closed');
        }

        $this->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by' => $user->id,
        ]);
    }

    public function lock(): void
    {
        if (! $this->isClosed()) {
            throw new \Exception('Only closed periods can be locked');
        }

        $this->update(['status' => 'locked']);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeLocked($query)
    {
        return $query->where('status', 'locked');
    }

    public function scopeByFiscalYear($query, int $year)
    {
        return $query->where('fiscal_year', $year);
    }
}
