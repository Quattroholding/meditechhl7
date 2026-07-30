<?php

namespace App\Observers;

use App\Models\ClientSubscription;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ClientSubscriptionObserver
{
    /**
     * Handle the ClientSubscription "created" event.
     */
    public function created(ClientSubscription $subscription): void
    {
        // Clear cache when a new subscription is created
        $this->clearPractitionerCache($subscription, 'created');
    }

    /**
     * Handle the ClientSubscription "updated" event.
     */
    public function updated(ClientSubscription $subscription): void
    {
        // Only clear cache if status or package_id changed (affects practitioner availability)
        if ($subscription->isDirty('status') || $subscription->isDirty('package_id')) {
            $this->clearPractitionerCache($subscription, 'updated');
        }
    }

    /**
     * Handle the ClientSubscription "deleted" event.
     */
    public function deleted(ClientSubscription $subscription): void
    {
        //
    }

    /**
     * Handle the ClientSubscription "restored" event.
     */
    public function restored(ClientSubscription $subscription): void
    {
        //
    }

    /**
     * Handle the ClientSubscription "force deleted" event.
     */
    public function forceDeleted(ClientSubscription $subscription): void
    {
        //
    }

    /**
     * Clear practitioner-related cache when subscription changes
     */
    private function clearPractitionerCache(ClientSubscription $subscription, string $event): void
    {
        try {
            // Get the client_id from the subscription
            $clientId = $subscription->client_id;

            if ($clientId) {
                // Clear specific cache keys related to practitioners
                $keysToForget = [
                    'practitioners_list',
                    "client_{$clientId}_practitioners",
                    'api_practitioners_list',
                ];

                foreach ($keysToForget as $key) {
                    Cache::forget($key);
                }

                // Clear cache tags if using Redis/Memcached
                // File and database drivers don't support tagging
                $cacheDriver = config('cache.default');
                if (in_array($cacheDriver, ['redis', 'memcached', 'array'])) {
                    Cache::tags(['practitioners', 'subscriptions'])->flush();
                    Cache::tags(['dashboard', 'subscriptions'])->flush();
                }

                Log::info('ClientSubscriptionObserver - Cache cleared', [
                    'event' => $event,
                    'subscription_id' => $subscription->id,
                    'client_id' => $clientId,
                    'old_status' => $subscription->getOriginal('status'),
                    'new_status' => $subscription->status,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('ClientSubscriptionObserver - Failed to clear cache', [
                'error' => $e->getMessage(),
                'subscription_id' => $subscription->id,
            ]);
        }
    }
}
