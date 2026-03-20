<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Immunization extends BaseModel
{
    protected $fillable = [
        'fhir_id',
        'patient_id',
        'encounter_id',
        'practitioner_id',
        'vaccine_type_id',
        'client_id',
        'branch_id',
        'consulting_room_id',
        'vaccine_code',
        'vaccine_display',
        'occurrence_datetime',
        'status',
        'lot_number',
        'expiration_date',
        'manufacturer',
        'route_code',
        'site_code',
        'dose_quantity',
        'dose_unit',
        'dose_number',
        'series_doses',
        'reaction',
        'note',
        'primary_source',
    ];

    protected function casts(): array
    {
        return [
            'occurrence_datetime' => 'datetime',
            'expiration_date' => 'date',
            'reaction' => 'array',
            'primary_source' => 'boolean',
            'dose_quantity' => 'decimal:2',
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->fhir_id)) {
                $model->fhir_id = 'immunization-'.Str::uuid();
            }
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }

    public function vaccineType(): BelongsTo
    {
        return $this->belongsTo(VaccineType::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function consultingRoom(): BelongsTo
    {
        return $this->belongsTo(ConsultingRoom::class);
    }

    public function getAgeAtVaccinationAttribute(): int
    {
        if (! $this->patient || ! $this->patient->birth_date) {
            return 0;
        }

        $birthDate = Carbon::parse($this->patient->birth_date);
        $vaccinationDate = Carbon::parse($this->occurrence_datetime);

        return $birthDate->diffInMonths($vaccinationDate);
    }

    public function isExpired(): bool
    {
        if (! $this->expiration_date) {
            return false;
        }

        return Carbon::parse($this->expiration_date)->isPast();
    }

    public function hasAdverseReaction(): bool
    {
        return ! empty($this->reaction) && is_array($this->reaction) && count($this->reaction) > 0;
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForVaccineType($query, int $vaccineTypeId)
    {
        return $query->where('vaccine_type_id', $vaccineTypeId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('occurrence_datetime', '>=', Carbon::now()->subDays($days));
    }

    public function toFHIR(): array
    {
        return [
            'resourceType' => 'Immunization',
            'id' => $this->fhir_id,
            'status' => $this->status,
            'vaccineCode' => [
                'coding' => [
                    [
                        'system' => 'http://hl7.org/fhir/sid/cvx',
                        'code' => $this->vaccine_code,
                        'display' => $this->vaccine_display,
                    ],
                ],
                'text' => $this->vaccine_display,
            ],
            'patient' => [
                'reference' => 'Patient/'.$this->patient->fhir_id,
                'display' => $this->patient->full_name,
            ],
            'encounter' => $this->encounter_id ? [
                'reference' => 'Encounter/'.$this->encounter->fhir_id,
            ] : null,
            'occurrenceDateTime' => $this->occurrence_datetime->toIso8601String(),
            'recorded' => $this->created_at->toIso8601String(),
            'primarySource' => $this->primary_source,
            'lotNumber' => $this->lot_number,
            'expirationDate' => $this->expiration_date ? $this->expiration_date->format('Y-m-d') : null,
            'manufacturer' => $this->manufacturer ? ['display' => $this->manufacturer] : null,
            'site' => $this->site_code ? [
                'coding' => [
                    [
                        'system' => 'http://snomed.info/sct',
                        'code' => $this->site_code,
                    ],
                ],
            ] : null,
            'route' => $this->route_code ? [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/v3-RouteOfAdministration',
                        'code' => $this->route_code,
                    ],
                ],
            ] : null,
            'doseQuantity' => $this->dose_quantity ? [
                'value' => $this->dose_quantity,
                'unit' => $this->dose_unit,
                'system' => 'http://unitsofmeasure.org',
                'code' => $this->dose_unit,
            ] : null,
            'performer' => [
                [
                    'actor' => [
                        'reference' => 'Practitioner/'.$this->practitioner->fhir_id,
                        'display' => $this->practitioner->full_name,
                    ],
                ],
            ],
            'note' => $this->note ? [
                ['text' => $this->note],
            ] : null,
            'reaction' => $this->hasAdverseReaction() ? array_map(function ($reaction) {
                return [
                    'date' => $reaction['date'] ?? null,
                    'detail' => [
                        'display' => $reaction['detail'] ?? '',
                    ],
                ];
            }, $this->reaction) : null,
            'protocolApplied' => [
                [
                    'doseNumberPositiveInt' => $this->dose_number,
                    'seriesDosesPositiveInt' => $this->series_doses,
                ],
            ],
        ];
    }
}
