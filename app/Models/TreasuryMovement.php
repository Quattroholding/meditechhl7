<?php

namespace App\Models;

use App\Enums\TreasuryMovementType;
use App\Models\Scopes\TreasuryMovementScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class TreasuryMovement extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'client_id',
        'movement_number',
        'movement_date',
        'movement_type',
        'source_type',
        'source_id',
        'amount',
        'description',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'uuid' => 'string',
            'movement_date' => 'date',
            'movement_type' => TreasuryMovementType::class,
            'amount' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new TreasuryMovementScope);

        static::creating(function ($model) {
            if (! $model->uuid) {
                $model->uuid = Str::uuid();
            }
            if (! $model->client_id && auth()->check()) {
                $model->client_id = auth()->user()->getCurrentClient()?->id;
            }
            if (! $model->movement_number) {
                $model->generateMovementNumber();
            }
        });
    }

    // ===== Relationships =====

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function paymentSchedules(): HasMany
    {
        return $this->hasMany(PaymentSchedule::class);
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

    public function generateMovementNumber(): void
    {
        $year = now()->year;
        $count = static::where('client_id', $this->client_id ?? auth()->user()->getCurrentClient()->id)
            ->whereYear('movement_date', $year)
            ->count() + 1;

        $this->movement_number = sprintf('TM-%d-%06d', $year, $count);
    }

    public function isIncome(): bool
    {
        return $this->movement_type === TreasuryMovementType::INCOME;
    }

    public function isExpense(): bool
    {
        return $this->movement_type === TreasuryMovementType::EXPENSE;
    }

    public function isTransfer(): bool
    {
        return $this->movement_type === TreasuryMovementType::TRANSFER;
    }
}
