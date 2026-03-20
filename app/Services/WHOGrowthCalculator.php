<?php

namespace App\Services;

use App\Models\WHOGrowthReference;
use Illuminate\Support\Facades\Cache;

class WHOGrowthCalculator
{
    private array $cache = [];

    /**
     * Calculate percentile and Z-score for a given measurement using WHO LMS method.
     *
     * @param  string  $measurementType  'weight_for_age', 'height_for_age', 'bmi_for_age', 'head_circumference_for_age'
     * @param  string  $gender  'male' or 'female'
     * @param  int  $ageMonths  Age in months (0-60)
     * @param  float  $value  Measurement value
     * @return array ['percentile' => float, 'zscore' => float]
     */
    public function calculate(string $measurementType, string $gender, int $ageMonths, float $value): array
    {
        if ($ageMonths < 0 || $ageMonths > 60) {
            return ['percentile' => null, 'zscore' => null];
        }

        if ($measurementType === 'head_circumference_for_age' && $ageMonths > 24) {
            return ['percentile' => null, 'zscore' => null];
        }

        $lms = $this->getLMS($gender, $ageMonths, $measurementType);

        if (! $lms) {
            return ['percentile' => null, 'zscore' => null];
        }

        $zscore = $this->calculateZScore($value, $lms['l'], $lms['m'], $lms['s']);
        $percentile = $this->zScoreToPercentile($zscore);

        return [
            'percentile' => round($percentile, 2),
            'zscore' => round($zscore, 2),
        ];
    }

    /**
     * Get LMS parameters from WHO reference data with caching.
     */
    private function getLMS(string $gender, int $ageMonths, string $measurementType): ?array
    {
        $cacheKey = "{$gender}_{$ageMonths}_{$measurementType}";

        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $lms = Cache::remember(
            "who_lms_{$cacheKey}",
            now()->addDays(7),
            fn () => WHOGrowthReference::getLMS($gender, $ageMonths, $measurementType)
        );

        $this->cache[$cacheKey] = $lms;

        return $lms;
    }

    /**
     * Calculate Z-score using WHO LMS method.
     *
     * Z = [(value/M)^L - 1] / (L * S)
     * Special case when L = 0: Z = ln(value/M) / S
     *
     * @param  float  $value  Measured value
     * @param  float  $l  Box-Cox power transformation
     * @param  float  $m  Median
     * @param  float  $s  Coefficient of variation
     */
    private function calculateZScore(float $value, float $l, float $m, float $s): float
    {
        if ($value <= 0 || $m <= 0 || $s <= 0) {
            return 0;
        }

        if (abs($l) < 0.0001) {
            return log($value / $m) / $s;
        }

        return (pow($value / $m, $l) - 1) / ($l * $s);
    }

    /**
     * Convert Z-score to percentile using cumulative normal distribution.
     *
     * Uses an approximation of the cumulative distribution function (CDF)
     * of the standard normal distribution.
     */
    private function zScoreToPercentile(float $zscore): float
    {
        $percentile = $this->normalCDF($zscore) * 100;

        return max(0, min(100, $percentile));
    }

    /**
     * Approximation of the cumulative distribution function (CDF)
     * of the standard normal distribution using Abramowitz and Stegun formula.
     */
    private function normalCDF(float $z): float
    {
        $t = 1 / (1 + 0.2316419 * abs($z));
        $d = 0.3989423 * exp(-$z * $z / 2);
        $p = $d * $t * (0.3193815 + $t * (-0.3565638 + $t * (1.781478 + $t * (-1.821256 + $t * 1.330274))));

        return $z > 0 ? 1 - $p : $p;
    }

    /**
     * Get WHO percentile curves for charting (returns reference values for all ages).
     *
     * @param  int  $maxAgeMonths  Maximum age to include (default 60)
     * @return array Array of ages with corresponding percentile values
     */
    public function getPercentileCurves(string $measurementType, string $gender, int $maxAgeMonths = 60): array
    {
        $maxAge = min($maxAgeMonths, 60);

        if ($measurementType === 'head_circumference_for_age') {
            $maxAge = min($maxAge, 24);
        }

        return Cache::remember(
            "who_percentiles_{$measurementType}_{$gender}_{$maxAge}",
            now()->addDays(30),
            function () use ($measurementType, $gender, $maxAge) {
                $curves = [
                    'ages' => [],
                    'p3' => [],
                    'p10' => [],
                    'p25' => [],
                    'p50' => [],
                    'p75' => [],
                    'p90' => [],
                    'p97' => [],
                ];

                $references = WHOGrowthReference::forGender($gender)
                    ->forMeasurementType($measurementType)
                    ->where('age_months', '<=', $maxAge)
                    ->orderBy('age_months')
                    ->get();

                foreach ($references as $ref) {
                    $curves['ages'][] = $ref->age_months;
                    $curves['p3'][] = $ref->p3;
                    $curves['p10'][] = $ref->p10;
                    $curves['p25'][] = $ref->p25;
                    $curves['p50'][] = $ref->p50;
                    $curves['p75'][] = $ref->p75;
                    $curves['p90'][] = $ref->p90;
                    $curves['p97'][] = $ref->p97;
                }

                return $curves;
            }
        );
    }
}
