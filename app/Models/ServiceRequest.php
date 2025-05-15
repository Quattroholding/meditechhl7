<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequest extends Model
{
    use SoftDeletes;

    protected $fillable=['fhir_id','encounter_id','patient_id','practitioner_id','status','intent','priority','do_not_perform','code','service_type',
        'code_system','code_display','quantity','quantity_unit','occurrence_start','occurrence_end','body_site','note','patient_instruction',
        'supporting_info','reason_code','reason_reference','authored_on','last_updated'];

    protected $casts = [
        'body_site' => 'array',
        'supporting_info' => 'array',
        'occurrence_start' => 'datetime',
        'occurrence_end' => 'datetime',
        'authored_on' => 'datetime',
        'last_updated' => 'datetime',
    ];

    // Relación con Encounter
    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    // Relación con Patient
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    // Relación con Practitioner
    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }

    // Métodos útiles para estados HL7
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function cpt(){
        return $this->belongsTo(CptCode::class,'code','code');
    }
}
