<?php

namespace App\Services\Consultation;

use App\Models\Appointment;
use App\Models\Encounter;

class ConsultationValidator
{
    /**
     * Validar prerequisitos para finalizar consulta
     *
     * @return array{appointment: Appointment, encounter: Encounter, client_id: int}
     *
     * @throws \Exception si la validación falla
     */
    public function validateConsultationFinalization(int $appointmentId): array
    {
        // 1. Validar que la cita existe
        $appointment = Appointment::find($appointmentId);
        if (! $appointment) {
            throw new \Exception('Cita no encontrada.');
        }

        // 2. Validar que el client_id está disponible
        $clientId = $appointment->client_id ?? auth()->user()->getCurrentClient()?->id;
        if (! $clientId) {
            throw new \Exception('No se pudo determinar el client_id de la consulta.');
        }

        // 3. Validar que el encounter existe
        $encounter = Encounter::whereAppointmentId($appointment->id)->first();
        if (! $encounter) {
            throw new \Exception('Encounter no encontrado.');
        }

        return [
            'appointment' => $appointment,
            'encounter' => $encounter,
            'client_id' => $clientId,
        ];
    }

    /**
     * Verificar si el usuario está autorizado para finalizar (practitioner o asistente)
     */
    public function isUserAuthorizedToFinalize(Appointment $appointment): bool
    {
        $userId = auth()->id();
        $practitionerUserId = $appointment->practitioner->user_id ?? null;
        $assistedByUserId = $appointment->assisted_by;

        return $userId === $practitionerUserId || $userId === $assistedByUserId;
    }
}
