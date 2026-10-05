<?php

namespace App\Services\Accounting;

use App\Models\AccountingAccount;
use App\Models\AccountingPeriod;
use App\Models\CostCenter;
use App\Models\JournalEntry;
use Illuminate\Support\Collection;

class AccountingService
{
    /**
     * Obtiene el plan de cuentas con filtros opcionales
     */
    public function getChartOfAccounts(int $clientId, array $filters = []): Collection
    {
        $query = AccountingAccount::where('client_id', $clientId);

        if (isset($filters['account_type'])) {
            $query->where('account_type', $filters['account_type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['allows_transaction'])) {
            $query->where('allows_transaction', $filters['allows_transaction']);
        }

        return $query->orderBy('code')->get();
    }

    /**
     * Obtiene la jerarquía de cuentas como árbol
     */
    public function getAccountHierarchy(int $clientId): Collection
    {
        $accounts = $this->getChartOfAccounts($clientId);

        return $accounts->filter(fn ($account) => $account->parent_id === null)
            ->map(fn ($account) => $this->buildAccountTree($account));
    }

    /**
     * Construye el árbol de una cuenta y sus hijos
     */
    private function buildAccountTree(AccountingAccount $account): array
    {
        $children = $account->children()->get();

        return [
            'id' => $account->id,
            'code' => $account->code,
            'name' => $account->name,
            'account_type' => $account->account_type,
            'level' => $account->level,
            'balance' => $account->balance,
            'allows_transaction' => $account->allows_transaction,
            'status' => $account->status,
            'children' => $children->isNotEmpty()
                ? $children->map(fn ($child) => $this->buildAccountTree($child))->toArray()
                : [],
        ];
    }

    /**
     * Crea una nueva cuenta contable
     */
    public function createAccount(int $clientId, array $data): AccountingAccount
    {
        $data['client_id'] = $clientId;
        $data['created_by'] = auth()->id();

        $account = AccountingAccount::create($data);

        if ($account->parent_id) {
            $account->calculateLevel();
            $account->save();
        }

        return $account;
    }

    /**
     * Actualiza una cuenta contable
     */
    public function updateAccount(AccountingAccount $account, array $data): AccountingAccount
    {
        $account->update(array_merge($data, ['updated_by' => auth()->id()]));

        if (isset($data['parent_id'])) {
            $account->calculateLevel();
            $account->save();
        }

        return $account;
    }

    /**
     * Elimina una cuenta (solo si no tiene movimientos)
     */
    public function deleteAccount(AccountingAccount $account): bool
    {
        if (! $account->canDelete()) {
            throw new \Exception('No se puede eliminar cuenta con movimientos asociados');
        }

        return $account->delete();
    }

    /**
     * Calcula el balance de una cuenta
     */
    public function calculateAccountBalance(AccountingAccount $account): float
    {
        $account->updateBalance();

        return (float) $account->balance;
    }

    /**
     * Recalcula todos los balances de las cuentas
     */
    public function updateAllBalances(int $clientId): void
    {
        $accounts = $this->getChartOfAccounts($clientId);

        foreach ($accounts as $account) {
            $account->updateBalance();
        }
    }

    /**
     * Obtiene o crea el período actual
     */
    public function getCurrentPeriod(int $clientId): AccountingPeriod
    {
        $now = now();

        return AccountingPeriod::firstOrCreate(
            [
                'client_id' => $clientId,
                'fiscal_year' => $now->year,
                'period_number' => $now->month,
            ],
            [
                'name' => $now->locale('es')->translatedFormat('F Y'),
                'start_date' => $now->startOfMonth(),
                'end_date' => $now->endOfMonth(),
                'status' => 'open',
            ]
        );
    }

    /**
     * Obtiene los períodos de un año
     */
    public function getYearPeriods(int $clientId, int $year): Collection
    {
        return AccountingPeriod::where('client_id', $clientId)
            ->where('fiscal_year', $year)
            ->orderBy('period_number')
            ->get();
    }
}
