<?php

namespace App\Observers;

use App\Models\Client;
use App\Services\Accounting\AccountingService;
use Illuminate\Support\Facades\Log;

class ClientObserver
{
    public function __construct(
        private AccountingService $accountingService
    ) {}

    public function updated(Client $client): void
    {
        // Check if accounting_enabled was just enabled (changed from false to true)
        if ($client->isDirty('accounting_enabled') && $client->accounting_enabled) {
            try {
                $createdCount = $this->accountingService->loadBaseAccountsForClient($client->id);

                Log::info('Accounting module enabled for client', [
                    'client_id' => $client->id,
                    'client_name' => $client->name,
                    'accounts_created' => $createdCount,
                ]);
            } catch (\Exception $e) {
                Log::error('Error enabling accounting module for client', [
                    'client_id' => $client->id,
                    'error' => $e->getMessage(),
                ]);

                throw $e;
            }
        }
    }
}
