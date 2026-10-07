<?php

namespace App\Models\Accounting;

use App\Enums\JournalEntryStatus;
use App\Models\BaseModel;
use App\Models\Scopes\JournalEntryScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'client_id',
        'entry_number',
        'entry_date',
        'document_type',
        'document_number',
        'description',
        'status',
        'accounting_period_id',
        'source_type',
        'source_id',
        'reversed_entry_id',
        'posted_at',
        'posted_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'status' => JournalEntryStatus::class,
        'posted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new JournalEntryScope);
    }

    // Relaciones

    public function accountingPeriod(): BelongsTo
    {
        return $this->belongsTo(AccountingPeriod::class);
    }

    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function reversedEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'reversed_entry_id');
    }

    public function reversingEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'reversed_entry_id');
    }

    // Métodos de negocio

    public function isDraft(): bool
    {
        return $this->status === JournalEntryStatus::DRAFT;
    }

    public function isPosted(): bool
    {
        return $this->status === JournalEntryStatus::POSTED;
    }

    public function isReversed(): bool
    {
        return $this->status === JournalEntryStatus::REVERSED;
    }

    public function getTotalDebit(): float
    {
        return $this->journalEntryLines()->sum('debit');
    }

    public function getTotalCredit(): float
    {
        return $this->journalEntryLines()->sum('credit');
    }

    public function isBalanced(): bool
    {
        return abs($this->getTotalDebit() - $this->getTotalCredit()) < 0.01;
    }

    public function post(User $user): void
    {
        if (! $this->isBalanced()) {
            throw new \Exception('Journal entry is not balanced');
        }

        $this->update([
            'status' => JournalEntryStatus::POSTED,
            'posted_at' => now(),
            'posted_by' => $user->id,
        ]);

        // Update account balances
        foreach ($this->journalEntryLines as $line) {
            $account = $line->accountingAccount;
            if ($line->debit > 0) {
                $account->updateBalance($line->debit, 'debit');
            }
            if ($line->credit > 0) {
                $account->updateBalance($line->credit, 'credit');
            }
        }
    }

    public function reverse(User $user): void
    {
        if (! $this->isPosted()) {
            throw new \Exception('Only posted entries can be reversed');
        }

        // Create reversing entry
        $reversingEntry = JournalEntry::create([
            'client_id' => $this->client_id,
            'entry_number' => 'REV-'.$this->entry_number,
            'entry_date' => now()->toDateString(),
            'document_type' => $this->document_type,
            'document_number' => $this->document_number,
            'description' => 'Reversal of '.$this->entry_number,
            'status' => JournalEntryStatus::POSTED,
            'accounting_period_id' => $this->accounting_period_id,
            'source_type' => $this->source_type,
            'source_id' => $this->source_id,
            'reversed_entry_id' => $this->id,
            'posted_at' => now(),
            'posted_by' => $user->id,
            'created_by' => $user->id,
        ]);

        // Create reversing lines
        foreach ($this->journalEntryLines as $line) {
            JournalEntryLine::create([
                'journal_entry_id' => $reversingEntry->id,
                'accounting_account_id' => $line->accounting_account_id,
                'cost_center_id' => $line->cost_center_id,
                'branch_id' => $line->branch_id,
                'debit' => $line->credit,
                'credit' => $line->debit,
                'description' => $line->description,
            ]);
        }

        // Update status
        $this->update(['status' => JournalEntryStatus::REVERSED]);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', JournalEntryStatus::DRAFT->value);
    }

    public function scopePosted($query)
    {
        return $query->where('status', JournalEntryStatus::POSTED->value);
    }
}
