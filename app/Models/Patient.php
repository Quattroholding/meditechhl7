<?php

namespace App\Models;

use App\Models\Scopes\PatientScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notifiable;

class Patient extends BaseModel
{
    use HasFactory,Notifiable;

    protected $fillable = [
        'fhir_id', 'identifier', 'identifier_type', 'name', 'given_name',
        'family_name', 'gender', 'birth_date', 'deceased', 'deceased_date',
        'address','billing_address', 'city', 'state', 'postal_code', 'country', 'phone', 'email',
        'marital_status', 'multiple_birth', 'multiple_birth_count','blood_type'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'deceased_date' => 'datetime',
        'deceased' => 'boolean',
        'multiple_birth' => 'boolean'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new PatientScope());
    }

    public function routeNotificationForMail($notification = null)
    {
        // Si estamos en testing, usar correo específico
        if (config('app.env') === 'local' || config('mail.testing_mode', false)) {
            return config('mail.testing_patient_email', 'patient.test@example.com');
        }

        return $this->email;
    }

    public function getCompleteHistory(): array
    {
        return [
           // 'patient' => ['title'=>__('patient.title'),'data'=>$this],
            'medical_history'=>['title'=>__('patient.medical_history'),'data'=>$this->medicalHistories()],
            'encounters' => ['title'=>__('encounter.titles'),'data'=>$this->getEncountersWithDetails()],
            'conditions' => ['title'=>__('patient.diagnostics'),'data'=>$this->conditions()],
            'vital_signs' => ['title'=>__('patient.vital_signs'),'data'=>$this->vitalSigns()],
            'physical_exams' => ['title'=>__('patient.physical_exams'),'data'=>$this->physicalExams()],
            'medications' => ['title'=>__('patient.medications'),'data'=>$this->medicationRequests()],
            'services' => ['title'=>__('patient.service_request'),'data'=>$this->serviceRequests()],
            'procedures' => ['title'=>__('patient.procedures'),'data'=>$this->procedures()],
            'referrals' => ['title'=>__('patient.title'),'data'=>$this->referrals()],
        ];
    }

    public function getSectionHistory($section){
        $data =[];
        switch ($section) {
            case 'info':
                $data = $this;
                break;
            case 'medical-history':
                $data = $this->medicalHistories();
                break;
            case 'encounters':
                $data = $this->getEncountersWithDetails();
                break;
            case 'conditions':
                $data = $this->conditions();
                break;
            case 'vital-signs':
                $data = $this->vitalSigns();
                break;
            case 'physical-exams':
                $data = $this->physicalExams();
                break;
            case 'medications':
                $data = $this->medicationRequests();
                break;
            case 'services':
                $data = $this->serviceRequests();
                break;
            case 'procedures':
                $data = $this->procedures();
                break;
            case 'referrals':
                $data = $this->referrals();
                break;
        }

        return $data;
    }

    // Relaciones

    public function clients(){
        return $this->belongsToMany(Client::class,'patient_clients');
    }

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

    public function allergies(){

        return $this->hasMany(MedicalHistory::class,'patient_id')->whereCategory('allergy');
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
        return $this->hasMany(MedicationRequest::class,'patient_id');
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function procedures(): HasMany
    {
        return $this->hasMany(Procedure::class);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }

    public function notes(){
        return $this->hasMany(ClinicalImpression::class);
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

    public function getAgeAttribute($attr){
        return Carbon::parse($this->birth_date)->age;
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

    public function getLastEncounter(){

    }

    public function current_medications(){
        return $this->hasMany(MedicationRequest::class,'patient_id')
            ->whereEncounterId($this->encounters()->latest()->first()->id)
            ->whereRaw("valid_to >='".now()->format('Y-m-d')."'");
    }
}
