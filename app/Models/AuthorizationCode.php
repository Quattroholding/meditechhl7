<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthorizationCode extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'practitioner_id',
        'code',
        'expires_at',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
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
     * Genera un nuevo código de autorización
     */
    public static function generate(int $patientId, int $practitionerId): self
    {
        // Generar código de 4 dígitos
        $code = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        return self::create([
            'patient_id' => $patientId,
            'practitioner_id' => $practitionerId,
            'code' => $code,
            'expires_at' => Carbon::now()->addHour(),
        ]);
    }

    /**
     * Verifica si el código es válido
     */
    public function isValid(): bool
    {
        return is_null($this->used_at) &&
               Carbon::now()->lessThan($this->expires_at);
    }

    /**
     * Marca el código como usado
     */
    public function markAsUsed(): void
    {
        $this->update(['used_at' => Carbon::now()]);
    }

    /**
     * Busca un código válido
     */
    public static function findValidCode(int $patientId, int $practitionerId, string $code): ?self
    {
        return self::where('patient_id', $patientId)
            ->where('practitioner_id', $practitionerId)
            ->where('code', $code)
            ->whereNull('used_at')
            ->where('expires_at', '>', Carbon::now())
            ->first();
    }
}
