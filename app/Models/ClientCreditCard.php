<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientCreditCard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'neopayments_customer_id',
        'neopayments_card_id',
        'card_token',
        'card_holder',
        'card_last_four',
        'card_brand',
        'exp_month',
        'exp_year',
        'is_default',
        'is_active',
        'alias',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function getMaskedCardNumber(): string
    {
        return '**** **** **** '.$this->card_last_four;
    }

    public function getExpirationDisplay(): string
    {
        return $this->exp_month.'/'.$this->exp_year;
    }

    public function isExpired(): bool
    {
        $expirationYear = 2000 + (int) $this->exp_year;
        $expirationMonth = (int) $this->exp_month;

        $expirationDate = Carbon::create($expirationYear, $expirationMonth, 1)->endOfMonth();

        return $expirationDate->isPast();
    }

    public function markAsDefault(): void
    {
        self::where('client_id', $this->client_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
