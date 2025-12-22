<?php

namespace App\Models;

use App\Enums\InvoiceItemType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientInvoiceItem extends Model
{
    protected $fillable = [
        'client_invoice_id',
        'description',
        'quantity',
        'unit_price',
        'total',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'total' => 'decimal:2',
            'type' => InvoiceItemType::class,
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (! $item->total) {
                $item->total = $item->quantity * $item->unit_price;
            }
        });

        static::updating(function ($item) {
            $item->total = $item->quantity * $item->unit_price;
        });
    }

    // Relationships
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(ClientInvoice::class, 'client_invoice_id');
    }

    // Scopes
    public function scopeByType(Builder $query, InvoiceItemType $type): void
    {
        $query->where('type', $type->value);
    }
}
