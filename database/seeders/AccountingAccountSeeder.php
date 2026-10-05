<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Models\AccountingAccount;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountingAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user
        $adminUser = User::where('email', 'rgasperi@smartcarebilling.com')->first()
            ?? User::where('email', 'atenorio@smartcarebilling.com')->first()
            ?? User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->first()
            ?? User::first();

        if (! $adminUser) {
            $this->command->warn('No admin user found. Skipping AccountingAccountSeeder.');

            return;
        }

        $chartOfAccounts = $this->getChartOfAccounts();

        foreach (Client::all() as $client) {
            $createdCount = 0;

            foreach ($chartOfAccounts as $accountData) {
                // Check if account already exists
                if (AccountingAccount::where('client_id', $client->id)
                    ->where('code', $accountData['code'])
                    ->exists()) {
                    continue;
                }

                // If this account has a parent code, we need to find the parent ID
                $parentId = null;
                if (! empty($accountData['parent_code'])) {
                    $parent = AccountingAccount::where('client_id', $client->id)
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
                    'client_id' => $client->id,
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

            if ($createdCount > 0) {
                $this->command->info("Created {$createdCount} accounting accounts for client: {$client->name}");
            }
        }

        $this->command->info('AccountingAccountSeeder completed successfully.');
    }

    /**
     * Get the chart of accounts structure
     */
    private function getChartOfAccounts(): array
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
                'name' => 'Activo Corriente',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => false,
                'parent_code' => '1',
            ],
            [
                'code' => '1101',
                'name' => 'Caja y Bancos',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '11',
            ],
            [
                'code' => '1102',
                'name' => 'Cuentas por Cobrar Pacientes',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '11',
            ],
            [
                'code' => '1103',
                'name' => 'Cuentas por Cobrar Aseguradoras',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '11',
            ],
            [
                'code' => '1104',
                'name' => 'Inventario',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '11',
            ],
            // Activo Fijo (12)
            [
                'code' => '12',
                'name' => 'Activo Fijo',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => false,
                'parent_code' => '1',
            ],
            [
                'code' => '1201',
                'name' => 'Equipos Médicos',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '12',
            ],
            [
                'code' => '1202',
                'name' => 'Mobiliario y Equipo',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '12',
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
                'name' => 'Pasivo Corriente',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => false,
                'parent_code' => '2',
            ],
            [
                'code' => '2101',
                'name' => 'Cuentas por Pagar Proveedores',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
                'parent_code' => '21',
            ],
            [
                'code' => '2102',
                'name' => 'ITBMS por Pagar',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
                'parent_code' => '21',
            ],
            // Pasivo a Largo Plazo (22)
            [
                'code' => '22',
                'name' => 'Pasivo a Largo Plazo',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => false,
                'parent_code' => '2',
            ],

            // PATRIMONIO (3)
            [
                'code' => '3',
                'name' => 'PATRIMONIO',
                'account_type' => AccountType::EQUITY,
                'allows_transaction' => false,
            ],
            [
                'code' => '3101',
                'name' => 'Capital',
                'account_type' => AccountType::EQUITY,
                'allows_transaction' => true,
                'parent_code' => '3',
            ],
            [
                'code' => '3102',
                'name' => 'Utilidades Retenidas',
                'account_type' => AccountType::EQUITY,
                'allows_transaction' => true,
                'parent_code' => '3',
            ],

            // INGRESOS (4)
            [
                'code' => '4',
                'name' => 'INGRESOS',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => false,
            ],
            [
                'code' => '4101',
                'name' => 'Ingresos por Consultas',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => true,
                'parent_code' => '4',
            ],
            [
                'code' => '4102',
                'name' => 'Ingresos por Procedimientos',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => true,
                'parent_code' => '4',
            ],
            [
                'code' => '4103',
                'name' => 'Ingresos por Medicamentos',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => true,
                'parent_code' => '4',
            ],

            // COSTOS (5)
            [
                'code' => '5',
                'name' => 'COSTOS',
                'account_type' => AccountType::COST,
                'allows_transaction' => false,
            ],
            [
                'code' => '5101',
                'name' => 'Costo de Medicamentos',
                'account_type' => AccountType::COST,
                'allows_transaction' => true,
                'parent_code' => '5',
            ],
            [
                'code' => '5102',
                'name' => 'Costo de Materiales',
                'account_type' => AccountType::COST,
                'allows_transaction' => true,
                'parent_code' => '5',
            ],

            // GASTOS (6)
            [
                'code' => '6',
                'name' => 'GASTOS',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => false,
            ],
            [
                'code' => '6101',
                'name' => 'Gastos Administrativos',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '6',
            ],
            [
                'code' => '6102',
                'name' => 'Gastos de Personal',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '6',
            ],
            [
                'code' => '6103',
                'name' => 'Servicios Públicos',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '6',
            ],
        ];
    }
}
