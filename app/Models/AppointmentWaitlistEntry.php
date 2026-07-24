<?php

namespace App\Models;

use App\Enums\AppointmentStatusEnum;
use App\Enums\FreedSlotSource;
use App\Enums\WaitlistStatus;
use App\Enums\WaitlistUrgencyLevel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentWaitlistEntry extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'appointment_id',
        'patient_id',
        'practitioner_id',
        'medical_speciality_id',
        'consulting_room_id',
        'client_id',
        'preferred_date',
        'preferred_time',
        'preferred_time_range_start',
        'preferred_time_range_end',
        'is_flexible_date',
        'is_flexible_time',
        'max_wait_days',
        'priority_score',
        'urgency_level',
        'reason',
        'status',
        'assigned_at',
        'expires_at',
        'notification_sent_at',
        'notification_count',
        'last_notification_at',
        'created_by',
        'cancelled_by',
        'cancellation_reason',
        'cancelled_at',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'preferred_time' => 'datetime:H:i',
        'preferred_time_range_start' => 'datetime:H:i',
        'preferred_time_range_end' => 'datetime:H:i',
        'is_flexible_date' => 'boolean',
        'is_flexible_time' => 'boolean',
        'max_wait_days' => 'integer',
        'priority_score' => 'decimal:2',
        'urgency_level' => WaitlistUrgencyLevel::class,
        'status' => WaitlistStatus::class,
        'assigned_at' => 'datetime',
        'expires_at' => 'datetime',
        'notification_sent_at' => 'datetime',
        'notification_count' => 'integer',
        'last_notification_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Relaciones
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }

    public function medicalSpeciality(): BelongsTo
    {
        return $this->belongsTo(MedicalSpeciality::class, 'medical_speciality_id');
    }

    public function consultingRoom(): BelongsTo
    {
        return $this->belongsTo(ConsultingRoom::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cancelledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function freedSlots(): HasMany
    {
        return $this->hasMany(AppointmentFreedSlot::class, 'matched_waitlist_entry_id');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', WaitlistStatus::Active->value);
    }

    public function scopeOrderedByPriority(Builder $query): Builder
    {
        return $query->orderBy('priority_score', 'desc')
            ->orderBy('created_at', 'asc');
    }

    public function scopeForPractitioner(Builder $query, int $practitionerId): Builder
    {
        return $query->where('practitioner_id', $practitionerId);
    }

    public function scopeForSpeciality(Builder $query, int $specialityId): Builder
    {
        return $query->where('medical_speciality_id', $specialityId);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('expires_at', '<', now())
            ->where('status', '!=', WaitlistStatus::Expired->value);
    }

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where('expires_at', '>=', now());
    }

    // Métodos
    /**
     * Calculate priority score based on urgency, waiting time, and flexibility
     */
    public function calculatePriority(): float
    {
        $score = 0.0;

        // Urgencia (0-40 puntos)
        $score += $this->urgency_level->priorityPoints();

        // Antigüedad (0-30 puntos): días esperando × 2
        $createdAt = $this->created_at instanceof Carbon ? $this->created_at : now();
        $daysWaiting = $createdAt->diffInDays(now());
        $score += min($daysWaiting * 2, 30);

        // Flexibilidad (0-20 puntos)
        if ($this->is_flexible_date) {
            $score += 10;
        }
        if ($this->is_flexible_time) {
            $score += 10;
        }

        // Paciente recurrente (0-10 puntos)
        $previousAppointments = Appointment::where('patient_id', $this->patient_id)
            ->where('status', AppointmentStatusEnum::Fulfilled->value)
            ->count();

        $score += min($previousAppointments, 10);

        return min($score, 100);
    }

    /**
     * Check if entry can receive another notification
     */
    public function canReceiveNotification(): bool
    {
        if ($this->notification_sent_at === null) {
            return true;
        }

        // Máximo una notificación por día
        return $this->notification_sent_at->addDay() <= now();
    }

    /**
     * Check if entry is approaching expiration (less than 3 days)
     */
    public function isApproachingExpiration(): bool
    {
        return $this->expires_at->diffInDays(now()) <= 3;
    }

    /**
     * Mark as assigned
     */
    public function markAsAssigned(): void
    {
        $this->update([
            'status' => WaitlistStatus::Assigned->value,
            'assigned_at' => now(),
        ]);
    }

    /**
     * Mark as expired
     */
    public function markAsExpired(): void
    {
        $this->update([
            'status' => WaitlistStatus::Expired->value,
        ]);
    }

    /**
     * Cancel the entry
     */
    public function cancel(User $cancelledBy, string $reason = null): void
    {
        $this->update([
            'status' => WaitlistStatus::Cancelled->value,
            'cancelled_by' => $cancelledBy->id,
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Update priority score
     */
    public function updatePriority(): void
    {
        $this->saveQuietly(function (self $model) {
            $model->priority_score = $model->calculatePriority();
        });
    }
}
