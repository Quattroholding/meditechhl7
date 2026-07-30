<?php

namespace App\Observers;

use App\Models\ClientInvoicePayment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ClientInvoicePaymentObserver
{
    /**
     * Handle the ClientInvoicePayment "created" event.
     */
    public function created(ClientInvoicePayment $clientInvoicePayment): void
    {
        $this->clearDashboardCache($clientInvoicePayment);
    }

    /**
     * Handle the ClientInvoicePayment "updated" event.
     */
    public function updated(ClientInvoicePayment $clientInvoicePayment): void
    {
        $this->clearDashboardCache($clientInvoicePayment);
    }

    /**
     * Handle the ClientInvoicePayment "deleted" event.
     */
    public function deleted(ClientInvoicePayment $clientInvoicePayment): void
    {
        $this->clearDashboardCache($clientInvoicePayment);
    }

    /**
     * Handle the ClientInvoicePayment "restored" event.
     */
    public function restored(ClientInvoicePayment $clientInvoicePayment): void
    {
        $this->clearDashboardCache($clientInvoicePayment);
    }

    /**
     * Handle the ClientInvoicePayment "force deleted" event.
     */
    public function forceDeleted(ClientInvoicePayment $clientInvoicePayment): void
    {
        $this->clearDashboardCache($clientInvoicePayment);
    }

    /**
     * Clear dashboard cache when client invoice payment changes
     */
    private function clearDashboardCache(ClientInvoicePayment $clientInvoicePayment): void
    {
        try {
            $clientId = $clientInvoicePayment->invoice?->client_id;

            if ($clientId) {
                $cacheDriver = config('cache.default');
                if (in_array($cacheDriver, ['redis', 'memcached', 'array'])) {
                    Cache::tags(['dashboard', 'subscriptions'])->flush();
                    Cache::tags(['dashboard', 'receivable_subscriptions'])->flush();
                }

                Log::info('ClientInvoicePaymentObserver - Dashboard cache cleared', [
                    'client_id' => $clientId,
                    'payment_id' => $clientInvoicePayment->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('ClientInvoicePaymentObserver - Failed to clear cache', [
                'error' => $e->getMessage(),
                'payment_id' => $clientInvoicePayment->id,
            ]);
        }
    }
}
