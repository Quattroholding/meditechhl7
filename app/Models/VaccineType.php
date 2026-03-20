<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VaccineType extends Model
{
    protected $fillable = [
        'cvx_code',
        'vaccine_name_en',
        'vaccine_name_es',
        'target_disease',
        'min_age_months',
        'max_age_months',
        'doses_required',
        'interval_days',
        'is_active',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'min_age_months' => 'integer',
            'max_age_months' => 'integer',
            'doses_required' => 'integer',
            'interval_days' => 'integer',
            'display_order' => 'integer',
        ];
    }

    public function immunizations(): HasMany
    {
        return $this->hasMany(Immunization::class);
    }

    public function getLocalizedNameAttribute(): string
    {
        return app()->getLocale() === 'es' ? $this->vaccine_name_es : $this->vaccine_name_en;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('vaccine_name_en');
    }

    public function scopeForAge($query, int $ageMonths)
    {
        return $query->where('min_age_months', '<=', $ageMonths)
            ->where(function ($q) use ($ageMonths) {
                $q->whereNull('max_age_months')
                    ->orWhere('max_age_months', '>=', $ageMonths);
            });
    }

    public function isAgeAppropriate(int $ageMonths): bool
    {
        if ($ageMonths < $this->min_age_months) {
            return false;
        }

        if ($this->max_age_months !== null && $ageMonths > $this->max_age_months) {
            return false;
        }

        return true;
    }
}
