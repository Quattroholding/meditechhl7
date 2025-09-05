<?php

namespace App\Models\Recepy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class RecepyDoctorProfile extends Model
{
    protected $table = 'recepy_doctor_profiles';

    protected $fillable = [
        'user_id',
        'logo',
        'address',
        'speciality',
        'facility',
        'phone',
        'email',
        'signature',
        'seal',
        'medical_license_number',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(RecepyPrescription::class, 'doctor_profile_id');
    }
}
