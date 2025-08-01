<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalSpeciality  extends Model
{
    protected $table='medical_specialties';
    protected $fillable = ['code', 'name','is_surgical'];

    public function practitionerQualifications(): HasMany
    {
        return $this->hasMany(PractitionerQualification::class);
    }
}
