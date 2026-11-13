<?php

namespace Database\Seeders;

use App\Models\Accounting\AccountingAccount;
use App\Models\Accounting\AccountingRule;
use App\Models\Accounting\ClientAccountingRuleMapping;
use App\Models\Client;
use App\Models\MedicalSpeciality;
use Illuminate\Database\Seeder;

class ClientAccountingRuleMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default account codes mapped to rule types (for backwards compatibility)
        $defaultMappings = [
            'income' => '4101', // Ingresos por Servicios (Medicina General)
            'receivable' => '1102', // Cuentas por Cobrar Pacientes
            'bank' => '1101', // Efectivo y Bancos
            'expense' => '6101', // Gastos Administrativos
        ];

        // Specific accounts for granular rules
        $granularAccounts = [
            'income_general_medicine' => '4101', // Medicina General
            'income_other_specialties' => '4102', // Otras Especialidades
            'receivable_patient' => '110201', // CxC Pacientes
            'receivable_insurance' => '110202', // CxC Seguros
        ];

        // Get all clients and rules
        $clients = Client::all();
        $incomeRule = AccountingRule::where('code', 'RULE-INCOME-001')->first();
        $assetRule = AccountingRule::where('code', 'RULE-ASSET-001')->first();
        $expenseRule = AccountingRule::where('code', 'RULE-EXPENSE-001')->first();

        // Get specialties if they exist
        $generalMedicine = MedicalSpeciality::where('name', 'LIKE', '%Medicina General%')->first();

        $createdCount = 0;

        foreach ($clients as $client) {
            // ========== REGLAS DE INGRESOS POR ESPECIALIDAD ==========
            $this->createIncomeRules($client, $incomeRule, $granularAccounts, $generalMedicine, $createdCount);

            // ========== REGLAS DE CxC POR TIPO DE PAGADOR ==========
            $this->createReceivableRules($client, $assetRule, $granularAccounts, $createdCount);

            // ========== REGLAS DE BANCO ==========
            $this->createBankRule($client, $assetRule, $defaultMappings, $createdCount);

            // ========== REGLAS DE GASTOS ==========
            $this->createExpenseRule($client, $expenseRule, $defaultMappings, $createdCount);
        }

        $this->command->info("Created {$createdCount} client accounting rule mappings.");
        $this->command->info('ClientAccountingRuleMappingSeeder completed successfully.');
    }

    /**
     * Crea reglas de ingresos por especialidad médica
     */
    private function createIncomeRules(Client $client, $incomeRule, array $granularAccounts, ?MedicalSpeciality $generalMedicine, &$createdCount): void
    {
        if (! $incomeRule) {
            return;
        }

        // Si existe Medicina General, crear regla específica
        if ($generalMedicine) {
            $account4101 = AccountingAccount::where([
                ['client_id', $client->id],
                ['code', $granularAccounts['income_general_medicine']],
            ])->first();

            if ($account4101) {
                $mapping = ClientAccountingRuleMapping::updateOrCreate(
                    [
                        'client_id' => $client->id,
                        'rule_type' => 'income',
                        'medical_speciality_id' => $generalMedicine->id,
                        'payment_source' => null,
                    ],
                    [
                        'accounting_rule_id' => $incomeRule->id,
                        'accounting_account_id' => $account4101->id,
                        'status' => 'active',
                        'is_default' => false,
                        'priority' => 10,
                        'created_by' => 1,
                        'updated_by' => 1,
                    ]
                );

                if ($mapping->wasRecentlyCreated) {
                    $createdCount++;
                }
            }
        }

        // Regla default para otras especialidades
        $account4102 = AccountingAccount::where([
            ['client_id', $client->id],
            ['code', $granularAccounts['income_other_specialties']],
        ])->first();

        if ($account4102) {
            $mapping = ClientAccountingRuleMapping::updateOrCreate(
                [
                    'client_id' => $client->id,
                    'rule_type' => 'income',
                    'medical_speciality_id' => null,
                    'payment_source' => null,
                ],
                [
                    'accounting_rule_id' => $incomeRule->id,
                    'accounting_account_id' => $account4102->id,
                    'status' => 'active',
                    'is_default' => true,
                    'priority' => 0,
                    'created_by' => 1,
                    'updated_by' => 1,
                ]
            );

            if ($mapping->wasRecentlyCreated) {
                $createdCount++;
            }
        }
    }

    /**
     * Crea reglas de CxC por tipo de pagador (paciente vs. seguro)
     */
    private function createReceivableRules(Client $client, $assetRule, array $granularAccounts, &$createdCount): void
    {
        if (! $assetRule) {
            return;
        }

        // CxC Paciente
        $account110201 = AccountingAccount::where([
            ['client_id', $client->id],
            ['code', $granularAccounts['receivable_patient']],
        ])->first();

        if ($account110201) {
            $mapping = ClientAccountingRuleMapping::updateOrCreate(
                [
                    'client_id' => $client->id,
                    'rule_type' => 'receivable',
                    'medical_speciality_id' => null,
                    'payment_source' => 'patient',
                ],
                [
                    'accounting_rule_id' => $assetRule->id,
                    'accounting_account_id' => $account110201->id,
                    'status' => 'active',
                    'is_default' => false,
                    'priority' => 10,
                    'created_by' => 1,
                    'updated_by' => 1,
                ]
            );

            if ($mapping->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        // CxC Seguro
        $account110202 = AccountingAccount::where([
            ['client_id', $client->id],
            ['code', $granularAccounts['receivable_insurance']],
        ])->first();

        if ($account110202) {
            $mapping = ClientAccountingRuleMapping::updateOrCreate(
                [
                    'client_id' => $client->id,
                    'rule_type' => 'receivable',
                    'medical_speciality_id' => null,
                    'payment_source' => 'insurance',
                ],
                [
                    'accounting_rule_id' => $assetRule->id,
                    'accounting_account_id' => $account110202->id,
                    'status' => 'active',
                    'is_default' => false,
                    'priority' => 10,
                    'created_by' => 1,
                    'updated_by' => 1,
                ]
            );

            if ($mapping->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        // Regla default CxC (si no se especifica payment_source)
        if ($account110201) {
            $mapping = ClientAccountingRuleMapping::updateOrCreate(
                [
                    'client_id' => $client->id,
                    'rule_type' => 'receivable',
                    'medical_speciality_id' => null,
                    'payment_source' => null,
                ],
                [
                    'accounting_rule_id' => $assetRule->id,
                    'accounting_account_id' => $account110201->id,
                    'status' => 'active',
                    'is_default' => true,
                    'priority' => 0,
                    'created_by' => 1,
                    'updated_by' => 1,
                ]
            );

            if ($mapping->wasRecentlyCreated) {
                $createdCount++;
            }
        }
    }

    /**
     * Crea regla de banco
     */
    private function createBankRule(Client $client, $assetRule, array $defaultMappings, &$createdCount): void
    {
        if (! $assetRule) {
            return;
        }

        $account = AccountingAccount::where([
            ['client_id', $client->id],
            ['code', $defaultMappings['bank']],
        ])->first();

        if (! $account) {
            return;
        }

        $mapping = ClientAccountingRuleMapping::updateOrCreate(
            [
                'client_id' => $client->id,
                'rule_type' => 'bank',
                'medical_speciality_id' => null,
                'payment_source' => null,
            ],
            [
                'accounting_rule_id' => $assetRule->id,
                'accounting_account_id' => $account->id,
                'status' => 'active',
                'is_default' => true,
                'priority' => 0,
                'created_by' => 1,
                'updated_by' => 1,
            ]
        );

        if ($mapping->wasRecentlyCreated) {
            $createdCount++;
        }
    }

    /**
     * Crea regla de gastos
     */
    private function createExpenseRule(Client $client, $expenseRule, array $defaultMappings, &$createdCount): void
    {
        if (! $expenseRule) {
            return;
        }

        $account = AccountingAccount::where([
            ['client_id', $client->id],
            ['code', $defaultMappings['expense']],
        ])->first();

        if (! $account) {
            return;
        }

        $mapping = ClientAccountingRuleMapping::updateOrCreate(
            [
                'client_id' => $client->id,
                'rule_type' => 'expense',
                'medical_speciality_id' => null,
                'payment_source' => null,
            ],
            [
                'accounting_rule_id' => $expenseRule->id,
                'accounting_account_id' => $account->id,
                'status' => 'active',
                'is_default' => true,
                'priority' => 0,
                'created_by' => 1,
                'updated_by' => 1,
            ]
        );

        if ($mapping->wasRecentlyCreated) {
            $createdCount++;
        }
    }
}
