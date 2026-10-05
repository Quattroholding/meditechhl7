<?php

namespace App\Models;

use App\Enums\ReceivableStatus;
use App\Models\Scopes\AccountsReceivableScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[ScopedBy([AccountsReceivableScope::class])]
class AccountsReceivable extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'client_id',
        'invoice_id',
        'patient_id',
        'insurance_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'original_amount',
        'paid_amount',
        'balance',
        'cost_center_id',
        'branch_id',
        'status',
        'days_overdue',
        'journal_entry_id',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'uuid' => 'string',
            'invoice_date' => 'date',
            'due_date' => 'date',
            'original_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance' => 'decimal:2',
            'status' => ReceivableStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (! $model->uuid) {
                $model->uuid = Str::uuid();
            }
            if (! $model->client_id && auth()->check()) {
                $model->client_id = auth()->user()->getCurrentClient()?->id;
            }
        });
    }

    // ===== Relationships =====

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function insurance(): BelongsTo
    {
        return $this->belongsTo(Insurance::class);
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
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

    // ===== Methods =====

    public function updateBalance(): void
    {
        $this->update([
            'balance' => $this->original_amount - $this->paid_amount,
        ]);
    }

    public function updateStatus(): void
    {
        if ($this->paid_amount >= $this->original_amount) {
            $this->update(['status' => ReceivableStatus::PAID]);
        } elseif ($this->paid_amount > 0) {
            $this->update(['status' => ReceivableStatus::PARTIAL]);
        } elseif (now()->greaterThan($this->due_date)) {
            $this->update(['status' => ReceivableStatus::OVERDUE]);
        } else {
            $this->update(['status' => ReceivableStatus::PENDING]);
        }
    }

    public function applyPayment(float $amount): void
    {
        $newPaidAmount = $this->paid_amount + $amount;
        $this->update([
            'paid_amount' => $newPaidAmount,
            'balance' => $this->original_amount - $newPaidAmount,
        ]);
        $this->updateStatus();
    }

    public function calculateDaysOverdue(): int
    {
        if ($this->status !== ReceivableStatus::OVERDUE) {
            return 0;
        }

        return now()->diffInDays($this->due_date);
    }

    public function isOverdue(): bool
    {
        return $this->status === ReceivableStatus::OVERDUE || now()->greaterThan($this->due_date);
    }

    public function isPaid(): bool
    {
        return $this->status === ReceivableStatus::PAID;
    }

    public function isPartiallyPaid(): bool
    {
        return $this->status === ReceivableStatus::PARTIAL;
    }

    public function getAmountFormattedAttribute(): string
    {
        return number_format($this->original_amount, 2);
    }

    public function getPaidFormattedAttribute(): string
    {
        return number_format($this->paid_amount, 2);
    }

    public function getBalanceFormattedAttribute(): string
    {
        return number_format($this->balance, 2);
    }
}
