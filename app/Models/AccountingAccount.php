<?php

namespace App\Models;

use App\Enums\AccountType;
use App\Models\Scopes\AccountingAccountScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AccountingAccount extends BaseModel
{
    use HasFactory, SoftDeletes;

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

    protected function casts(): array
    {
        return [
            'uuid' => 'string',
            'account_type' => AccountType::class,
            'allows_transaction' => 'boolean',
            'balance' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new AccountingAccountScope);

        static::creating(function ($model) {
            if (! $model->uuid) {
                $model->uuid = Str::uuid();
            }
            if (! $model->client_id && auth()->check()) {
                $model->client_id = auth()->user()->getCurrentClient()?->id;
            }
            if (! $model->level) {
                $model->calculateLevel();
            }
        });
    }

    // ===== Relationships =====

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AccountingAccount::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(AccountingAccount::class, 'parent_id');
    }

    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
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

    public function scopeByType($query, AccountType $type)
    {
        return $query->where('account_type', $type);
    }

    // ===== Methods =====

    public function calculateLevel(): void
    {
        if ($this->parent_id) {
            $parent = AccountingAccount::find($this->parent_id);
            $this->level = ($parent->level ?? 1) + 1;
        } else {
            $this->level = 1;
        }
    }

    public function getHierarchyPath(): string
    {
        $path = "{$this->code} - {$this->name}";

        if ($this->parent) {
            $path = $this->parent->getHierarchyPath() . ' > ' . $path;
        }

        return $path;
    }

    public function updateBalance(): void
    {
        $debit = $this->journalEntryLines()->sum('debit');
        $credit = $this->journalEntryLines()->sum('credit');

        if ($this->account_type->isDebitNormal()) {
            $this->balance = $debit - $credit;
        } else {
            $this->balance = $credit - $debit;
        }

        $this->save();
    }

    public function canDelete(): bool
    {
        return $this->journalEntryLines()->count() === 0 &&
               $this->children()->count() === 0;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
