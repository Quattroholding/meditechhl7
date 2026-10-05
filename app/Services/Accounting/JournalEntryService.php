<?php

namespace App\Services\Accounting;

use App\Enums\JournalEntryStatus;
use App\Models\AccountingAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Support\Collection;

class JournalEntryService
{
    protected AccountingService $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    /**
     * Crea un asiento manual
     */
    public function createManualEntry(int $clientId, array $data): JournalEntry
    {
        $data['client_id'] = $clientId;
        $data['document_type'] = 'manual';
        $data['status'] = JournalEntryStatus::DRAFT;
        $data['created_by'] = auth()->id();

        // Obtener o crear período
        if (! isset($data['accounting_period_id'])) {
            $period = $this->accountingService->getCurrentPeriod($clientId);
            $data['accounting_period_id'] = $period->id;
        }

        $entry = JournalEntry::create($data);

        // Crear líneas
        if (isset($data['lines']) && is_array($data['lines'])) {
            foreach ($data['lines'] as $lineData) {
                $this->addLine($entry, $lineData);
            }
        }

        return $entry;
    }

    /**
     * Agrega una línea a un asiento
     */
    public function addLine(JournalEntry $entry, array $data): JournalEntryLine
    {
        if ($entry->status !== JournalEntryStatus::DRAFT) {
            throw new \Exception('No se puede agregar líneas a asiento contabilizado');
        }

        $line = JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'accounting_account_id' => $data['accounting_account_id'],
            'cost_center_id' => $data['cost_center_id'] ?? null,
            'branch_id' => $data['branch_id'] ?? null,
            'debit' => $data['debit'] ?? 0,
            'credit' => $data['credit'] ?? 0,
            'description' => $data['description'] ?? null,
        ]);

        return $line;
    }

    /**
     * Contabiliza un asiento
     */
    public function postEntry(JournalEntry $entry, ?int $userId = null): void
    {
        $this->validateEntry($entry);
        $entry->post($userId);
    }

    /**
     * Valida que un asiento pueda contabilizarse
     */
    public function validateEntry(JournalEntry $entry): void
    {
        if ($entry->status !== JournalEntryStatus::DRAFT) {
            throw new \Exception('Solo se pueden contabilizar asientos en borrador');
        }

        if (! $entry->isBalanced()) {
            throw new \Exception(sprintf(
                'Asiento desbalanceado. Débitos: %s, Créditos: %s',
                $entry->getTotalDebit(),
                $entry->getTotalCredit()
            ));
        }

        if (! $entry->accountingPeriod->isOpen()) {
            throw new \Exception('No se puede contabilizar en período cerrado');
        }

        // Validar que todas las cuentas permitan movimiento
        foreach ($entry->lines as $line) {
            if (! $line->accountingAccount->allows_transaction) {
                throw new \Exception(
                    "Cuenta {$line->accountingAccount->code} no permite movimientos"
                );
            }
        }
    }

    /**
     * Revierte un asiento
     */
    public function reverseEntry(JournalEntry $entry, string $reason = ''): JournalEntry
    {
        return $entry->reverse($reason);
    }

    /**
     * Obtiene asientos de un período con filtros
     */
    public function getEntriesByPeriod(int $clientId, int $periodId, array $filters = []): Collection
    {
        $query = JournalEntry::where('client_id', $clientId)
            ->where('accounting_period_id', $periodId);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['document_type'])) {
            $query->where('document_type', $filters['document_type']);
        }

        if (isset($filters['start_date'])) {
            $query->whereDate('entry_date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->whereDate('entry_date', '<=', $filters['end_date']);
        }

        return $query->orderBy('entry_date')->orderBy('created_at')->get();
    }

    /**
     * Obtiene el total de débitos y créditos de un asiento
     */
    public function getEntrySummary(JournalEntry $entry): array
    {
        return [
            'total_debit' => $entry->getTotalDebit(),
            'total_credit' => $entry->getTotalCredit(),
            'is_balanced' => $entry->isBalanced(),
            'difference' => $entry->getTotalDebit() - $entry->getTotalCredit(),
            'line_count' => $entry->lines()->count(),
        ];
    }
}
