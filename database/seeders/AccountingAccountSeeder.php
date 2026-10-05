<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Models\AccountingAccount;
use App\Models\Client;
use Illuminate\Database\Seeder;

class AccountingAccountSeeder extends Seeder
{
    public function run(): void
    {
        $client = Client::first();
        if (! $client) {
            return;
        }

        $accounts = [
            // ACTIVOS
            [
                'code' => '1',
                'name' => 'Activos',
                'account_type' => AccountType::ASSET,
                'parent_id' => null,
                'allows_transaction' => false,
            ],
            [
                'code' => '11',
                'name' => 'Activo Corriente',
                'account_type' => AccountType::ASSET,
                'parent_id' => 1,
                'allows_transaction' => false,
            ],
            [
                'code' => '1101',
                'name' => 'Caja y Bancos',
                'account_type' => AccountType::ASSET,
                'parent_id' => 2,
                'allows_transaction' => true,
            ],
            [
                'code' => '1102',
                'name' => 'Cuentas por Cobrar Pacientes',
                'account_type' => AccountType::ASSET,
                'parent_id' => 2,
                'allows_transaction' => true,
            ],
            [
                'code' => '1103',
                'name' => 'Cuentas por Cobrar Aseguradoras',
                'account_type' => AccountType::ASSET,
                'parent_id' => 2,
                'allows_transaction' => true,
            ],
            [
                'code' => '1104',
                'name' => 'Inventario',
                'account_type' => AccountType::ASSET,
                'parent_id' => 2,
                'allows_transaction' => true,
            ],
            [
                'code' => '12',
                'name' => 'Activo Fijo',
                'account_type' => AccountType::ASSET,
                'parent_id' => 1,
                'allows_transaction' => false,
            ],
            [
                'code' => '1201',
                'name' => 'Equipos Médicos',
                'account_type' => AccountType::ASSET,
                'parent_id' => 7,
                'allows_transaction' => true,
            ],
            [
                'code' => '1202',
                'name' => 'Mobiliario y Equipo',
                'account_type' => AccountType::ASSET,
                'parent_id' => 7,
                'allows_transaction' => true,
            ],

            // PASIVOS
            [
                'code' => '2',
                'name' => 'Pasivos',
                'account_type' => AccountType::LIABILITY,
                'parent_id' => null,
                'allows_transaction' => false,
            ],
            [
                'code' => '21',
                'name' => 'Pasivo Corriente',
                'account_type' => AccountType::LIABILITY,
                'parent_id' => 10,
                'allows_transaction' => false,
            ],
            [
                'code' => '2101',
                'name' => 'Cuentas por Pagar Proveedores',
                'account_type' => AccountType::LIABILITY,
                'parent_id' => 11,
                'allows_transaction' => true,
            ],
            [
                'code' => '2102',
                'name' => 'ITBMS por Pagar',
                'account_type' => AccountType::LIABILITY,
                'parent_id' => 11,
                'allows_transaction' => true,
            ],
            [
                'code' => '22',
                'name' => 'Pasivo a Largo Plazo',
                'account_type' => AccountType::LIABILITY,
                'parent_id' => 10,
                'allows_transaction' => false,
            ],

            // PATRIMONIO
            [
                'code' => '3',
                'name' => 'Patrimonio',
                'account_type' => AccountType::EQUITY,
                'parent_id' => null,
                'allows_transaction' => false,
            ],
            [
                'code' => '3101',
                'name' => 'Capital',
                'account_type' => AccountType::EQUITY,
                'parent_id' => 15,
                'allows_transaction' => true,
            ],
            [
                'code' => '3102',
                'name' => 'Utilidades Retenidas',
                'account_type' => AccountType::EQUITY,
                'parent_id' => 15,
                'allows_transaction' => true,
            ],

            // INGRESOS
            [
                'code' => '4',
                'name' => 'Ingresos',
                'account_type' => AccountType::INCOME,
                'parent_id' => null,
                'allows_transaction' => false,
            ],
            [
                'code' => '4101',
                'name' => 'Ingresos por Consultas',
                'account_type' => AccountType::INCOME,
                'parent_id' => 18,
                'allows_transaction' => true,
            ],
            [
                'code' => '4102',
                'name' => 'Ingresos por Procedimientos',
                'account_type' => AccountType::INCOME,
                'parent_id' => 18,
                'allows_transaction' => true,
            ],
            [
                'code' => '4103',
                'name' => 'Ingresos por Medicamentos',
                'account_type' => AccountType::INCOME,
                'parent_id' => 18,
                'allows_transaction' => true,
            ],

            // COSTOS
            [
                'code' => '5',
                'name' => 'Costos',
                'account_type' => AccountType::COST,
                'parent_id' => null,
                'allows_transaction' => false,
            ],
            [
                'code' => '5101',
                'name' => 'Costo de Medicamentos',
                'account_type' => AccountType::COST,
                'parent_id' => 23,
                'allows_transaction' => true,
            ],
            [
                'code' => '5102',
                'name' => 'Costo de Materiales',
                'account_type' => AccountType::COST,
                'parent_id' => 23,
                'allows_transaction' => true,
            ],

            // GASTOS
            [
                'code' => '6',
                'name' => 'Gastos',
                'account_type' => AccountType::EXPENSE,
                'parent_id' => null,
                'allows_transaction' => false,
            ],
            [
                'code' => '6101',
                'name' => 'Gastos Administrativos',
                'account_type' => AccountType::EXPENSE,
                'parent_id' => 26,
                'allows_transaction' => true,
            ],
            [
                'code' => '6102',
                'name' => 'Gastos de Personal',
                'account_type' => AccountType::EXPENSE,
                'parent_id' => 26,
                'allows_transaction' => true,
            ],
            [
                'code' => '6103',
                'name' => 'Servicios Públicos',
                'account_type' => AccountType::EXPENSE,
                'parent_id' => 26,
                'allows_transaction' => true,
            ],
        ];

        foreach ($accounts as $data) {
            AccountingAccount::firstOrCreate(
                ['client_id' => $client->id, 'code' => $data['code']],
                [
                    'name' => $data['name'],
                    'account_type' => $data['account_type'],
                    'parent_id' => $data['parent_id'],
                    'allows_transaction' => $data['allows_transaction'],
                    'status' => 'active',
                    'created_by' => 1,
                ]
            );
        }

        // Recalcular niveles
        $allAccounts = AccountingAccount::where('client_id', $client->id)->get();
        foreach ($allAccounts as $account) {
            $account->calculateLevel();
            $account->save();
        }
    }
}
