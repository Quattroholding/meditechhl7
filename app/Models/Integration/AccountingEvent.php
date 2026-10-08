<?php

namespace App\Models\Integration;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountingEvent extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'module',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones

    public function configs(): HasMany
    {
        return $this->hasMany(AccountingEventConfig::class);
    }

    // Métodos de negocio

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getConfigForClient(int $clientId): ?AccountingEventConfig
    {
        return $this->configs()
            ->where('client_id', $clientId)
            ->where('status', 'active')
            ->first();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }
}
