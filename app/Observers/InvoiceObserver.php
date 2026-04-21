<?php

namespace App\Observers;

use App\Helpers\CacheHelper;

use App\Models\Invoice;
use Illuminate\Support\Facades\Cache;

class InvoiceObserver
{
    /**
     * Handle the Invoice "created" event.
     */
    public function created(Invoice $invoice): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Handle the Invoice "updated" event.
     */
    public function updated(Invoice $invoice): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Handle the Invoice "deleted" event.
     */
    public function deleted(Invoice $invoice): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Handle the Invoice "restored" event.
     */
    public function restored(Invoice $invoice): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Handle the Invoice "force deleted" event.
     */
    public function forceDeleted(Invoice $invoice): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Clear dashboard cache for invoices
     */
    private function clearDashboardCache(): void
    {
        CacheHelper::flush(['dashboard', 'invoices']);
    }
}
