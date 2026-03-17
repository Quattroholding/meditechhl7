<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HemoScreenStandaloneResult extends Model
{
    protected $table = 'hemoscreen_standalone_results';

    protected $fillable = [
        'user_id',
        'practitioner_id',
        'scb_id',
        'device_serial',
        'patient_identifier',
        'message_control_id',
        'message_type',
        'panel_code',
        'panel_name',
        'observations',
        'raw_message',
        'test_performed_at',
    ];

    protected function casts(): array
    {
        return [
            'observations' => 'array',
            'raw_message' => 'array',
            'test_performed_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the result
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the practitioner that owns the result
     */
    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }

    /**
     * Get the client (scb_id) that owns the result
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'scb_id');
    }

    /**
     * Scope to filter results for a specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter recent results (default 30 days)
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('test_performed_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to filter by device serial
     */
    public function scopeByDevice($query, string $serial)
    {
        return $query->where('device_serial', $serial);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('test_performed_at', [$startDate, $endDate]);
    }

    /**
     * Scope to order by most recent first
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderBy('test_performed_at', 'desc');
    }

    /**
     * Get a specific observation by LOINC code
     */
    public function getObservationByCode(string $code): ?array
    {
        $observations = $this->observations ?? [];
        foreach ($observations as $obs) {
            if (isset($obs['code']) && $obs['code'] === $code) {
                return $obs;
            }
        }

        return null;
    }

    /**
     * Check if any observation values are abnormal (outside reference ranges)
     */
    public function hasAbnormalValues(): bool
    {
        $referenceRanges = $this->getReferenceRanges();
        $observations = $this->observations ?? [];

        foreach ($observations as $obs) {
            $code = $obs['code'] ?? null;
            $value = $obs['value'] ?? null;

            if ($code && $value !== null && isset($referenceRanges[$code])) {
                $range = $referenceRanges[$code];
                if ($value < $range['min'] || $value > $range['max']) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get reference ranges for CBC parameters
     */
    public function getReferenceRanges(): array
    {
        return [
            '718-7' => ['min' => 13.5, 'max' => 17.5, 'unit' => 'g/dL', 'name' => 'Hemoglobin'],
            '789-8' => ['min' => 4.5, 'max' => 5.9, 'unit' => '10^12/L', 'name' => 'RBC'],
            '6690-2' => ['min' => 4.5, 'max' => 11.0, 'unit' => '10^9/L', 'name' => 'WBC'],
            '20570-8' => ['min' => 38.8, 'max' => 50.0, 'unit' => '%', 'name' => 'Hematocrit'],
            '787-2' => ['min' => 80.0, 'max' => 100.0, 'unit' => 'fL', 'name' => 'MCV'],
            '785-6' => ['min' => 27.0, 'max' => 32.0, 'unit' => 'pg', 'name' => 'MCH'],
            '786-4' => ['min' => 32.0, 'max' => 36.0, 'unit' => 'g/dL', 'name' => 'MCHC'],
            '777-3' => ['min' => 150.0, 'max' => 400.0, 'unit' => '10^9/L', 'name' => 'Platelets'],
            '751-8' => ['min' => 40.0, 'max' => 70.0, 'unit' => '%', 'name' => 'Neutrophils'],
            '736-9' => ['min' => 20.0, 'max' => 40.0, 'unit' => '%', 'name' => 'Lymphocytes'],
            '5905-5' => ['min' => 4.0, 'max' => 8.0, 'unit' => '%', 'name' => 'Monocytes'],
            '713-8' => ['min' => 1.0, 'max' => 4.0, 'unit' => '%', 'name' => 'Eosinophils'],
            '706-2' => ['min' => 0.0, 'max' => 1.0, 'unit' => '%', 'name' => 'Basophils'],
        ];
    }

    /**
     * Check if a specific observation value is abnormal
     */
    public function isAbnormal(string $code, float $value): bool
    {
        $referenceRanges = $this->getReferenceRanges();

        if (! isset($referenceRanges[$code])) {
            return false;
        }

        $range = $referenceRanges[$code];

        return $value < $range['min'] || $value > $range['max'];
    }

    /**
     * Get all abnormal observations
     */
    public function getAbnormalObservations(): array
    {
        $abnormal = [];
        $observations = $this->observations ?? [];

        foreach ($observations as $obs) {
            $code = $obs['code'] ?? null;
            $value = $obs['value'] ?? null;

            if ($code && $value !== null && $this->isAbnormal($code, (float) $value)) {
                $abnormal[] = $obs;
            }
        }

        return $abnormal;
    }
}
