<?php

namespace App\Models;

use App\Models\Scopes\PractitionerScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Practitioner extends BaseModel
{
    use HasFactory,Notifiable;

    protected $fillable = [
        'fhir_id', 'identifier', 'name', 'given_name', 'family_name','user_id',
        'gender', 'birth_date', 'address', 'phone', 'email', 'active', 'registry', 'identifier_type',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new PractitionerScope);
    }

    public function routeNotificationForMail($notification = null)
    {
        // Si estamos en testing, usar correo específico
        if (config('app.env') === 'testing' || config('mail.testing_mode', false)) {
            return config('mail.testing_practitioner_email', 'doctor.test@example.com');
        }

        return $this->email;
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

    public function qualifications(): HasMany
    {
        return $this->hasMany(PractitionerQualification::class);
    }

    public function specialties()
    {
        return $this->belongsToMany(MedicalSpeciality::class, 'practitioner_qualifications');
    }

    public function medicationRequests(): HasMany
    {
        return $this->hasMany(MedicationRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function insuranceCompanies()
    {
        return $this->belongsToMany(InsuranceCompany::class, 'practitioner_insurance_company')
                    ->withPivot('accepts', 'custom_coverage_percentage', 'custom_copay_amount', 'notes')
                    ->withTimestamps();
    }

    public function acceptedInsurances()
    {
        return $this->belongsToMany(InsuranceCompany::class, 'practitioner_insurance_company')
                    ->wherePivot('accepts', true)
                    ->withPivot('accepts', 'custom_coverage_percentage', 'custom_copay_amount', 'notes')
                    ->withTimestamps();
    }

    public function rejectedInsurances()
    {
        return $this->belongsToMany(InsuranceCompany::class, 'practitioner_insurance_company')
                    ->wherePivot('accepts', false)
                    ->withPivot('accepts', 'custom_coverage_percentage', 'custom_copay_amount', 'notes')
                    ->withTimestamps();
    }

    public function avatar()
    {
        return $this->files()->whereType('avatar')->latest()->first();
    }

    public function signature()
    {
        return $this->files()->whereType('signature')->latest()->first();
    }

    public function seal()
    {
        return $this->files()->whereType('seal')->latest()->first();
    }

    public function getSignaturePath()
    {
        $signature = $this->signature();
        if ($signature && \Storage::disk('local')->exists($signature->path)) {
            return \Storage::disk('local')->path($signature->path);
        }

        return null;
    }

    public function getSealPath()
    {
        $seal = $this->seal();
        if ($seal && \Storage::disk('local')->exists($seal->path)) {
            return \Storage::disk('local')->path($seal->path);
        }

        return null;
    }

    public function getProfileNameAttribute()
    {

        $path = url('assets/img/profiles/avatar-02.jpg');
        if ($this->avatar()) {
            $path = url('storage/'.$this->avatar()->path);
        }

        return '<div class="profile-image m-0">
                  <a href="'.url('practitioners/'.$this->id.'/profile').'"  class= "text-base">
                                        <img width="28" height="28" src="'.$path.'" class="rounded-circle m-r-5" alt="" style="display:inline-block;">
                                        '.$this->name.'
                                    </a>
                    </div>';
    }

    public function getBirthDateAttribute($attr)
    {
        return Carbon::parse($attr)->format('d/m/Y');
    }

    // Scope para obtener solo practicantes activos
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
