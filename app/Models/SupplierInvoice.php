<?php

namespace App\Models;

use App\Enums\PayableStatus;
use App\Models\Scopes\SupplierInvoiceScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SupplierInvoice extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'client_id',
        'supplier_id',
        'invoice_number',
        'invoice_date',
        'received_date',
        'due_date',
        'currency',
        'subtotal',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'balance',
        'cost_center_id',
        'notes',
        'status',
        'approved_at',
        'approved_by',
        'journal_entry_id',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'uuid' => 'string',
            'invoice_date' => 'date',
            'received_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance' => 'decimal:2',
            'status' => PayableStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new SupplierInvoiceScope);

        static::creating(function ($model) {
            if (! $model->uuid) {
                $model->uuid = Str::uuid();
            }
            if (! $model->client_id && auth()->check()) {
                $model->client_id = auth()->user()->getCurrentClient()?->id;
            }
            if (! $model->balance) {
                $model->balance = $model->total_amount - $model->paid_amount;
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty(['total_amount', 'paid_amount'])) {
                $model->balance = $model->total_amount - $model->paid_amount;
                $model->updateStatus();
            }
        });
    }

    // ===== Relationships =====

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ===== Scopes =====

    public function scopeApproved($query)
    {
        return $query->whereIn('status', ['approved', 'partial', 'paid']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'registered']);
    }

    // ===== Methods =====

    public function updateBalance(): void
    {
        $this->balance = $this->total_amount - $this->paid_amount;
        $this->save();
    }

    public function updateStatus(): void
    {
        if ($this->balance <= 0) {
            $this->status = PayableStatus::PAID;
        } elseif ($this->paid_amount > 0) {
            $this->status = PayableStatus::PARTIAL;
        } elseif (now()->toDateString() > $this->due_date->toDateString() && $this->balance > 0) {
            $this->status = PayableStatus::OVERDUE;
        }

        $this->save();
    }

    public function approve(?int $userId = null): void
    {
        $this->update([
            'status' => PayableStatus::APPROVED,
            'approved_at' => now(),
            'approved_by' => $userId ?? auth()->id(),
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => PayableStatus::CANCELLED]);
    }

    public function getStatusLabel(): string
    {
        return $this->status->label();
    }

    public function isApproved(): bool
    {
        return in_array($this->status, [
            PayableStatus::APPROVED,
            PayableStatus::PARTIAL,
            PayableStatus::PAID,
        ]);
    }

    public function isPaid(): bool
    {
        return $this->status === PayableStatus::PAID;
    }

    public function isOverdue(): bool
    {
        return $this->status === PayableStatus::OVERDUE;
    }

    public function daysOverdue(): int
    {
        if ($this->status !== PayableStatus::OVERDUE) {
            return 0;
        }

        return now()->diffInDays($this->due_date);
    }
}
