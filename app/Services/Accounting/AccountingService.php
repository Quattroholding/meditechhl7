<?php

namespace App\Services\Accounting;

use App\Enums\AccountType;
use App\Models\Accounting\AccountingAccount;
use App\Models\Accounting\AccountingPeriod;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
        $account->recalculateBalance();

        return (float) $account->balance;
    }

    /**
     * Recalcula todos los balances de las cuentas
     */
    public function updateAllBalances(int $clientId): void
    {
        $accounts = $this->getChartOfAccounts($clientId);

        foreach ($accounts as $account) {
            $account->recalculateBalance();
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

    /**
     * Carga las cuentas base para un cliente cuando se habilita el módulo de contabilidad
     */
    public function loadBaseAccountsForClient(int $clientId): int
    {
        $adminUser = User::where('email', 'rgasperi@smartcarebilling.com')->first()
            ?? User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->first()
            ?? User::first();

        if (! $adminUser) {
            throw new \Exception('No admin user found to create accounting accounts');
        }

        $chartOfAccounts = $this->getBaseChartOfAccounts();
        $createdCount = 0;

        foreach ($chartOfAccounts as $accountData) {
            // Check if account already exists
            if (AccountingAccount::where('client_id', $clientId)
                ->where('code', $accountData['code'])
                ->exists()) {
                continue;
            }

            // If this account has a parent code, we need to find the parent ID
            $parentId = null;
            if (! empty($accountData['parent_code'])) {
                $parent = AccountingAccount::where('client_id', $clientId)
                    ->where('code', $accountData['parent_code'])
                    ->first();
                if ($parent) {
                    $parentId = $parent->id;
                }
            }

            $level = ! empty($accountData['parent_code']) ? 2 : 1;
            if ($parentId) {
                $parent = AccountingAccount::find($parentId);
                $level = ($parent->level ?? 1) + 1;
            }

            AccountingAccount::create([
                'uuid' => Str::uuid(),
                'client_id' => $clientId,
                'code' => $accountData['code'],
                'name' => $accountData['name'],
                'description' => $accountData['description'] ?? '',
                'account_type' => $accountData['account_type'],
                'parent_id' => $parentId,
                'level' => $level,
                'allows_transaction' => $accountData['allows_transaction'] ?? true,
                'status' => 'active',
                'created_by' => $adminUser->id,
            ]);

            $createdCount++;
        }

        return $createdCount;
    }

    /**
     * Obtiene la estructura base del plan de cuentas
     */
    private function getBaseChartOfAccounts(): array
    {
        return [
            // ACTIVO (1)
            [
                'code' => '1',
                'name' => 'ACTIVO',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => false,
            ],
            // Activo Corriente (11)
            [
                'code' => '11',
                'parent_code' => '1',
                'name' => 'ACTIVO CORRIENTE',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => false,
            ],
            // Caja (1101)
            [
                'code' => '1101',
                'parent_code' => '11',
                'name' => 'CAJA',
                'description' => 'Efectivo en caja',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
            ],
            // Banco (1102)
            [
                'code' => '1102',
                'parent_code' => '11',
                'name' => 'BANCO',
                'description' => 'Efectivo en banco',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
            ],
            // Cuentas por Cobrar (1103)
            [
                'code' => '1103',
                'parent_code' => '11',
                'name' => 'CUENTAS POR COBRAR',
                'description' => 'Cuentas por cobrar a clientes',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
            ],
            // Inventario (1104)
            [
                'code' => '1104',
                'parent_code' => '11',
                'name' => 'INVENTARIO',
                'description' => 'Inventario de productos',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
            ],

            // PASIVO (2)
            [
                'code' => '2',
                'name' => 'PASIVO',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => false,
            ],
            // Pasivo Corriente (21)
            [
                'code' => '21',
                'parent_code' => '2',
                'name' => 'PASIVO CORRIENTE',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => false,
            ],
            // Cuentas por Pagar (2101)
            [
                'code' => '2101',
                'parent_code' => '21',
                'name' => 'CUENTAS POR PAGAR',
                'description' => 'Cuentas por pagar a proveedores',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
            ],
            // Impuestos por Pagar (2102)
            [
                'code' => '2102',
                'parent_code' => '21',
                'name' => 'IMPUESTOS POR PAGAR',
                'description' => 'Impuestos pendientes de pago',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
            ],

            // PATRIMONIO (3)
            [
                'code' => '3',
                'name' => 'PATRIMONIO',
                'account_type' => AccountType::EQUITY,
                'allows_transaction' => false,
            ],
            // Capital Social (3101)
            [
                'code' => '3101',
                'parent_code' => '3',
                'name' => 'CAPITAL SOCIAL',
                'description' => 'Aporte de capital de los socios',
                'account_type' => AccountType::EQUITY,
                'allows_transaction' => true,
            ],
            // Ganancias Retenidas (3102)
            [
                'code' => '3102',
                'parent_code' => '3',
                'name' => 'GANANCIAS RETENIDAS',
                'description' => 'Ganancias retenidas de ejercicios anteriores',
                'account_type' => AccountType::EQUITY,
                'allows_transaction' => true,
            ],

            // INGRESOS (4)
            [
                'code' => '4',
                'name' => 'INGRESOS',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => false,
            ],
            // Ingresos Operacionales (41)
            [
                'code' => '41',
                'parent_code' => '4',
                'name' => 'INGRESOS OPERACIONALES',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => false,
            ],
            // Ingresos por Servicios (4101)
            [
                'code' => '4101',
                'parent_code' => '41',
                'name' => 'INGRESOS POR SERVICIOS',
                'description' => 'Ingresos por servicios prestados',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => true,
            ],
            // Ingresos por Consultas (4102)
            [
                'code' => '4102',
                'parent_code' => '41',
                'name' => 'INGRESOS POR CONSULTAS',
                'description' => 'Ingresos por consultas médicas',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => true,
            ],

            // GASTOS (5)
            [
                'code' => '5',
                'name' => 'GASTOS',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => false,
            ],
            // Gastos de Operación (51)
            [
                'code' => '51',
                'parent_code' => '5',
                'name' => 'GASTOS DE OPERACIÓN',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => false,
            ],
            // Salarios y Beneficios (5101)
            [
                'code' => '5101',
                'parent_code' => '51',
                'name' => 'SALARIOS Y BENEFICIOS',
                'description' => 'Salarios, bonificaciones y beneficios a empleados',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
            ],
            // Suministros (5102)
            [
                'code' => '5102',
                'parent_code' => '51',
                'name' => 'SUMINISTROS',
                'description' => 'Gastos en suministros y materiales',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
            ],
            // Servicios (5103)
            [
                'code' => '5103',
                'parent_code' => '51',
                'name' => 'SERVICIOS',
                'description' => 'Gastos en servicios varios',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
            ],
        ];
    }
}
