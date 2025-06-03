<?php

namespace App\Models;
use App\Models\Scopes\EncouterScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Encounter extends Model
{
    use HasFactory;
    protected $fillable = [
        'fhir_id', 'patient_id', 'practitioner_id', 'appointment_id', 'identifier',
        'status', 'class', 'type', 'priority', 'reason','start','end','medical_speciality_id',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime'
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new EncouterScope());
    }

    // Relaciones
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function diagnoses(): HasMany
    {
        return $this->hasMany(EncounterDiagnosis::class);
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

    public function presentIllnesses(): HasOne
    {
        return $this->hasOne(PresentIllness::class);
    }

    public function procedures(): HasMany
    {
        return $this->hasMany(Procedure::class);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }

    public function observations(): HasMany
    {
        return $this->hasMany(Observation::class);
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function medicalSpeciality(): BelongsTo
    {
        return $this->belongsTo(MedicalSpeciality::class);
    }

    /**
     * Scope a query to only include appointments fullfilled.
     */
    public function scopeFinished(Builder $query): void
    {
        $query->where('status', 'finished');
    }

    public function getTimeAttribute(){
        return Carbon::parse($this->start)->format('h:i').'-'.Carbon::parse($this->end)->format('h:i');
    }

    public function getStatusAttribute($attr){

       return ' <span  class="badge" style="background-color: #'.$this->statusColors()[$attr].'">'. __('encounter.status.'.$attr). '</span>';
    }

    public function statusColors(){
        return [
            'planned'=>'FFD700',
            'arrived'=>'00BCD4',
            'triaged'=>'FF9800',
            'in-progress'=>'4CAF50',
            'onleave'=>'9C27B0',
            'finished'=>'2196F3',
            'cancelled'=>'F44336',
        ];
    }

}
