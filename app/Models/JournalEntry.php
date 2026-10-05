<?php

namespace App\Models;

use App\Enums\JournalEntryStatus;
use App\Models\Scopes\JournalEntryScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class JournalEntry extends BaseModel
{
    use HasFactory, SoftDeletes;

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

    protected function casts(): array
    {
        return [
            'uuid' => 'string',
            'entry_date' => 'date',
            'status' => JournalEntryStatus::class,
            'posted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new JournalEntryScope);

        static::creating(function ($model) {
            if (! $model->uuid) {
                $model->uuid = Str::uuid();
            }
            if (! $model->client_id && auth()->check()) {
                $model->client_id = auth()->user()->getCurrentClient()?->id;
            }
            if (! $model->entry_number) {
                $model->generateEntryNumber();
            }
        });
    }

    // ===== Relationships =====

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function accountingPeriod(): BelongsTo
    {
        return $this->belongsTo(AccountingPeriod::class);
    }

    public function lines(): HasMany
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

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function reversedEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'reversed_entry_id');
    }

    // ===== Scopes =====

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    public function scopeReversed($query)
    {
        return $query->where('status', 'reversed');
    }

    // ===== Methods =====

    public function generateEntryNumber(): void
    {
        $year = now()->year;
        $count = static::where('client_id', $this->client_id)
            ->whereYear('entry_date', $year)
            ->count() + 1;

        $this->entry_number = sprintf('JE-%d-%06d', $year, $count);
    }

    public function getTotalDebit(): float
    {
        return (float) $this->lines()->sum('debit');
    }

    public function getTotalCredit(): float
    {
        return (float) $this->lines()->sum('credit');
    }

    public function isBalanced(): bool
    {
        return abs($this->getTotalDebit() - $this->getTotalCredit()) < 0.01;
    }

    public function post(?int $userId = null): void
    {
        if (! $this->isBalanced()) {
            throw new \Exception('No se puede contabilizar: asiento desbalanceado');
        }

        if (! $this->accountingPeriod->isOpen()) {
            throw new \Exception('No se puede contabilizar: período contable cerrado');
        }

        // Actualizar balances de cuentas
        foreach ($this->lines as $line) {
            $line->accountingAccount->updateBalance();
        }

        $this->update([
            'status' => JournalEntryStatus::POSTED,
            'posted_at' => now(),
            'posted_by' => $userId ?? auth()->id(),
        ]);
    }

    public function reverse(?string $reason = null): JournalEntry
    {
        if ($this->status !== JournalEntryStatus::POSTED) {
            throw new \Exception('Solo se pueden revertir asientos contabilizados');
        }

        // Crear asiento inverso
        $reversedEntry = static::create([
            'client_id' => $this->client_id,
            'entry_date' => now()->toDateString(),
            'document_type' => 'reversed',
            'document_number' => $this->entry_number,
            'description' => "Reversión: {$reason}",
            'accounting_period_id' => $this->accounting_period_id,
            'status' => JournalEntryStatus::POSTED,
            'posted_at' => now(),
            'posted_by' => auth()->id(),
            'created_by' => auth()->id(),
        ]);

        // Crear líneas inversas
        foreach ($this->lines as $line) {
            JournalEntryLine::create([
                'journal_entry_id' => $reversedEntry->id,
                'accounting_account_id' => $line->accounting_account_id,
                'cost_center_id' => $line->cost_center_id,
                'branch_id' => $line->branch_id,
                'debit' => $line->credit,
                'credit' => $line->debit,
                'description' => $line->description ? "Reversión: {$line->description}" : null,
            ]);
        }

        // Marcar original como revertido
        $this->update([
            'status' => JournalEntryStatus::REVERSED,
            'reversed_entry_id' => $reversedEntry->id,
        ]);

        // Actualizar balances
        foreach ($reversedEntry->lines as $line) {
            $line->accountingAccount->updateBalance();
        }

        return $reversedEntry;
    }

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
}
