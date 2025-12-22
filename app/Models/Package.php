<?php

namespace App\Models;

use App\Enums\BillingPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends BaseModel
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'base_price',
        'max_doctors_included',
        'price_per_extra_doctor',
        'features',
        'is_active',
        'billing_period',
        'billing_period_days',
        'agent_available',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'price_per_extra_doctor' => 'decimal:2',
            'features' => 'array',
            'is_active' => 'boolean',
            'billing_period' => BillingPeriod::class,
            'max_doctors_included' => 'integer',
            'billing_period_days' => 'integer',
        ];
    }

    // Relationships
    public function subscriptions(): HasMany
    {
        return $this->hasMany(ClientSubscription::class);
    }

    public function activeSubscriptions(): HasMany
    {
        return $this->hasMany(ClientSubscription::class)
            ->whereIn('status', ['trial', 'active']);
    }

    // Scopes
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeByBillingPeriod(Builder $query, BillingPeriod $period): void
    {
        $query->where('billing_period', $period->value);
    }

    // Methods
    public function calculatePrice(int $extraDoctors = 0): float
    {
        $basePrice = (float) $this->base_price;
        $extraPrice = $extraDoctors * (float) $this->price_per_extra_doctor;

        return $basePrice + $extraPrice;
    }

    public function isAvailable(): bool
    {
        return $this->is_active === true;
    }

    public function getTotalDoctorsAllowed(int $extraDoctors = 0): int
    {
        return $this->max_doctors_included + $extraDoctors;
    }

    public function features(): array
    {
        $array = [
            'Administracion y Agendamitos de citas',
            'Gestion de Pacientes',
            'Historial Clinico Digital',
            'Creacion de licencias medicas',
            'Dashboard con kpis relevantes',
            'Gestion de cobros de servicios',
        ];

        if($this->agent_available)
            array_push($array, 'Agente automatizado de citas SAMI a traves de nuestro whatsapp. ');

        if($this->id==4)
            array_push($array, 'Agente automatizado de citas a traves de whatsapp personalizado.');

        return $array;
    }
}
