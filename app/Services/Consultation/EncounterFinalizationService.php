<?php

namespace App\Services\Consultation;

use App\Models\Appointment;
use App\Models\Encounter;
use App\Services\EncounterSnapshotService;
use Illuminate\Support\Facades\Log;

class EncounterFinalizationService
{
    public function __construct(
        protected EncounterSnapshotService $snapshotService
    ) {}

    /**
     * Finalizar encounter y appointment
     */
    public function finalizeConsultation(
        Encounter $encounter,
        Appointment $appointment,
        bool $isAuthorized
    ): void {
        if (! $isAuthorized) {
            throw new \Exception('No autorizado para finalizar esta consulta.');
        }

        // Actualizar estatus de appointment a 'fulfilled'
        $appointment->update(['status' => 'fulfilled']);

        // Actualizar estatus de encounter a 'finished' y establecer datetime
        $wasAlreadyFinished = $encounter->getRawOriginal('status') === 'finished';

        // Solo establecer tiempo final la primera vez que se finaliza
        if ($encounter->status !== 'finished') {
            $encounter->status = 'finished';
            $encounter->end = now();
            $encounter->save();
        }

        // Si el encounter ya estaba finalizado y no tiene snapshots,
        // crear snapshot inicial (compatibilidad retroactiva)
        if ($wasAlreadyFinished && $encounter->snapshots()->count() === 0) {
            $this->snapshotService->createSnapshot(
                $encounter,
                'initial_finish',
                'Snapshot inicial creado al activar funcionalidad'
            );
            Log::info('Snapshot inicial creado para encounter ya finalizado', [
                'encounter_id' => $encounter->id,
            ]);
        }
    }

    /**
     * Verificar estado de notificación de prescripción
     *
     * Retorna información sobre si se enviarán prescripciones al correo del paciente
     */
    public function checkPrescriptionNotification(Encounter $encounter): array
    {
        $hasMedications = $encounter->medicationRequests()->exists();
        $hasServiceRequests = $encounter->serviceRequests()->exists();

        $patientEmail = $encounter->patient?->email ?? '';
        $willSend = ($hasMedications || $hasServiceRequests) && ! empty($patientEmail);

        return [
            'has_medications' => $hasMedications,
            'has_service_requests' => $hasServiceRequests,
            'will_send' => $willSend,
            'patient_email' => $patientEmail,
        ];
    }
}
