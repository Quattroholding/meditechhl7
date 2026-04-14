<?php

namespace App\Models;

use App\Models\Scopes\MedicalSpecialityScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalSpeciality extends Model
{
    protected $table = 'medical_specialties';

    protected $fillable = ['code', 'name', 'description', 'is_surgical'];

    protected $casts = [
        'is_surgical' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new MedicalSpecialityScope);
    }

    public function practitionerQualifications(): HasMany
    {
        return $this->hasMany(PractitionerQualification::class);
    }

    public function practitioners()
    {
        return $this->belongsToMany(Practitioner::class, 'practitioner_qualifications');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'medical_speciality_id');
    }
}
