<?php

namespace Database\Seeders;

use App\Models\Accounting\AccountingAccount;
use App\Models\Accounting\AccountingEvent;
use App\Models\Accounting\AccountingEventConfig;
use App\Models\Client;
use Illuminate\Database\Seeder;

class AccountingEventConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventConfigs = [
            // FACTURACIÓN
            'EVT-001' => ['debit_account_code' => '1101', 'credit_account_code' => '4101'], // Factura Contado: Caja -> Ingresos
            'EVT-002' => ['debit_account_code' => '1401', 'credit_account_code' => '4101'], // Factura Crédito: CxC -> Ingresos
            'EVT-003' => ['debit_account_code' => '1101', 'credit_account_code' => '1401'], // Pago Factura: Caja -> CxC
            'EVT-004' => ['debit_account_code' => '5101', 'credit_account_code' => '1101'], // Devolución: Devol -> Caja

            // COMPRAS
            'EVT-005' => ['debit_account_code' => '1501', 'credit_account_code' => '2101'], // Compra: Inventario -> CxP
            'EVT-006' => ['debit_account_code' => '2101', 'credit_account_code' => '1101'], // Pago Proveedor: CxP -> Caja
            'EVT-007' => ['debit_account_code' => '2101', 'credit_account_code' => '1501'], // Devolución Compra: CxP -> Inventario
            'EVT-008' => ['debit_account_code' => '1501', 'credit_account_code' => '2401'], // Recepción: Inventario -> Trans

            // TESORERÍA
            'EVT-009' => ['debit_account_code' => '1101', 'credit_account_code' => '4101'], // Cobro Manual: Caja -> Ingresos
            'EVT-010' => ['debit_account_code' => '6101', 'credit_account_code' => '1101'], // Pago Manual: Gastos -> Caja
            'EVT-011' => ['debit_account_code' => '1102', 'credit_account_code' => '1101'], // Transferencia: Banco -> Caja
            'EVT-012' => ['debit_account_code' => '6201', 'credit_account_code' => '1101'], // Comisiones: Gastos Fin -> Caja

            // NÓMINA
            'EVT-013' => ['debit_account_code' => '6101', 'credit_account_code' => '2201'], // Planilla: Gastos -> Salarios x Pagar
            'EVT-014' => ['debit_account_code' => '2201', 'credit_account_code' => '2301'], // Retenciones: Salarios -> Retenciones
            'EVT-015' => ['debit_account_code' => '2201', 'credit_account_code' => '1101'], // Pago Planilla: Salarios -> Caja
            'EVT-016' => ['debit_account_code' => '6301', 'credit_account_code' => '2201'], // Bonificación: Bono -> Salarios

            // OTROS
            'EVT-017' => ['debit_account_code' => '5101', 'credit_account_code' => '1501'], // Movimiento Inv: Devol -> Inventario
            'EVT-018' => ['debit_account_code' => '1101', 'credit_account_code' => '1101'], // Ajuste Manual (flexible)
            'EVT-019' => ['debit_account_code' => '2501', 'credit_account_code' => '1401'], // Incobrables: Provisión -> CxC

            // RECTIFICACIONES
            'EVT-R001' => ['debit_account_code' => '1101', 'credit_account_code' => '1101'], // Reversión (flexible)
            'EVT-R002' => ['debit_account_code' => '1101', 'credit_account_code' => '1101'], // Corrección (flexible)
            'EVT-R003' => ['debit_account_code' => '1101', 'credit_account_code' => '1101'], // Ajuste Período (flexible)
            'EVT-R004' => ['debit_account_code' => '6401', 'credit_account_code' => '1601'], // Depreciación: Gasto -> Acum Deprec
            'EVT-R005' => ['debit_account_code' => '6501', 'credit_account_code' => '1701'], // Amortización: Gasto -> Acum Amort
        ];

        $clients = Client::all();
        $successCount = 0;

        foreach ($clients as $client) {
            foreach ($eventConfigs as $eventCode => $accountMapping) {
                // Get event
                $event = AccountingEvent::where('code', $eventCode)->first();
                if (! $event) {
                    continue;
                }

                // Get accounts
                $debitAccount = AccountingAccount::where([
                    ['client_id', $client->id],
                    ['code', $accountMapping['debit_account_code']],
                ])->first();

                $creditAccount = AccountingAccount::where([
                    ['client_id', $client->id],
                    ['code', $accountMapping['credit_account_code']],
                ])->first();

                // Skip if accounts don't exist
                if (! $debitAccount || ! $creditAccount) {
                    continue;
                }

                // Create or update config
                $config = AccountingEventConfig::updateOrCreate(
                    [
                        'client_id' => $client->id,
                        'accounting_event_id' => $event->id,
                    ],
                    [
                        'debit_account_id' => $debitAccount->id,
                        'credit_account_id' => $creditAccount->id,
                        'auto_generate' => true,
                        'status' => 'active',
                        'created_by' => 1, // Assume system user or first user
                        'updated_by' => 1,
                    ]
                );

                if ($config->wasRecentlyCreated) {
                    $successCount++;
                }
            }
        }

        $totalConfigs = $clients->count() * count($eventConfigs);
        $this->command->info("Created/Updated {$successCount} accounting event configurations out of {$totalConfigs}.");
        $this->command->info('AccountingEventConfigSeeder completed successfully.');
    }
}
