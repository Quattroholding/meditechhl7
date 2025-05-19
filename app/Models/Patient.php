<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Patient extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'fhir_id', 'identifier', 'identifier_type', 'name', 'given_name',
        'family_name', 'gender', 'birth_date', 'deceased', 'deceased_date',
        'address', 'city', 'state', 'postal_code', 'country', 'phone', 'email',
        'marital_status', 'multiple_birth', 'multiple_birth_count'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'deceased_date' => 'datetime',
        'deceased' => 'boolean',
        'multiple_birth' => 'boolean'
    ];

    public function getCompleteHistory(): array
    {
        return [
            'patient' => $this,
            'encounters' => $this->getEncountersWithDetails(),
            'conditions' => $this->conditions(),
            'vital_signs' => $this->vitalSigns(),
            'physical_exams' => $this->physicalExams(),
            'medications' => $this->medicationRequests(),
            'procedures' => $this->procedures(),
            'referrals' => $this->referrals(),
            'allergies'=>$this->allergies(),
        ];
    }

    // Relaciones
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function encounters(): HasMany
    {
        return $this->hasMany(Encounter::class);
    }

    public function medicalHistories(): HasMany
    {
        return $this->hasMany(MedicalHistory::class);
    }

    public function allergies(): HasMany
    {
        return $this->hasMany(Allergy::class);
    }

    public function conditions(): HasMany
    {
        return $this->hasMany(Condition::class);
    }


    public function vitalSigns(): HasMany
    {
        return $this->hasMany(VitalSign::class);
    }

    public function physicalExams(): HasMany
    {
        return $this->hasMany(PhysicalExam::class);
    }

    public function medicationRequests(): HasMany
    {
        return $this->hasMany(MedicationRequest::class);
    }

    public function procedures(): HasMany
    {
        return $this->hasMany(Procedure::class);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }

    // Accesor para FHIR
    public function getFhirResourceAttribute()
    {
        return [
            'resourceType' => 'Patient',
            'id' => $this->fhir_id,
            'identifier' => [
                [
                    'system' => $this->identifier_type,
                    'value' => $this->identifier
                ]
            ],
            'name' => [
                [
                    'use' => 'official',
                    'family' => $this->family_name,
                    'given' => [$this->given_name]
                ]
            ],
            // ... otros campos según FHIR
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getBirthDateAttribute($attr){
        return Carbon::parse($attr)->format('d-m-Y');
    }

    public function getProfileNameAttribute(){
        $path = url('assets/img/profiles/avatar-02.jpg');
        if($this->avatar()) $path = url('storage/'.$this->avatar()->path);

        return '<div class="profile-image">
                  <a href="'.route('patient.profile',$this->id).'" >
                                        <img width="28" height="28" src="'.$path.'" class="rounded-circle m-r-5" alt="" style="display:inline-block;">
                                        '.$this->name.'
                                    </a>
                    </div>';
    }

    protected function getEncountersWithDetails(): Collection
    {
        return $this->encounters()
            ->with(['practitioner', 'serviceRequests', 'observations'])
            ->get();
    }

    public function avatar(){
        return $this->files()->whereType('avatar')->latest()->first();
    }
}
