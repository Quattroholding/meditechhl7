<?php

namespace Database\Seeders;

use App\Models\Accounting\AccountingAccount;
use App\Models\Accounting\AccountingRule;
use App\Models\Accounting\ClientAccountingRuleMapping;
use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientAccountingRuleMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default account codes mapped to rule types
        $defaultMappings = [
            'income' => '4101', // Ingresos por Servicios
            'receivable' => '1102', // Cuentas por Cobrar Pacientes
            'bank' => '1101', // Efectivo y Bancos
            'expense' => '6101', // Gastos Administrativos
        ];

        // Get all clients and rules
        $clients = Client::all();
        $incomeRule = AccountingRule::where('code', 'RULE-INCOME-001')->first();
        $equityRule = AccountingRule::where('code', 'RULE-EQUITY-001')->first();
        $assetRule = AccountingRule::where('code', 'RULE-ASSET-001')->first();
        $expenseRule = AccountingRule::where('code', 'RULE-EXPENSE-001')->first();

        $createdCount = 0;

        foreach ($clients as $client) {
            foreach ($defaultMappings as $ruleType => $accountCode) {
                // Get the account for this client
                $account = AccountingAccount::where([
                    ['client_id', $client->id],
                    ['code', $accountCode],
                ])->first();

                if (! $account) {
                    continue;
                }

                // Determine which rule to use based on type
                $rule = match ($ruleType) {
                    'income' => $incomeRule,
                    'receivable' => $assetRule, // Receivable is an asset
                    'bank' => $assetRule, // Bank is an asset
                    'expense' => $expenseRule,
                    default => $assetRule,
                };

                if (! $rule) {
                    continue;
                }

                // Create or update mapping
                $mapping = ClientAccountingRuleMapping::updateOrCreate(
                    [
                        'client_id' => $client->id,
                        'rule_type' => $ruleType,
                    ],
                    [
                        'accounting_rule_id' => $rule->id,
                        'accounting_account_id' => $account->id,
                        'status' => 'active',
                        'created_by' => 1,
                        'updated_by' => 1,
                    ]
                );

                if ($mapping->wasRecentlyCreated) {
                    $createdCount++;
                }
            }
        }

        $this->command->info("Created {$createdCount} client accounting rule mappings.");
        $this->command->info('ClientAccountingRuleMappingSeeder completed successfully.');
    }
}
