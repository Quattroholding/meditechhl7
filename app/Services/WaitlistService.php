<?php

namespace App\Services;

use App\Enums\AppointmentStatusEnum;
use App\Enums\FreedSlotStatus;
use App\Enums\WaitlistStatus;
use App\Enums\WaitlistUrgencyLevel;
use App\Models\Appointment;
use App\Models\AppointmentFreedSlot;
use App\Models\AppointmentWaitlistEntry;
use App\Models\User;
use App\Notifications\AppointmentAddedToWaitlistNotification;
use App\Notifications\AppointmentBookedNotification;
use App\Notifications\WaitlistEntryCancelledNotification;
use App\Notifications\WaitlistEntryExpiredNotification;
use App\Notifications\WaitlistSlotAvailableNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class WaitlistService
{
    /**
     * Add an appointment to the waitlist
     */
    public function addToWaitlist(
        Appointment $appointment,
        array $preferences,
        User $createdBy
    ): AppointmentWaitlistEntry {
        // Cambiar estado de la cita a waitlist
        $appointment->update(['status' => AppointmentStatusEnum::Waitlist->value]);

        // Calcular fecha de expiración (por defecto 30 días)
        $maxWaitDays = $preferences['max_wait_days'] ?? 30;
        $expiresAt = now()->addDays($maxWaitDays);

        // Crear entrada en la lista de espera
        $entry = AppointmentWaitlistEntry::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'practitioner_id' => $appointment->practitioner_id,
            'medical_speciality_id' => $appointment->medical_speciality_id,
            'consulting_room_id' => $appointment->consulting_room_id,
            'client_id' => $appointment->client_id,
            'preferred_date' => $preferences['preferred_date'] ?? null,
            'preferred_time' => $preferences['preferred_time'] ?? null,
            'preferred_time_range_start' => $preferences['preferred_time_range_start'] ?? null,
            'preferred_time_range_end' => $preferences['preferred_time_range_end'] ?? null,
            'is_flexible_date' => $preferences['is_flexible_date'] ?? false,
            'is_flexible_time' => $preferences['is_flexible_time'] ?? false,
            'max_wait_days' => $maxWaitDays,
            'urgency_level' => $preferences['urgency_level'] ?? WaitlistUrgencyLevel::Routine->value,
            'reason' => $preferences['reason'] ?? null,
            'status' => WaitlistStatus::Active->value,
            'expires_at' => $expiresAt,
            'created_by' => $createdBy->id,
        ]);

        // Calcular score de prioridad inicial
        $entry->priority_score = $entry->calculatePriority();
        $entry->save();

        // Enviar notificación al paciente
        $this->notifyWaitlistEntry($entry, 'added');

        return $entry;
    }

    /**
     * Register a freed slot when an appointment is cancelled or no-show
     */
    public function registerFreedSlot(
        Appointment $cancelledAppointment,
        string $source = 'cancellation'
    ): ?AppointmentFreedSlot {
        // Validar que la cita está siendo cancelada/no-show
        if (! in_array($cancelledAppointment->status->value, ['booked', 'confirm', 'arrived'])) {
            \Log::warning('registerFreedSlot retornando null: Estado de cita no válido', [
                'appointment_id' => $cancelledAppointment->id,
                'status' => $cancelledAppointment->status->value,
                'valid_statuses' => ['booked', 'confirm', 'arrived'],
            ]);

            return null;
        }

        // Validar que el slot está en el futuro con al menos 30 minutos de diferencia
        if ($cancelledAppointment->start->lessThanOrEqualTo(now()->addMinutes(30))) {
            \Log::warning('registerFreedSlot retornando null: Cita muy pronto', [
                'appointment_id' => $cancelledAppointment->id,
                'appointment_start' => $cancelledAppointment->start->format('Y-m-d H:i:s'),
                'now' => now()->format('Y-m-d H:i:s'),
                'minutos_hasta_cita' => now()->diffInMinutes($cancelledAppointment->start),
                'minimo_requerido' => 30,
            ]);

            return null;
        }

        // Calcular expires_at (2 horas antes del inicio de la cita)
        $expiresAt = $cancelledAppointment->start->subHours(2);

        // Crear entrada de slot liberado
        $freedSlot = AppointmentFreedSlot::create([
            'practitioner_id' => $cancelledAppointment->practitioner_id,
            'consulting_room_id' => $cancelledAppointment->consulting_room_id,
            'medical_speciality_id' => $cancelledAppointment->medical_speciality_id,
            'client_id' => $cancelledAppointment->client_id,
            'slot_date' => $cancelledAppointment->start->toDateString(),
            'slot_start_time' => $cancelledAppointment->start->toTimeString(),
            'slot_end_time' => $cancelledAppointment->end->toTimeString(),
            'duration_minutes' => $cancelledAppointment->minutes_duration ?? 30,
            'freed_by' => $source,
            'cancelled_appointment_id' => $cancelledAppointment->id,
            'status' => FreedSlotStatus::Available->value,
            'expires_at' => $expiresAt,
        ]);

        // Respetar configuración del cliente para auto-asignación
        $client = $cancelledAppointment->client;
        $autoAssign = $client->getSettings('waitlist_auto_assign', false);

        if ($autoAssign) {
            // Buscar y notificar matches automáticamente
            $this->findAndNotifyMatches($freedSlot);
        }

        return $freedSlot;
    }

    /**
     * Get suggested candidates for a freed slot (for manual assignment by receptionist)
     */
    public function getSuggestedCandidates(AppointmentFreedSlot $freedSlot, int $limit = 5): \Illuminate\Support\Collection
    {
        // Obtener entradas activas y no expiradas
        $activeEntries = AppointmentWaitlistEntry::query()
            ->where('client_id', $freedSlot->client_id)
            ->where(function ($query) use ($freedSlot) {
                // Mismo doctor O misma especialidad
                $query->where('practitioner_id', $freedSlot->practitioner_id)
                    ->orWhere(function ($q) use ($freedSlot) {
                        if ($freedSlot->medical_speciality_id) {
                            $q->where('medical_speciality_id', $freedSlot->medical_speciality_id);
                        }
                    });
            })
            ->active()
            ->notExpired()
            ->get();

        if ($activeEntries->isEmpty()) {
            return collect();
        }

        // Calcular match scores y retornar ordenados por score
        return $activeEntries->map(function (AppointmentWaitlistEntry $entry) use ($freedSlot) {
            $createdAt = Carbon::parse($entry->getRawOriginal('created_at'));

            return [
                'entry' => $entry,
                'score' => $freedSlot->matchScore($entry),
                'patient_name' => $entry->patient->name,
                'patient_id' => $entry->patient->id,
                'urgency' => $entry->urgency_level->label(),
                'days_waiting' => (int) ceil($createdAt->diffInDays(now())),
                'priority_score' => $entry->priority_score,
            ];
        })
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }

    /**
     * Find and notify matching waitlist entries for a freed slot
     */
    public function findAndNotifyMatches(AppointmentFreedSlot $freedSlot): void
    {
        // Obtener entradas activas y no expiradas
        $activeEntries = AppointmentWaitlistEntry::query()
            ->where('client_id', $freedSlot->client_id)
            ->where(function ($query) use ($freedSlot) {
                // Mismo doctor O misma especialidad
                $query->where('practitioner_id', $freedSlot->practitioner_id)
                    ->orWhere(function ($q) use ($freedSlot) {
                        if ($freedSlot->medical_speciality_id) {
                            $q->where('medical_speciality_id', $freedSlot->medical_speciality_id);
                        }
                    });
            })
            ->active()
            ->notExpired()
            ->get();

        if ($activeEntries->isEmpty()) {
            return;
        }

        // Calcular match scores
        $matches = $activeEntries->map(function (AppointmentWaitlistEntry $entry) use ($freedSlot) {
            return [
                'entry' => $entry,
                'score' => $freedSlot->matchScore($entry),
            ];
        })
            ->sortByDesc('score')
            ->take(3) // Notificar a los 3 mejores matches
            ->values();

        // Notificar a los matches y marcar el slot
        foreach ($matches as $match) {
            $entry = $match['entry'];

            if ($entry->canReceiveNotification()) {
                $this->notifyWaitlistEntry($entry, 'slot_available', $freedSlot);

                // Actualizar datos de notificación
                $entry->update([
                    'notification_sent_at' => now(),
                    'notification_count' => $entry->notification_count + 1,
                    'last_notification_at' => now(),
                ]);
            }
        }

        // Marcar el slot como emparejado si hay coincidencias
        if ($matches->isNotEmpty()) {
            $topMatch = $matches->first();
            $freedSlot->markAsMatched($topMatch['entry']);
        }
    }

    /**
     * Assign a waitlist entry to a specific time slot
     */
    public function assignFromWaitlist(
        AppointmentWaitlistEntry $entry,
        Carbon $start,
        int $duration,
        ?int $roomId = null,
        ?User $assignedBy = null
    ): Appointment {
        $appointment = $entry->appointment;

        // Validar disponibilidad del slot
        // ⚠️ IMPORTANTE: Excluir la propia cita en waitlist para que no considere conflicto consigo misma
        $conflicts = Appointment::where('client_id', $appointment->client_id)
            ->where('practitioner_id', $appointment->practitioner_id)
            ->where('id', '!=', $appointment->id)  // ← Excluir la propia cita
            ->where('status', '!=', AppointmentStatusEnum::Cancelled->value)
            ->where('status', '!=', AppointmentStatusEnum::Waitlist->value)  // ← Excluir otras citas en waitlist
            ->where(function ($query) use ($start, $duration) {
                $end = $start->copy()->addMinutes($duration);
                // Usar < y > en lugar de <= y >= para permitir citas back-to-back
                // Ejemplo: Si asignas 10:00-10:30, puedes tener otra cita de 10:30-11:00 sin conflicto
                $query->where('start', '<', $end)      // start < end (10:00-10:30)
                    ->where('end', '>', $start);        // end > start (10:00-10:30)
            })
            ->exists();

        if ($conflicts) {
            throw new \Exception('El espacio de tiempo no está disponible');
        }

        // Actualizar cita
        $end = $start->copy()->addMinutes($duration);
        $appointment->update([
            'status' => AppointmentStatusEnum::Booked->value,
            'start' => $start,
            'end' => $end,
            'minutes_duration' => $duration,
            'consulting_room_id' => $roomId ?? $appointment->consulting_room_id,
            'practitioner_suggested_datetime' => $start,
        ]);

        // Marcar entrada como asignada
        $entry->markAsAssigned();

        // Notificar al paciente
        $this->notifyWaitlistEntry($entry, 'assigned', null, $start);

        // Marcar FreedSlot como manualmente llenado si existe
        if ($entry->freedSlots()->exists()) {
            $entry->freedSlots()->latest()->first()?->markAsManuallyFilled();
        }

        return $appointment;
    }

    /**
     * Get prioritized waitlist for a given context
     */
    public function getPrioritizedWaitlist(
        int $clientId,
        ?int $practitionerId = null,
        ?int $specialityId = null,
        int $limit = 50,
        int $offset = 0
    ): Collection {
        $query = AppointmentWaitlistEntry::query()
            ->where('client_id', $clientId)
            ->active()
            ->notExpired();

        if ($practitionerId) {
            $query->where('practitioner_id', $practitionerId);
        }

        if ($specialityId) {
            $query->where('medical_speciality_id', $specialityId);
        }

        return $query->orderedByPriority()
            ->skip($offset)
            ->limit($limit)
            ->get();
    }

    /**
     * Recalculate priority scores for all active entries
     */
    public function recalculatePriorities(): int
    {
        $updated = 0;

        AppointmentWaitlistEntry::active()
            ->notExpired()
            ->chunk(100, function ($entries) use (&$updated) {
                foreach ($entries as $entry) {
                    $entry->updatePriority();
                    $updated++;
                }
            });

        return $updated;
    }

    /**
     * Expire old waitlist entries
     */
    public function expireOldEntries(): int
    {
        $updated = 0;

        AppointmentWaitlistEntry::expired()
            ->chunk(100, function ($entries) use (&$updated) {
                foreach ($entries as $entry) {
                    $entry->markAsExpired();

                    // Notificar al paciente
                    $this->notifyWaitlistEntry($entry, 'expired');

                    $updated++;
                }
            });

        return $updated;
    }

    /**
     * Expire old freed slots
     */
    public function expireOldFreedSlots(): int
    {
        $updated = 0;

        AppointmentFreedSlot::expired()
            ->chunk(100, function ($slots) use (&$updated) {
                foreach ($slots as $slot) {
                    $slot->markAsExpired();
                    $updated++;
                }
            });

        return $updated;
    }

    /**
     * Cancel a waitlist entry
     */
    public function cancelEntry(
        AppointmentWaitlistEntry $entry,
        User $cancelledBy,
        ?string $reason = null
    ): void {
        $entry->cancel($cancelledBy, $reason);

        // Cambiar estado de la cita si es necesario
        if ($entry->appointment->status->value === AppointmentStatusEnum::Waitlist->value) {
            $entry->appointment->update([
                'status' => AppointmentStatusEnum::Cancelled->value,
            ]);
        }

        // Notificar al paciente
        $this->notifyWaitlistEntry($entry, 'cancelled');
    }

    /**
     * Get waitlist statistics for a client
     */
    public function getWaitlistStats(int $clientId): array
    {
        $total = AppointmentWaitlistEntry::where('client_id', $clientId)->active()->notExpired()->count();

        $urgent = AppointmentWaitlistEntry::where('client_id', $clientId)
            ->active()
            ->notExpired()
            ->whereIn('urgency_level', [
                WaitlistUrgencyLevel::Urgent->value,
                WaitlistUrgencyLevel::VeryUrgent->value,
                WaitlistUrgencyLevel::Emergency->value,
            ])
            ->count();

        $expiringInThreeDays = AppointmentWaitlistEntry::where('client_id', $clientId)
            ->active()
            ->notExpired()
            ->whereBetween('expires_at', [now(), now()->addDays(3)])
            ->count();

        return [
            'total' => $total,
            'urgent' => $urgent,
            'expiring_soon' => $expiringInThreeDays,
        ];
    }

    /**
     * Send notification for a waitlist entry
     */
    private function notifyWaitlistEntry(
        AppointmentWaitlistEntry $entry,
        string $type = 'added',
        ?AppointmentFreedSlot $freedSlot = null,
        ?Carbon $assignedTime = null
    ): void {
        $patient = $entry->patient;
        $appointment = $entry->appointment;

        \Log::info('notifyWaitlistEntry() iniciado', [
            'type' => $type,
            'entry_id' => $entry->id,
            'patient_id' => $patient->id,
        ]);

        if ($type === 'assigned' && $assignedTime) {
            // Notificar que la cita ha sido asignada desde la waitlist
            $appointment->refresh();
            $patient->notify(new AppointmentBookedNotification($appointment));

            \Log::info('Notificación de asignación desde waitlist enviada', [
                'entry_id' => $entry->id,
                'appointment_id' => $appointment->id,
                'assigned_time' => $assignedTime->format('Y-m-d H:i'),
            ]);
        } elseif ($type === 'slot_available' && $freedSlot) {
            // Notificar que hay un espacio disponible
            $patient->notify(new WaitlistSlotAvailableNotification($freedSlot));

            \Log::info('Notificación de espacio disponible enviada', [
                'entry_id' => $entry->id,
                'freed_slot_id' => $freedSlot->id,
            ]);
        } elseif ($type === 'expired') {
            // Notificar que la entrada en la lista de espera ha expirado
            $patient->notify(new WaitlistEntryExpiredNotification($entry));

            \Log::info('Notificación de expiración enviada', [
                'entry_id' => $entry->id,
            ]);
        } elseif ($type === 'cancelled') {
            // Notificar que la entrada ha sido cancelada
            $patient->notify(new WaitlistEntryCancelledNotification($entry));

            \Log::info('Notificación de cancelación enviada', [
                'entry_id' => $entry->id,
            ]);
        } elseif ($type === 'added') {
            // Notificar que fue agregado a la lista de espera
            $patient->notify(new AppointmentAddedToWaitlistNotification($entry));

            \Log::info('Notificación de agregación a waitlist enviada', [
                'entry_id' => $entry->id,
                'patient_id' => $patient->id,
            ]);
        }
    }
}
