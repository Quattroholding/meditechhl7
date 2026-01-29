<?php

namespace App\Models;

use App\Enums\PreferenceType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientPreference extends Model
{
    protected $fillable = [
        'client_id',
        'preference_type',
        'preference_key',
        'preference_value',
        'description',
        'metadata',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'preference_type' => PreferenceType::class,
            'preference_value' => 'array',
            'metadata' => 'array',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Scopes
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeByType(Builder $query, PreferenceType|string $type): void
    {
        $value = $type instanceof PreferenceType ? $type->value : $type;
        $query->where('preference_type', $value);
    }

    public function scopeByKey(Builder $query, string $key): void
    {
        $query->where('preference_key', $key);
    }

    public function scopeForClient(Builder $query, int $clientId): void
    {
        $query->where('client_id', $clientId);
    }

    // Helper Methods
    public static function get(int $clientId, PreferenceType|string $type, string $key, mixed $default = null): mixed
    {
        $preference = static::forClient($clientId)
            ->byType($type)
            ->byKey($key)
            ->active()
            ->first();

        return $preference ? $preference->preference_value : $default;
    }

    public static function set(int $clientId, PreferenceType|string $type, string $key, mixed $value, ?string $description = null): self
    {
        return static::updateOrCreate(
            [
                'client_id' => $clientId,
                'preference_type' => $type instanceof PreferenceType ? $type->value : $type,
                'preference_key' => $key,
            ],
            [
                'preference_value' => $value,
                'description' => $description,
                'is_active' => true,
            ]
        );
    }

    public static function remove(int $clientId, PreferenceType|string $type, string $key): bool
    {
        return static::forClient($clientId)
            ->byType($type)
            ->byKey($key)
            ->delete();
    }

    public static function getInvoiceTemplate(int $clientId, string $default = 'template_1'): string
    {
        $value = static::get($clientId, PreferenceType::INVOICE_TEMPLATE, 'template_name');

        return $value['template'] ?? $default;
    }

    public static function setInvoiceTemplate(int $clientId, string $template): self
    {
        return static::set(
            $clientId,
            PreferenceType::INVOICE_TEMPLATE,
            'template_name',
            ['template' => $template],
            'Plantilla de factura seleccionada por el cliente'
        );
    }

    public static function getMedicalLeaveTemplate(int $clientId, string $default = 'medical_leave_1'): string
    {
        $value = static::get($clientId, PreferenceType::MEDICAL_LEAVE_TEMPLATE, 'template_name');

        return $value['template'] ?? $default;
    }

    public static function setMedicalLeaveTemplate(int $clientId, string $template): self
    {
        return static::set(
            $clientId,
            PreferenceType::MEDICAL_LEAVE_TEMPLATE,
            'template_name',
            ['template' => $template],
            'Plantilla de licencia médica seleccionada por el cliente'
        );
    }
}
