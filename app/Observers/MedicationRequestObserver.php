<?php

namespace App\Observers;

use App\Helpers\CacheHelper;

use App\Models\MedicationRequest;
use Illuminate\Support\Facades\Cache;

class MedicationRequestObserver
{
    /**
     * Handle the MedicationRequest "created" event.
     */
    public function created(MedicationRequest $medicationRequest): void
    {
        $this->clearMedicationsCache($medicationRequest);
    }

    /**
     * Handle the MedicationRequest "updated" event.
     */
    public function updated(MedicationRequest $medicationRequest): void
    {
        $this->clearMedicationsCache($medicationRequest);
    }

    /**
     * Handle the MedicationRequest "deleted" event.
     */
    public function deleted(MedicationRequest $medicationRequest): void
    {
        $this->clearMedicationsCache($medicationRequest);
    }

    /**
     * Handle the MedicationRequest "restored" event.
     */
    public function restored(MedicationRequest $medicationRequest): void
    {
        $this->clearMedicationsCache($medicationRequest);
    }

    /**
     * Handle the MedicationRequest "force deleted" event.
     */
    public function forceDeleted(MedicationRequest $medicationRequest): void
    {
        $this->clearMedicationsCache($medicationRequest);
    }

    /**
     * Clear medications cache for the practitioner
     */
    private function clearMedicationsCache(MedicationRequest $medicationRequest): void
    {
        if ($medicationRequest->practitioner_id) {
            // Clear cache específico del practitioner
            CacheHelper::flush(['doctor_dashboard', 'medications', 'practitioner_'.$medicationRequest->practitioner_id]);
        }

        // Clear general medications cache
        CacheHelper::flush(['doctor_dashboard', 'medications']);
    }
}
