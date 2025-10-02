<?php

namespace App\Models;

use App\Models\Scopes\PatientScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class Patient extends BaseModel
{
    use HasFactory,Notifiable;

    protected $fillable = [
        'fhir_id', 'identifier', 'identifier_type', 'name', 'given_name',
        'family_name', 'gender', 'birth_date', 'deceased', 'deceased_date',
        'address', 'city', 'state', 'postal_code', 'country', 'phone', 'email',
        'marital_status', 'multiple_birth', 'multiple_birth_count', 'blood_type', 'whatsapp_phone',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'deceased_date' => 'datetime',
        'deceased' => 'boolean',
        'multiple_birth' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new PatientScope);
    }

    public function routeNotificationForMail($notification = null)
    {
        // Si estamos en testing, usar correo específico
        if (config('app.env') === 'local' || config('mail.testing_mode')) {
            return config('mail.testing_patient_email', 'patient.test@example.com');
        }

        return $this->email;
    }

    public function getCompleteHistory(): array
    {
        return [
            // 'patient' => ['title'=>__('patient.title'),'data'=>$this],
            'medical_history' => ['title' => __('patient.medical_history'), 'data' => $this->medicalHistories()],
            'encounters' => ['title' => __('encounter.titles'), 'data' => $this->getEncountersWithDetails()],
            'conditions' => ['title' => __('patient.diagnostics'), 'data' => $this->conditions()],
            'vital_signs' => ['title' => __('patient.vital_signs'), 'data' => $this->vitalSigns()],
            'physical_exams' => ['title' => __('patient.physical_exams'), 'data' => $this->physicalExams()],
            'medications' => ['title' => __('patient.medications'), 'data' => $this->medicationRequests()],
            'services' => ['title' => __('patient.service_request'), 'data' => $this->serviceRequests()],
            'procedures' => ['title' => __('patient.procedures'), 'data' => $this->procedures()],
            'referrals' => ['title' => __('patient.title'), 'data' => $this->referrals()],
        ];
    }

    public function getSectionHistory($section)
    {
        $data = [];
        switch ($section) {
            case 'info':
                $data = $this;
                break;
            case 'medical-history':
                $data = $this->medicalHistories;
                break;
            case 'encounters':
                $data = $this->getEncountersWithDetails();
                break;
            case 'conditions':
                $data = $this->conditions()->with(['icd10Code', 'practitioner'])->get();
                break;
            case 'vital-signs':
                $data = $this->vitalSigns()->with(['practitioner', 'observationType'])->get();
                break;
            case 'physical-exams':
                $data = $this->physicalExams()->with(['practitioner', 'observationType'])->get();
                break;
            case 'medications':
                $data = $this->medicationRequests()->with(['medicine', 'practitioner'])->get();
                break;
            case 'services':
                $data = $this->serviceRequests()->with('practitioner')->get();
                break;
            case 'procedures':
                $data = $this->procedures()->with('practitioner')->get();
                break;
            case 'allergies':
                $data = $this->allergies;
                break;
        }

        return $data;
    }

    // Relaciones

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'patient_clients');
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

    public function allergies()
    {

        return $this->hasMany(MedicalHistory::class, 'patient_id')->whereCategory('allergy');
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
        return $this->hasMany(MedicationRequest::class, 'patient_id');
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

    public function notes()
    {
        return $this->hasMany(ClinicalImpression::class);
    }

    public function account()
    {
        return $this->hasOne(Account::class, 'patient_id')->whereStatus('active');
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
                    'value' => $this->identifier,
                ],
            ],
            'name' => [
                [
                    'use' => 'official',
                    'family' => $this->family_name,
                    'given' => [$this->given_name],
                ],
            ],
            // ... otros campos según FHIR
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getBirthDateAttribute($attr)
    {
        return $attr ? Carbon::parse($attr)->format('d-m-Y') : null;
    }

    public function setBirthDateAttribute($value)
    {
        // Handle different date formats when setting
        if ($value) {
            try {
                // Try to parse the date - Carbon can handle various formats
                $this->attributes['birth_date'] = Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                // If parsing fails, store as is and let validation catch it
                $this->attributes['birth_date'] = $value;
            }
        } else {
            $this->attributes['birth_date'] = null;
        }
    }

    public function getAgeAttribute($attr)
    {
        return Carbon::parse($this->birth_date)->age;
    }

    public function getProfileNameAttribute()
    {
        $path = url('assets/img/profiles/avatar-02.jpg');
        $route = '';
        if (auth()->user()->can('patients.profile')) {
            $route = route('patient.profile', $this->id);
        }

        $title = 'Ver perfil';
        if ($this->avatar()) {
            $path = url('storage/'.$this->avatar()->path);
        }

        if (auth()->user()->hasRole('doctor')) {
            $title = 'Ver historial medico';
            $route = route('patient.medical_history', $this->id);
        }

        return '<div class="profile-image m-0">
                  <a href="'.$route.'" title="'.$title.'" class= "text-base">
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

    public function avatar()
    {
        return $this->files()->whereType('avatar')->latest()->first();
    }

    public function getLastEncounter() {}

    public function current_medications()
    {
        return $this->hasMany(MedicationRequest::class, 'patient_id')
            ->whereEncounterId($this->encounters()->latest()->first()->id)
            ->whereRaw("valid_to >='".now()->format('Y-m-d')."'");
    }

    // Insurance relationships
    public function insurancePolicies(): HasMany
    {
        return $this->hasMany(PatientInsurancePolicy::class);
    }

    public function activeInsurancePolicies(): HasMany
    {
        return $this->insurancePolicies()->active();
    }

    public function primaryInsurance(): HasOne
    {
        return $this->hasOne(PatientInsurancePolicy::class)->primary()->active();
    }

    public function secondaryInsurance(): HasOne
    {
        return $this->hasOne(PatientInsurancePolicy::class)->secondary()->active();
    }

    public function invoicePayments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function hasActiveInsurance(): bool
    {
        return $this->activeInsurancePolicies()->exists();
    }

    public function getPrimaryInsuranceCompany()
    {
        return $this->primaryInsurance?->insuranceCompany;
    }

    // Patient relationships
    public function relationships(): HasMany
    {
        return $this->hasMany(PatientRelationship::class);
    }

    public function activeRelationships(): HasMany
    {
        return $this->relationships()->active();
    }

    public function relatedTo(): HasMany
    {
        return $this->hasMany(PatientRelationship::class, 'related_patient_id');
    }

    public function emergencyContacts(): HasMany
    {
        return $this->relationships()->emergencyContacts()->active();
    }

    public function insuranceSubscribers(): HasMany
    {
        return $this->relationships()->insuranceSubscribers()->active();
    }

    public function familyMembers(): HasMany
    {
        return $this->relationships()->active()
            ->whereIn('relationship_code', ['CHILD', 'PARENT', 'SPOUSE', 'SIBLING']);
    }

    public function dependents(): HasMany
    {
        return $this->relationships()->active()
            ->whereIn('relationship_code', ['CHILD', 'SPOUSE']);
    }

    public function guardians(): HasMany
    {
        return $this->relationships()->active()
            ->whereIn('relationship_code', ['PARENT', 'GUARD']);
    }

    // Insurance methods with relationships
    public function subscribedInsurancePolicies(): HasMany
    {
        return $this->hasMany(PatientInsurancePolicy::class, 'subscriber_patient_id');
    }

    public function getDependentInsurancePolicies()
    {
        return $this->subscribedInsurancePolicies()
            ->where('patient_id', '!=', $this->id)
            ->with(['patient', 'insuranceCompany']);
    }

    public function getFamilyInsuranceInfo(): array
    {
        $familyPolicies = [];

        // Policies where this patient is the beneficiary
        $beneficiaryPolicies = $this->activeInsurancePolicies()->with('subscriberPatient')->get();

        // Policies where this patient is the subscriber (covering dependents)
        $subscriberPolicies = $this->getDependentInsurancePolicies()->get();

        return [
            'as_beneficiary' => $beneficiaryPolicies,
            'as_subscriber' => $subscriberPolicies,
            'total_dependents_covered' => $subscriberPolicies->count(),
        ];
    }

    public function addRelationship(Patient $relatedPatient, string $relationshipCode, array $options = []): PatientRelationship
    {
        return $this->relationships()->create([
            'fhir_id' => 'rel-'.uniqid(),
            'related_patient_id' => $relatedPatient->id,
            'relationship_code' => $relationshipCode,
            'relationship_display' => PatientRelationship::RELATIONSHIP_CODES[$relationshipCode] ?? $relationshipCode,
            'is_emergency_contact' => $options['is_emergency_contact'] ?? false,
            'is_insurance_subscriber' => $options['is_insurance_subscriber'] ?? false,
            'effective_date' => $options['effective_date'] ?? now(),
            'notes' => $options['notes'] ?? null,
        ]);
    }

    public function getEmergencyContactsInfo(): array
    {
        return $this->emergencyContacts()
            ->with('relatedPatient')
            ->get()
            ->map(function ($contact) {
                return $contact->getContactInfo();
            })->toArray();
    }

    public function getGenderAttribute($attr): string
    {
        if(strtolower($attr)=='female'){
            return 'Femenino';
        }

        return 'Masculino';

    }
}
