<?php

namespace App\Models\Treasury;

use App\Models\Accounting\AccountingAccount;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bank extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'bank_name',
        'account_number',
        'account_type',
        'currency',
        'accounting_account_id',
        'balance',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'account_type' => 'string',
        'balance' => 'decimal:2',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relaciones

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

    public function treasuryMovements(): HasMany
    {
        return $this->hasMany(TreasuryMovement::class);
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class, 'default_bank_id');
    }

    // Métodos de negocio

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    public function isChecking(): bool
    {
        return $this->account_type === 'checking';
    }

    public function isSavings(): bool
    {
        return $this->account_type === 'savings';
    }

    public function deposit(float $amount, string $description, User $user, $reference = null): TreasuryMovement
    {
        $this->balance += $amount;
        $this->save();

        return TreasuryMovement::create([
            'client_id' => $this->client_id,
            'movement_number' => $this->generateMovementNumber(),
            'movement_date' => now()->toDateString(),
            'movement_type' => 'deposit',
            'bank_id' => $this->id,
            'amount' => $amount,
            'description' => $description,
            'reference_number' => $reference,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    public function withdraw(float $amount, string $description, User $user, $reference = null): TreasuryMovement
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient funds');
        }

        $this->balance -= $amount;
        $this->save();

        return TreasuryMovement::create([
            'client_id' => $this->client_id,
            'movement_number' => $this->generateMovementNumber(),
            'movement_date' => now()->toDateString(),
            'movement_type' => 'withdrawal',
            'bank_id' => $this->id,
            'amount' => $amount,
            'description' => $description,
            'reference_number' => $reference,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    protected function generateMovementNumber(): string
    {
        return 'BANK-'.$this->id.'-'.time();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeChecking($query)
    {
        return $query->where('account_type', 'checking');
    }

    public function scopeSavings($query)
    {
        return $query->where('account_type', 'savings');
    }

    public function getBalanceNameAttribute()
    {
        return $this->bank_name.' ($'.number_format($this->balance, 2).')';
    }
}
