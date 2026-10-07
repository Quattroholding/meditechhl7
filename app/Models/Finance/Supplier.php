<?php

namespace App\Models\Finance;

use App\Models\Accounting\AccountingAccount;
use App\Models\BaseModel;
use App\Models\Client;
use App\Models\Scopes\SupplierScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends BaseModel
{
    use HasFactory;

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

    protected $casts = [
        'credit_days' => 'integer',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new SupplierScope);
    }

    // Relaciones

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function accountingAccount(): BelongsTo
    {
        return $this->belongsTo(AccountingAccount::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(SupplierInvoice::class);
    }

    // Métodos de negocio

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getTotalPayable(): float
    {
        return $this->invoices()->whereNotIn('status', ['paid', 'cancelled'])->sum('balance');
    }

    public function getPendingBalance(): float
    {
        return (float) $this->invoices()
            ->whereIn('status', ['registered', 'approved', 'partial', 'overdue'])
            ->sum('balance');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getFullName(): string
    {
        return $this->commercial_name ?? $this->legal_name;
    }
}
