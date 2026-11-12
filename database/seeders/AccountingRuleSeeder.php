<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Models\Accounting\AccountingRule;
use Illuminate\Database\Seeder;

class AccountingRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            // ASSETS (Activos)
            [
                'code' => 'RULE-ASSET-001',
                'name' => 'Activos - Regla Estándar',
                'description' => 'Regla de naturaleza y comportamiento para cuentas de Activos. Los activos aumentan con débitos y disminuyen con créditos.',
                'account_type' => AccountType::ASSET,
                'nature' => 'debit',
                'requires_cost_center' => false,
                'is_system_account' => false,
                'is_control_account' => false,
                'closes_period' => false,
                'status' => 'active',
            ],

            // LIABILITIES (Pasivos)
            [
                'code' => 'RULE-LIABILITY-001',
                'name' => 'Pasivos - Regla Estándar',
                'description' => 'Regla de naturaleza y comportamiento para cuentas de Pasivos. Los pasivos aumentan con créditos y disminuyen con débitos.',
                'account_type' => AccountType::LIABILITY,
                'nature' => 'credit',
                'requires_cost_center' => false,
                'is_system_account' => false,
                'is_control_account' => false,
                'closes_period' => false,
                'status' => 'active',
            ],

            // EQUITY (Patrimonio)
            [
                'code' => 'RULE-EQUITY-001',
                'name' => 'Patrimonio - Regla Estándar',
                'description' => 'Regla de naturaleza y comportamiento para cuentas de Patrimonio. El patrimonio aumenta con créditos y disminuye con débitos.',
                'account_type' => AccountType::EQUITY,
                'nature' => 'credit',
                'requires_cost_center' => false,
                'is_system_account' => false,
                'is_control_account' => false,
                'closes_period' => false,
                'status' => 'active',
            ],

            // INCOME (Ingresos)
            [
                'code' => 'RULE-INCOME-001',
                'name' => 'Ingresos - Regla Estándar',
                'description' => 'Regla de naturaleza y comportamiento para cuentas de Ingresos. Los ingresos aumentan con créditos, requieren centro de costos y cierran período.',
                'account_type' => AccountType::INCOME,
                'nature' => 'credit',
                'requires_cost_center' => true,
                'is_system_account' => false,
                'is_control_account' => false,
                'closes_period' => true,
                'status' => 'active',
            ],

            // COSTS (Costos)
            [
                'code' => 'RULE-COST-001',
                'name' => 'Costos - Regla Estándar',
                'description' => 'Regla de naturaleza y comportamiento para cuentas de Costos. Los costos aumentan con débitos, requieren centro de costos y cierran período.',
                'account_type' => AccountType::COST,
                'nature' => 'debit',
                'requires_cost_center' => true,
                'is_system_account' => false,
                'is_control_account' => false,
                'closes_period' => true,
                'status' => 'active',
            ],

            // EXPENSES (Gastos)
            [
                'code' => 'RULE-EXPENSE-001',
                'name' => 'Gastos - Regla Estándar',
                'description' => 'Regla de naturaleza y comportamiento para cuentas de Gastos. Los gastos aumentan con débitos, requieren centro de costos y cierran período.',
                'account_type' => AccountType::EXPENSE,
                'nature' => 'debit',
                'requires_cost_center' => true,
                'is_system_account' => false,
                'is_control_account' => false,
                'closes_period' => true,
                'status' => 'active',
            ],
        ];

        $createdCount = 0;

        foreach ($rules as $data) {
            $rule = AccountingRule::updateOrCreate(
                ['code' => $data['code']],
                $data
            );

            if ($rule->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        if ($createdCount > 0) {
            $this->command->info("Created {$createdCount} accounting rules.");
        } else {
            $this->command->info('All accounting rules already exist.');
        }

        $this->command->info('AccountingRuleSeeder completed successfully.');
    }
}
