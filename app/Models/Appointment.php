<?php

namespace App\Models;

use App\Enums\AppointmentStatusEnum;
use App\Jobs\SendAppointmentReminderJob;
use App\Models\Scopes\AppointmentScope;
use App\Notifications\AppointmentBookedForPractitionerNotification;
use App\Notifications\AppointmentBookedNotification;
use App\Notifications\AppointmentCancelledNotification;
use App\Notifications\AppointmentConfirmedNotification;
use App\Notifications\AppointmentProposedNotification;
use App\Notifications\AppointmentRejectedNotification;
use App\Notifications\AppointmentRescheduledForPractitionerNotification;
use App\Notifications\AppointmentRescheduledNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'fhir_id', 'patient_id', 'practitioner_id', 'client_id', 'identifier', 'status',
        'service_type', 'description', 'start', 'end', 'minutes_duration', 'medical_speciality_id', 'consulting_room_id',
        'original_requested_datetime', 'practitioner_suggested_datetime', 'comment', 'client_id', 'scb_id',
        'consultation_type', 'virtual_room_id', 'virtual_room_url',
        'virtual_session_started_at', 'virtual_session_ended_at', 'virtual_session_metadata', 'source_creation',
        'reminder_scheduled_at', 'reminder_sent_at',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'original_requested_datetime' => 'datetime',
        'practitioner_suggested_datetime' => 'datetime',
        'minutes_duration' => 'integer',
        'virtual_session_started_at' => 'datetime',
        'virtual_session_ended_at' => 'datetime',
        'virtual_session_metadata' => 'array',
        'status' => AppointmentStatusEnum::class,
        'reminder_scheduled_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    protected $appends = [
        'formatted_date',
        'formatted_time',
        'fhir_status',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new AppointmentScope);
    }

    // Relaciones
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class)->withDefault(['profile_name' => 'N/A']);
    }

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class)->withDefault(['profile_name' => 'N/A']);
    }

    public function encounter(): HasOne
    {
        return $this->hasOne(Encounter::class, 'appointment_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function medicalSpeciality(): BelongsTo
    {
        return $this->belongsTo(MedicalSpeciality::class, 'medical_speciality_id')->withDefault([
            'name' => 'Medicina General',
        ]);
    }

    public function consultingRoom()
    {
        return $this->belongsTo(ConsultingRoom::class)->withDefault(['name' => 'N/A']);
    }

    public function statusHistory()
    {
        return $this->hasMany(AppointmentStatus::class)->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to only include appointments fullfilled.
     */
    public function scopeFullFilled(Builder $query): void
    {
        $query->where('status', 'fulfilled');
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

    public static function statusColors()
    {
        return [
            'proposed' => 'dedede',
            'pending' => 'FFA500',
            'booked' => '4CAF50',
            'confirm' => '55ce63',
            'arrived' => '00BCD4',
            'fulfilled' => '2196F3',
            'cancelled' => 'F44336',
            'noshow' => '9E9E9E',
            'entered-in-error' => 'FF5252',
            'checked-in' => '7C4DFF',
            'waitlist' => 'FF9800',
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
            'no-show' => 'noshow',
        ];

        return $statusMap[$this->status->value] ?? 'booked';
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
            $startTime = Carbon::parse($this->attributes['appointment_date'].' '.$this->attributes['appointment_time']);
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
            'resourceType' => 'Appointment',
            'id' => $this->fhir_id,
            'status' => $this->fhir_status,
            'serviceCategory' => [[
                'coding' => [[
                    'system' => 'http://terminology.hl7.org/CodeSystem/service-category',
                    'code' => '17',
                    'display' => 'General Practice',
                ]],
            ]],
            'serviceType' => [[
                'coding' => [[
                    'system' => 'http://snomed.info/sct',
                    'code' => '11429006',
                    'display' => 'Consultation',
                ]],
            ]],
            'subject' => [
                'reference' => 'Patient/'.$this->patient->name,
                'display' => $this->patient_name,
            ],
            'participant' => [
                [
                    'actor' => [
                        'reference' => 'Patient/'.$this->patient->name,
                        'display' => $this->patient->name,
                    ],
                    'required' => 'required',
                    'status' => 'accepted',
                ],
                [
                    'actor' => [
                        'reference' => 'Practitioner/'.$this->practitioner->name,
                        'display' => $this->practitioner->name,
                    ],
                    'required' => 'required',
                    'status' => 'accepted',
                ],
            ],
            'start' => $this->start ? $this->start->toISOString() : null,
            'end' => $this->end ? $this->end->toISOString() : null,
            'comment' => $this->description,
            'patientInstruction' => $this->patient->phone ? 'Contacto: '.$this->patient->phone : null,
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

    public function addPatientToPractitionerClient()
    {
        $this->patient->clients()->sync($this->client_id, ['created_at' => now(), 'updated_at' => now()]);
    }

    // Notificación al practitioner sobre propuesta
    public function notifyPractitionerAboutProposal()
    {
        $this->practitioner->notify(
            new AppointmentProposedNotification($this)
        );
    }

    // Notificación al practitioner sobre cita agendada
    public function notifyPractitionerAboutBooking()
    {
        $this->practitioner->notify(
            new AppointmentBookedForPractitionerNotification($this)
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
            ! $this->practitioner_suggested_datetime->eq($this->original_requested_datetime);
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

    /**
     * Notify the patient about appointment reschedule (date/time change)
     *
     * @param  Carbon  $originalDateTime  The original date/time before the change
     * @param  string|null  $reason  Optional reason for the reschedule
     */
    public function notifyPatientAboutReschedule(Carbon $originalDateTime, ?string $reason = null)
    {
        $this->patient->notify(
            new AppointmentRescheduledNotification($this, $originalDateTime, $reason)
        );

        \Log::info('Patient notified about appointment reschedule', [
            'appointment_id' => $this->id,
            'patient_id' => $this->patient_id,
            'original_datetime' => $originalDateTime->format('Y-m-d H:i:s'),
            'new_datetime' => $this->start->format('Y-m-d H:i:s'),
            'reason' => $reason,
        ]);
    }

    /**
     * Notify the practitioner about appointment reschedule (date/time change)
     *
     * @param  Carbon  $originalDateTime  The original date/time before the change
     * @param  string|null  $reason  Optional reason for the reschedule
     * @param  string|null  $changedBy  Name of the person who made the change
     */
    public function notifyPractitionerAboutReschedule(Carbon $originalDateTime, ?string $reason = null, ?string $changedBy = null)
    {
        $this->practitioner->notify(
            new AppointmentRescheduledForPractitionerNotification($this, $originalDateTime, $reason, $changedBy)
        );

        \Log::info('Practitioner notified about appointment reschedule', [
            'appointment_id' => $this->id,
            'practitioner_id' => $this->practitioner_id,
            'original_datetime' => $originalDateTime->format('Y-m-d H:i:s'),
            'new_datetime' => $this->start->format('Y-m-d H:i:s'),
            'reason' => $reason,
            'changed_by' => $changedBy,
        ]);
    }

    /**
     * Schedule a reminder notification for the patient 2 hours before the appointment
     * Only schedules if the appointment is more than 2 hours in the future
     */
    public function notifyPatientAboutAppointment()
    {
        // Check if appointment is more than 2 hours in the future
        $hoursUntilAppointment = now()->diffInHours($this->start, false);

        if ($hoursUntilAppointment <= 2) {
            \Log::info('Appointment reminder not scheduled - appointment is too soon', [
                'appointment_id' => $this->id,
                'appointment_datetime' => $this->start->format('Y-m-d H:i:s'),
                'hours_until' => $hoursUntilAppointment,
            ]);

            return false;
        }

        // Skip if reminder already sent
        if ($this->reminder_sent_at) {
            \Log::info('Appointment reminder already sent, skipping duplicate', [
                'appointment_id' => $this->id,
                'reminder_sent_at' => $this->reminder_sent_at->format('Y-m-d H:i:s'),
            ]);

            return false;
        }

        // Skip if reminder already scheduled (prevents re-scheduling)
        if ($this->reminder_scheduled_at) {
            \Log::info('Appointment reminder already scheduled, skipping', [
                'appointment_id' => $this->id,
                'reminder_scheduled_at' => $this->reminder_scheduled_at->format('Y-m-d H:i:s'),
            ]);

            return false;
        }

        // Schedule the reminder job to run 2 hours before the appointment
        $reminderTime = $this->start->copy()->subHours(2);

        // Mark reminder as scheduled BEFORE dispatching
        $this->reminder_scheduled_at = now();
        $this->saveQuietly(); // Use saveQuietly to avoid triggering model events

        SendAppointmentReminderJob::dispatch($this)->delay($reminderTime);

        \Log::info('Appointment reminder scheduled successfully', [
            'appointment_id' => $this->id,
            'patient_id' => $this->patient_id,
            'appointment_datetime' => $this->start->format('Y-m-d H:i:s'),
            'reminder_datetime' => $reminderTime->format('Y-m-d H:i:s'),
            'hours_until_appointment' => $hoursUntilAppointment,
        ]);

        return true;
    }

    /**
     * Send immediate confirmation notification when appointment is booked
     * Only sends if the appointment is more than 1 day in the future
     */
    public function notifyPatientAboutBooking()
    {
        // Check if appointment is more than 1 day (24 hours) in the future
        $hoursUntilAppointment = now()->diffInHours($this->start, false);

        if ($hoursUntilAppointment <= 24) {
            \Log::info('Appointment booking notification not sent - appointment is within 24 hours', [
                'appointment_id' => $this->id,
                'appointment_datetime' => $this->start->format('Y-m-d H:i:s'),
                'hours_until' => $hoursUntilAppointment,
            ]);

            return false;
        }

        // Send immediate notification to patient
        $this->patient->notify(new AppointmentBookedNotification($this));

        \Log::info('Appointment booking notification sent successfully', [
            'appointment_id' => $this->id,
            'patient_id' => $this->patient_id,
            'patient_phone' => $this->patient->phone ?? $this->patient->whatsapp_phone,
            'patient_email' => $this->patient->email,
            'appointment_datetime' => $this->start->format('Y-m-d H:i:s'),
            'hours_until_appointment' => $hoursUntilAppointment,
        ]);

        return true;
    }

    // Virtual consultation methods
    public function isVirtual(): bool
    {
        return $this->consultation_type === 'virtual';
    }

    public function hasActiveVirtualSession(): bool
    {
        return $this->virtual_session_started_at !== null
            && $this->virtual_session_ended_at === null;
    }

    public function getVirtualSessionDuration(): ?int
    {
        if (! $this->virtual_session_started_at) {
            return null;
        }

        $endTime = $this->virtual_session_ended_at ?? now();

        return $this->virtual_session_started_at->diffInMinutes($endTime);
    }

    public function scopeVirtual($query)
    {
        return $query->where('consultation_type', 'virtual');
    }

    public function scopePresencial($query)
    {
        return $query->where('consultation_type', 'presencial');
    }

    /**
     * Clear reminder tracking when appointment is rescheduled
     * Allows a new reminder to be scheduled for the new datetime
     */
    public function clearReminderTracking(): void
    {
        $this->reminder_scheduled_at = null;
        $this->reminder_sent_at = null;
        $this->saveQuietly();

        \Log::info('Reminder tracking cleared for rescheduled appointment', [
            'appointment_id' => $this->id,
        ]);
    }
}
