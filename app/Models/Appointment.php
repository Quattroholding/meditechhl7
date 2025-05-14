<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Appointment extends Model
{
    use HasFactory;
    protected $fillable = [
        'fhir_id', 'patient_id', 'practitioner_id', 'identifier', 'status',
        'service_type', 'description', 'start', 'end', 'minutes_duration'
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime'
    ];

    // Relaciones
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }

    public function encounter(): HasOne
    {
        return $this->hasOne(Encounter::class);
    }

    public function consultingRoom()
    {
        return $this->belongsTo(ConsultingRoom::class)->withDefault(['name'=>'N/A']);
    }

    /*public function getStatusAttribute($attr){

    }*/
}
