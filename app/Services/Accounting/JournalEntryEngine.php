<?php

namespace App\Services\Accounting;

use App\Enums\JournalEntryStatus;
use App\Models\Accounting\AccountingEventConfig;
use App\Models\Accounting\AccountingPeriod;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\JournalEntryLine;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JournalEntryEngine
{
    /**
     * Create a journal entry from an accounting event.
     *
     * @param  array  $additionalData  [entry_date, source_type, source_id, cost_center_id, branch_id]
     *
     * @throws \Exception
     */
    public function createEntryFromEvent(
        Client $client,
        int $accountingEventId,
        float $amount,
        string $description = '',
        array $additionalData = [],
        ?User $user = null
    ): ?JournalEntry {
        $user = $user ?? auth()->user();

        // Get event configuration
        $config = AccountingEventConfig::where([
            ['client_id', $client->id],
            ['accounting_event_id', $accountingEventId],
            ['status', 'active'],
        ])->first();

        if (! $config || ! $config->auto_generate) {
            return null;
        }

        return DB::transaction(function () use (
            $client,
            $config,
            $amount,
            $description,
            $additionalData,
            $user
        ) {
            $entryDate = $additionalData['entry_date'] ?? now()->toDateString();
            $sourceType = $additionalData['source_type'] ?? null;
            $sourceId = $additionalData['source_id'] ?? null;
            $costCenterId = $additionalData['cost_center_id'] ?? $config->default_cost_center_id;
            $branchId = $additionalData['branch_id'] ?? null;

            // Get accounting period
            $accountingPeriod = $this->getAccountingPeriod($client, $entryDate);

            // Create journal entry
            $journalEntry = JournalEntry::create([
                'uuid' => Str::uuid(),
                'client_id' => $client->id,
                'entry_number' => $this->generateEntryNumber($client),
                'entry_date' => $entryDate,
                'document_type' => null,
                'document_number' => null,
                'description' => $description,
                'status' => JournalEntryStatus::DRAFT,
                'accounting_period_id' => $accountingPeriod?->id,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            // Create debit line
            JournalEntryLine::create([
                'uuid' => Str::uuid(),
                'journal_entry_id' => $journalEntry->id,
                'accounting_account_id' => $config->debit_account_id,
                'cost_center_id' => $costCenterId,
                'branch_id' => $branchId,
                'debit' => $amount,
                'credit' => 0,
                'description' => $description,
            ]);

            // Create credit line
            JournalEntryLine::create([
                'uuid' => Str::uuid(),
                'journal_entry_id' => $journalEntry->id,
                'accounting_account_id' => $config->credit_account_id,
                'cost_center_id' => $costCenterId,
                'branch_id' => $branchId,
                'debit' => 0,
                'credit' => $amount,
                'description' => $description,
            ]);

            return $journalEntry;
        });
    }

    /**
     * Post a journal entry (record it in the accounting system).
     */
    public function postEntry(JournalEntry $journalEntry, User $user): void
    {
        $journalEntry->post($user);
    }

    /**
     * Generate a unique entry number.
     */
    private function generateEntryNumber(Client $client): string
    {
        $lastEntry = JournalEntry::where('client_id', $client->id)
            ->orderBy('entry_number', 'desc')
            ->first();

        if (! $lastEntry) {
            return 'ASN-001';
        }

        // Extract number from entry_number
        preg_match('/(\d+)$/', $lastEntry->entry_number, $matches);
        $nextNumber = isset($matches[1]) ? (int) $matches[1] + 1 : 1;

        return 'ASN-'.str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get or create an accounting period for a given date.
     *
     * @return AccountingPeriod|null
     */
    private function getAccountingPeriod(Client $client, string $date)
    {
        // This assumes an AccountingPeriod model exists
        // If not, return null for now
        $periodClass = 'App\\Models\\Accounting\\AccountingPeriod';
        if (! class_exists($periodClass)) {
            return null;
        }

        $dateCarbon = Carbon::parse($date);
        $year = $dateCarbon->year;

        // Find accounting period by date range
        return $periodClass::where('client_id', $client->id)
            ->where('fiscal_year', $year)
            ->where('status', 'open')
            ->whereDate('start_date', '<=', $dateCarbon)
            ->whereDate('end_date', '>=', $dateCarbon)
            ->first();
    }
}
