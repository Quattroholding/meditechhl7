<?php

namespace App\Models;

use App\Enums\FreedSlotSource;
use App\Enums\FreedSlotStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentFreedSlot extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'practitioner_id',
        'consulting_room_id',
        'medical_speciality_id',
        'client_id',
        'slot_date',
        'slot_start_time',
        'slot_end_time',
        'duration_minutes',
        'freed_by',
        'cancelled_appointment_id',
        'status',
        'matched_waitlist_entry_id',
        'matched_at',
        'expires_at',
    ];

    protected $casts = [
        'slot_date' => 'date',
        'slot_start_time' => 'string',
        'slot_end_time' => 'string',
        'duration_minutes' => 'integer',
        'freed_by' => FreedSlotSource::class,
        'status' => FreedSlotStatus::class,
        'matched_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Relaciones
    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }

    public function consultingRoom(): BelongsTo
    {
        return $this->belongsTo(ConsultingRoom::class);
    }

    public function medicalSpeciality(): BelongsTo
    {
        return $this->belongsTo(MedicalSpeciality::class, 'medical_speciality_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function cancelledAppointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'cancelled_appointment_id');
    }

    public function matchedWaitlistEntry(): BelongsTo
    {
        return $this->belongsTo(AppointmentWaitlistEntry::class, 'matched_waitlist_entry_id');
    }

    // Scopes
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', FreedSlotStatus::Available->value);
    }

    public function scopeForPractitioner(Builder $query, int $practitionerId): Builder
    {
        return $query->where('practitioner_id', $practitionerId);
    }

    public function scopeForSpeciality(Builder $query, int $specialityId): Builder
    {
        return $query->where('medical_speciality_id', $specialityId);
    }

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where('expires_at', '>=', now());
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('expires_at', '<', now())
            ->where('status', '!=', FreedSlotStatus::Expired->value);
    }

    // Métodos
    /**
     * Calculate match score with a waitlist entry
     * Returns score from 0-100
     */
    public function matchScore(AppointmentWaitlistEntry $entry): float
    {
        $score = 0.0;

        // Match exacto de fecha preferida (0-50 puntos)
        if ($entry->preferred_date && $entry->preferred_date->equalTo($this->slot_date)) {
            $score += 50;
        } elseif ($entry->is_flexible_date) {
            // Fecha flexible: 20 puntos si coincide con rango preferido
            if ($entry->preferred_date && $this->slot_date->between(
                $entry->preferred_date,
                $entry->preferred_date->addDays($entry->max_wait_days)
            )) {
                $score += 20;
            } else {
                $score += 10; // Solo flexibilidad de fecha
            }
        }

        // Match de rango horario (0-30 puntos)
        $slotStart = Carbon::createFromTimeString($this->slot_start_time);
        $slotEnd = Carbon::createFromTimeString($this->slot_end_time);

        if ($entry->preferred_time) {
            $preferredTime = Carbon::createFromTimeString($entry->preferred_time);
            if ($preferredTime->between($slotStart, $slotEnd)) {
                $score += 30;
            }
        }

        if ($entry->preferred_time_range_start && $entry->preferred_time_range_end) {
            $rangeStart = Carbon::createFromTimeString($entry->preferred_time_range_start);
            $rangeEnd = Carbon::createFromTimeString($entry->preferred_time_range_end);

            // Verificar si el slot coincide parcialmente con el rango
            if ($slotStart->between($rangeStart, $rangeEnd) ||
                $slotEnd->between($rangeStart, $rangeEnd) ||
                ($slotStart->lessThanOrEqualTo($rangeStart) && $slotEnd->greaterThanOrEqualTo($rangeEnd))) {
                $score += 20;
            }
        } elseif ($entry->is_flexible_time) {
            $score += 5; // Solo flexibilidad de hora
        }

        // Prioridad del paciente (0-20 puntos)
        $score += ($entry->priority_score / 5); // Normalizar a 0-20

        return min($score, 100);
    }

    /**
     * Mark as matched
     */
    public function markAsMatched(AppointmentWaitlistEntry $entry): void
    {
        $this->update([
            'status' => FreedSlotStatus::Matched->value,
            'matched_waitlist_entry_id' => $entry->id,
            'matched_at' => now(),
        ]);
    }

    /**
     * Mark as expired
     */
    public function markAsExpired(): void
    {
        $this->update([
            'status' => FreedSlotStatus::Expired->value,
        ]);
    }

    /**
     * Mark as manually filled
     */
    public function markAsManuallyFilled(): void
    {
        $this->update([
            'status' => FreedSlotStatus::ManuallyFilled->value,
        ]);
    }

    /**
     * Check if slot has expired
     */
    public function hasExpired(): bool
    {
        return $this->expires_at <= now();
    }
}
