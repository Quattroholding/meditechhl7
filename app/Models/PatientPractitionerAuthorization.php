<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientPractitionerAuthorization extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'practitioner_id',
        'authorized_by_user_id',
        'authorized_at',
    ];

    protected function casts(): array
    {
        return [
            'authorized_at' => 'datetime',
        ];
    }

    /**
     * Relación con el paciente
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relación con el médico
     */
    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }

    /**
     * Relación con el usuario que autorizó
     */
    public function authorizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by_user_id');
    }

    /**
     * Verifica si un practitioner está autorizado para ver el historial de un paciente
     */
    public static function isAuthorized(int $patientId, int $practitionerId): bool
    {
        return self::where('patient_id', $patientId)
            ->where('practitioner_id', $practitionerId)
            ->exists();
    }
}
