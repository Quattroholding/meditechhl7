<?php

namespace App\Models\Finance;

use App\Models\Accounting\JournalEntry;
use App\Models\BaseModel;
use App\Models\Branch;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Scopes\AccountsReceivableScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountsReceivable extends BaseModel
{
    use HasFactory;

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

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'original_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'days_overdue' => 'integer',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new AccountsReceivableScope);
    }

    // Relaciones

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
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

    // Métodos de negocio

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue' || (now()->isAfter($this->due_date) && ! $this->isPaid());
    }

    public function calculateDaysOverdue(): int
    {
        if ($this->isPaid()) {
            return 0;
        }

        $daysOverdue = now()->diffInDays($this->due_date);

        return max(0, $daysOverdue);
    }

    public function recordPayment(float $amount): void
    {
        $this->paid_amount += $amount;
        $this->balance = $this->original_amount - $this->paid_amount;

        if ($this->balance <= 0) {
            $this->status = 'paid';
            $this->balance = 0;
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        }

        $this->days_overdue = $this->calculateDaysOverdue();
        $this->save();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }
}
