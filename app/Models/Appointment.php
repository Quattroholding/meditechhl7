<?php

namespace App\Models;

use App\Models\Scopes\AppointmentScope;
use App\Notifications\AppointmentCancelledNotification;
use App\Notifications\AppointmentConfirmedNotification;
use App\Notifications\AppointmentProposedNotification;
use App\Notifications\AppointmentRejectedNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    use HasFactory;
    protected $fillable = [
        'fhir_id', 'patient_id', 'practitioner_id','client_id', 'identifier', 'status',
        'service_type', 'description', 'start', 'end', 'minutes_duration','medical_speciality_id','consulting_room_id',
        'original_requested_datetime','practitioner_suggested_datetime','comment'
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'original_requested_datetime' => 'datetime',
        'practitioner_suggested_datetime' => 'datetime',
        'minutes_duration' => 'integer',
    ];

    protected $appends = [
        'formatted_date',
        'formatted_time',
        'fhir_status'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new AppointmentScope());
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

    public function encounter(): HasOne
    {
        return $this->hasOne(Encounter::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function medicalSpeciality(): BelongsTo
    {
        return $this->belongsTo(MedicalSpeciality::class,'medical_speciality_id')->withDefault([
            'name'=>'Medicina General',
        ]);
    }

    public function consultingRoom()
    {
        return $this->belongsTo(ConsultingRoom::class)->withDefault(['name'=>'N/A']);
    }

    /**
     * Scope a query to only include appointments fullfilled.
     */
    public function scopeFullFilled(Builder $query): void
    {
        $query->where('status', 'fullfilled');
    }

    /**
     * Scope a query to only include appointments pending.
     */
    public function scopePending(Builder $query): void
    {
        $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include appointments booked.
     */
    public function scopeBooked(Builder $query): void
    {
        $query->where('status', 'reservado');
    }

    public static function statusColors(){
          return [
              'proposed'=>'dedede',
              'pending'=>'FFA500',
              'booked'=>'4CAF50',
              'arrived'=>'00BCD4',
              'fulfilled'=>'2196F3',
              'cancelled'=>'F44336',
              'noshow'=>'9E9E9E',
              'entered-in-error'=>'FF5252',
              'checked-in'=>'7C4DFF',
              'waitlist'=>'FF9800'
          ];
    }

    // Accesor para fecha formateada
    public function getFormattedDateAttribute()
    {
        return $this->start->format('Y-m-d');
    }

    // Accesor para hora formateada
    public function getFormattedTimeAttribute()
    {
        return $this->start->format('H:i');
    }

    // Accesor para estado FHIR
    public function getFhirStatusAttribute()
    {
        $statusMap = [
            'scheduled' => 'booked',
            'confirmed' => 'booked',
            'in-progress' => 'arrived',
            'completed' => 'fulfilled',
            'cancelled' => 'cancelled',
            'no-show' => 'noshow'
        ];

        return $statusMap[$this->status] ?? 'booked';
    }

    // Mutador para calcular tiempos automáticamente
    public function setAppointmentDateAttribute($value)
    {
        $this->attributes['appointment_date'] = $value;
        $this->calculateTimes();
    }

    public function setAppointmentTimeAttribute($value)
    {
        $this->attributes['appointment_time'] = $value;
        $this->calculateTimes();
    }

    public function setDurationAttribute($value)
    {
        $this->attributes['minutes_duration'] = $value;
        $this->calculateTimes();
    }

    private function calculateTimes()
    {
        if (isset($this->attributes['appointment_date']) && isset($this->attributes['appointment_time'])) {
            $startTime = Carbon::parse($this->attributes['appointment_date'] . ' ' . $this->attributes['appointment_time']);
            $this->attributes['start_time'] = $startTime;

            if (isset($this->attributes['minutes_duration'])) {
                $this->attributes['end_time'] = $startTime->copy()->addMinutes($this->attributes['duration']);
            }
        }
    }

    // Convertir a formato FHIR
    public function toFHIR()
    {
        return [
            "resourceType" => "Appointment",
            "id" => $this->fhir_id,
            "status" => $this->fhir_status,
            "serviceCategory" => [[
                "coding" => [[
                    "system" => "http://terminology.hl7.org/CodeSystem/service-category",
                    "code" => "17",
                    "display" => "General Practice"
                ]]
            ]],
            "serviceType" => [[
                "coding" => [[
                    "system" => "http://snomed.info/sct",
                    "code" => "11429006",
                    "display" => "Consultation"
                ]]
            ]],
            "subject" => [
                "reference" => "Patient/" .$this->patient->name,
                "display" => $this->patient_name
            ],
            "participant" => [
                [
                    "actor" => [
                        "reference" => "Patient/" . $this->patient->name,
                        "display" =>$this->patient->name
                    ],
                    "required" => "required",
                    "status" => "accepted"
                ],
                [
                    "actor" => [
                        "reference" => "Practitioner/" .$this->practitioner->name,
                        "display" => $this->practitioner->name
                    ],
                    "required" => "required",
                    "status" => "accepted"
                ]
            ],
            "start" => $this->start ? $this->start->toISOString() : null,
            "end" => $this->end ? $this->end->toISOString() : null,
            "comment" => $this->description,
            "patientInstruction" => $this->patient->phone ? "Contacto: " . $this->patient->phone : null
        ];
    }

    // Scopes para consultas comunes
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('start', $date);
    }

    public function scopeForDateRange($query, $start, $end)
    {
        return $query->whereBetween('start', [$start, $end]);
    }

    public function scopeForDoctor($query, $doctorId)
    {
        return $query->where('pratitioner_id', $doctorId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start', '>=', now()->toDateString())
            ->orderBy('start');
    }

    public function addPatientToPractitionerClient(){
        $this->patient->clients()->sync($this->client_id,array('created_at'=>now(),'updated_at'=>now()));
    }

    // Notificación al practitioner sobre propuesta
    public function notifyPractitionerAboutProposal()
    {
        $this->practitioner->notify(
            new AppointmentProposedNotification($this)
        );
    }

    public function notifyPatientAboutConfirmation()
    {
        $this->patient->notify(
            new AppointmentConfirmedNotification($this)
        );
    }

    public function wasDateTimeChanged(): bool
    {
        return $this->practitioner_suggested_datetime &&
            !$this->practitioner_suggested_datetime->eq($this->original_requested_datetime);
    }

    public function notifyPatientAboutRejection($rejectionReason = null, $alternatives = [])
    {
        $this->patient->notify(
            new AppointmentRejectedNotification($this, $rejectionReason)
        );
    }


    // Notificación al paciente sobre cancelación de cita confirmada
    public function notifyPatientAboutCancellation($cancellationReason = null)
    {
        $this->patient->notify(
            new AppointmentCancelledNotification($this, $cancellationReason, 'practitioner')
        );
    }
}

