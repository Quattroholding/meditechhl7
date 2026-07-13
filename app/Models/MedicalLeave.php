<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MedicalLeave extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fhir_id',
        'identifier',
        'patient_id',
        'encounter_id',
        'condition_id',
        'practitioner_id',
        'client_id',
        'consulting_room_id',
        'practitioner_name',
        'practitioner_license_number',
        'clinic_name',
        'clinic_address',
        'clinic_phone',
        'clinic_logo_url',
        'patient_name',
        'start_datetime',
        'end_datetime',
        'total_days',
        'diagnosis',
        'diagnosis_code',
        'status',
        'issue_date',
        'notes',
        'extension',
        'verification_hash',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'issue_date' => 'date',
        'extension' => 'array',
        'total_days' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (! $model->verification_hash) {
                $model->verification_hash = Str::random(32);
            }
        });
    }

    // Relaciones
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function condition(): BelongsTo
    {
        return $this->belongsTo(Condition::class);
    }

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Accesores y métodos de utilidad
    public function getFormattedStartDateAttribute()
    {
        return $this->start_datetime->format('d/m/Y H:i');
    }

    public function getFormattedEndDateAttribute()
    {
        return $this->end_datetime->format('d/m/Y H:i');
    }

    public function getFormattedIssueDateAttribute()
    {
        return $this->issue_date->format('d/m/Y');
    }

    /**
     * Obtiene la firma o el sello digital del médico como data URI para PDFs
     * Prioriza Files table, luego recepy_doctor_profiles
     */
    public function getPractitionerFilePathAttribute($type = 'signature')
    {
        // Obtener el user_id del practitioner
        $userId = $this->practitioner->user_id ?? null;

        if (! $userId) {
            return null;
        }

        // Intentar obtener sello desde tabla files (almacenamiento privado)
        $fileType = File::where('user_id', $userId)
            ->where('type', $type)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($fileType && $fileType->path) {
            // Los archivos en la tabla files se guardan en storage/app (privado)
            // Convertir a data URI para usar en PDFs
            $fullPath = storage_path('app/private/'.$fileType->path);

            if (file_exists($fullPath)) {
                $fileContents = file_get_contents($fullPath);
                $base64 = base64_encode($fileContents);
                $extension = pathinfo($fileType->path, PATHINFO_EXTENSION);

                return "data:image/{$extension};base64,{$base64}";
            }
        }

        // Fallback: intentar obtener desde recepy_doctor_profiles
        $profile = \DB::table('recepy_doctor_profiles')
            ->where('user_id', $userId)
            ->whereNotNull($type)
            ->first();

        if ($profile && $profile->$type) {
            // Los perfiles de recepy pueden estar en storage público
            $publicPath = public_path('storage/'.$profile->$type);

            if (file_exists($publicPath)) {
                $fileContents = file_get_contents($publicPath);
                $base64 = base64_encode($fileContents);
                $extension = pathinfo($profile->seal, PATHINFO_EXTENSION);

                return "data:image/{$extension};base64,{$base64}";
            }
        }

        return null;
    }

    // Recurso FHIR
    public function getFhirResourceAttribute()
    {
        return [
            'resourceType' => 'DocumentReference',
            'id' => $this->fhir_id,
            'identifier' => [
                [
                    'system' => 'http://hospital.sistema/medical-leaves',
                    'value' => $this->identifier,
                ],
            ],
            'status' => $this->status,
            'type' => [
                'coding' => [
                    [
                        'system' => 'http://loinc.org',
                        'code' => '28570-0',
                        'display' => 'Procedure note',
                    ],
                ],
                'text' => 'Medical Leave Certificate',
            ],
            'category' => [
                [
                    'coding' => [
                        [
                            'system' => 'http://hl7.org/fhir/us/core/CodeSystem/us-core-documentreference-category',
                            'code' => 'clinical-note',
                            'display' => 'Clinical Note',
                        ],
                    ],
                ],
            ],
            'subject' => [
                'reference' => 'Patient/'.$this->patient->fhir_id,
                'display' => $this->patient_name,
            ],
            'date' => $this->issue_date->toIso8601String(),
            'author' => [
                [
                    'reference' => 'Practitioner/'.$this->practitioner->fhir_id,
                    'display' => $this->practitioner_name,
                ],
            ],
            'context' => [
                'encounter' => [
                    [
                        'reference' => 'Encounter/'.$this->encounter->fhir_id,
                    ],
                ],
                'period' => [
                    'start' => $this->start_datetime->toIso8601String(),
                    'end' => $this->end_datetime->toIso8601String(),
                ],
            ],
        ];
    }
}
