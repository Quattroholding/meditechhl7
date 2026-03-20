<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WHOGrowthReference extends Model
{
    protected $table = 'who_growth_references';

    protected $fillable = [
        'gender',
        'age_months',
        'measurement_type',
        'l',
        'm',
        's',
        'p3',
        'p10',
        'p25',
        'p50',
        'p75',
        'p90',
        'p97',
    ];

    protected function casts(): array
    {
        return [
            'age_months' => 'integer',
            'l' => 'decimal:6',
            'm' => 'decimal:4',
            's' => 'decimal:6',
            'p3' => 'decimal:2',
            'p10' => 'decimal:2',
            'p25' => 'decimal:2',
            'p50' => 'decimal:2',
            'p75' => 'decimal:2',
            'p90' => 'decimal:2',
            'p97' => 'decimal:2',
        ];
    }

    public function scopeForGender($query, string $gender)
    {
        return $query->where('gender', $gender);
    }

    public function scopeForAge($query, int $ageMonths)
    {
        return $query->where('age_months', $ageMonths);
    }

    public function scopeForMeasurementType($query, string $type)
    {
        return $query->where('measurement_type', $type);
    }

    public static function getLMS(string $gender, int $ageMonths, string $measurementType): ?array
    {
        // Try exact match first
        $ref = static::forGender($gender)
            ->forAge($ageMonths)
            ->forMeasurementType($measurementType)
            ->first();

        if ($ref) {
            return [
                'l' => (float) $ref->l,
                'm' => (float) $ref->m,
                's' => (float) $ref->s,
            ];
        }

        // If no exact match, try linear interpolation between nearest ages
        $before = static::forGender($gender)
            ->forMeasurementType($measurementType)
            ->where('age_months', '<', $ageMonths)
            ->orderBy('age_months', 'desc')
            ->first();

        $after = static::forGender($gender)
            ->forMeasurementType($measurementType)
            ->where('age_months', '>', $ageMonths)
            ->orderBy('age_months', 'asc')
            ->first();

        if (! $before || ! $after) {
            return null;
        }

        // Linear interpolation
        $fraction = ($ageMonths - $before->age_months) / ($after->age_months - $before->age_months);

        return [
            'l' => (float) $before->l + ($after->l - $before->l) * $fraction,
            'm' => (float) $before->m + ($after->m - $before->m) * $fraction,
            's' => (float) $before->s + ($after->s - $before->s) * $fraction,
        ];
    }
}
