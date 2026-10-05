<?php

namespace App\Models;

use App\Models\Scopes\SupplierScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Supplier extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'client_id',
        'ruc',
        'dv',
        'legal_name',
        'commercial_name',
        'address',
        'phone',
        'email',
        'contact_person',
        'credit_days',
        'accounting_account_id',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'uuid' => 'string',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new SupplierScope);

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

    public function accountingAccount(): BelongsTo
    {
        return $this->belongsTo(AccountingAccount::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(SupplierInvoice::class);
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

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    // ===== Methods =====

    public function getFullName(): string
    {
        return $this->commercial_name ?? $this->legal_name;
    }

    public function getRucDv(): string
    {
        return "{$this->ruc}-{$this->dv}";
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getPendingBalance(): float
    {
        return (float) $this->invoices()
            ->whereIn('status', ['registered', 'approved', 'partial', 'overdue'])
            ->sum('balance');
    }
}
