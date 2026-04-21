<?php

namespace App\Observers;

use App\Helpers\CacheHelper;

use App\Models\Patient;
use Illuminate\Support\Facades\Cache;

class PatientObserver
{
    /**
     * Handle the Patient "created" event.
     */
    public function created(Patient $patient): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Handle the Patient "updated" event.
     */
    public function updated(Patient $patient): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Handle the Patient "deleted" event.
     */
    public function deleted(Patient $patient): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Handle the Patient "restored" event.
     */
    public function restored(Patient $patient): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Handle the Patient "force deleted" event.
     */
    public function forceDeleted(Patient $patient): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Clear dashboard cache for patients
     */
    private function clearDashboardCache(): void
    {
        Cache::tags(['dashboard', 'patients'])->flush();
        Cache::tags(['doctor_dashboard', 'patients'])->flush();
        Cache::tags(['doctor_dashboard', 'demographics'])->flush(); // Age blocks
    }
}
