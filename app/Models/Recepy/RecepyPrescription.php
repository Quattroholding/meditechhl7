<?php

namespace App\Models\Recepy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class RecepyPrescription extends Model
{
    use SoftDeletes;

    protected $table = 'recepy_prescriptions';

    protected $fillable = [
        'doctor_profile_id',
        'patient_name',
        'patient_document',
        'patient_age',
        'patient_birth_date',
        'patient_gender',
        'patient_address',
        'patient_phone',
        'diagnosis',
        'additional_notes',
        'prescription_date',
        'prescription_number',
        'status'
    ];

    protected $casts = [
        'patient_birth_date' => 'date',
        'prescription_date' => 'date',
        'patient_age'=>'integer',
    ];

    public function doctorProfile(): BelongsTo
    {
        return $this->belongsTo(RecepyDoctorProfile::class, 'doctor_profile_id');
    }

    public function medications(): HasMany
    {
        return $this->hasMany(RecepyPrescriptionMedication::class, 'prescription_id')
                    ->orderBy('line_order');
    }

    public function activeMedications(): HasMany
    {
        return $this->hasMany(RecepyPrescriptionMedication::class, 'prescription_id')
                    ->where('is_active', true)
                    ->orderBy('line_order');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($prescription) {
            if (empty($prescription->prescription_number)) {
                $prescription->prescription_number = 'RX-' . date('Y') . '-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
