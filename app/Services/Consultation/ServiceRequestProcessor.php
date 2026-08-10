<?php

namespace App\Services\Consultation;

use App\Models\Encounter;

class ServiceRequestProcessor
{
    /**
     * Procesar todos los service requests para el encounter finalizado
     *
     * Actualiza procedimientos realizados a 'completed' y otros a 'active'
     */
    public function processServiceRequests(Encounter $encounter): void
    {
        // Si el procedimiento se realizó en la consulta, marcar como completed
        $encounter->serviceRequests()
            ->where('status', 'draft')
            ->where('performed_in_consultation', true)
            ->update(['status' => 'completed']);

        // Los demás service requests se marcan como active
        $encounter->serviceRequests()
            ->where('status', 'draft')
            ->update(['status' => 'active']);
    }
}
