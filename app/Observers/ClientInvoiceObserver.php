<?php

namespace App\Observers;

use App\Models\ClientInvoice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ClientInvoiceObserver
{
    /**
     * Handle the ClientInvoice "created" event.
     */
    public function created(ClientInvoice $clientInvoice): void
    {
        $this->clearDashboardCache($clientInvoice);
    }

    /**
     * Handle the ClientInvoice "updated" event.
     */
    public function updated(ClientInvoice $clientInvoice): void
    {
        $this->clearDashboardCache($clientInvoice);
    }

    /**
     * Handle the ClientInvoice "deleted" event.
     */
    public function deleted(ClientInvoice $clientInvoice): void
    {
        $this->clearDashboardCache($clientInvoice);
    }

    /**
     * Handle the ClientInvoice "restored" event.
     */
    public function restored(ClientInvoice $clientInvoice): void
    {
        $this->clearDashboardCache($clientInvoice);
    }

    /**
     * Handle the ClientInvoice "force deleted" event.
     */
    public function forceDeleted(ClientInvoice $clientInvoice): void
    {
        $this->clearDashboardCache($clientInvoice);
    }

    /**
     * Clear dashboard cache when client invoice changes
     */
    private function clearDashboardCache(ClientInvoice $clientInvoice): void
    {
        try {
            $clientId = $clientInvoice->client_id;

            if ($clientId) {
                $cacheDriver = config('cache.default');
                if (in_array($cacheDriver, ['redis', 'memcached', 'array'])) {
                    Cache::tags(['dashboard', 'subscriptions'])->flush();
                    Cache::tags(['dashboard', 'receivable_subscriptions'])->flush();
                }

                Log::info('ClientInvoiceObserver - Dashboard cache cleared', [
                    'client_id' => $clientId,
                    'invoice_id' => $clientInvoice->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('ClientInvoiceObserver - Failed to clear cache', [
                'error' => $e->getMessage(),
                'invoice_id' => $clientInvoice->id,
            ]);
        }
    }
}
