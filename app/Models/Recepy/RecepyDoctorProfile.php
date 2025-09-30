<?php

namespace App\Models\Recepy;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecepyDoctorProfile extends Model
{
    use SoftDeletes;

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
        'medical_code_number',
        'is_active',
        'recepy_background_color',
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
