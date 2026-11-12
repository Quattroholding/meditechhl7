<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Models\Accounting\AccountingAccount;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AccountingAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user
        $adminUser = User::where('email', 'rgasperi@smartcarebilling.com')->first()
            ?? User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->first()
            ?? User::first();

        if (! $adminUser) {
            $this->command->warn('No admin user found. Skipping AccountingAccountSeeder.');

            return;
        }

        $chartOfAccounts = $this->getChartOfAccounts();

        foreach (Client::all() as $client) {
            $count = 0;

            foreach ($chartOfAccounts as $accountData) {
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

                $attributes = [
                    'client_id' => $client->id,
                    'code' => $accountData['code'],
                ];

                $values = [
                    'name' => $accountData['name'],
                    'description' => $accountData['description'] ?? '',
                    'account_type' => $accountData['account_type'],
                    'nature' => $this->getNature($accountData['account_type']),
                    'parent_id' => $parentId,
                    'level' => $level,
                    'allows_transaction' => $accountData['allows_transaction'] ?? true,
                    'requires_cost_center' => $this->requiresCostCenter($accountData['account_type']),
                    'is_system_account' => false,
                    'is_control_account' => false,
                    'closes_period' => $this->closesPeriod($accountData['account_type']),
                    'status' => 'active',
                    'created_by' => $adminUser->id,
                ];

                // Add uuid only for new accounts
                $account = AccountingAccount::firstOrNew($attributes);
                if (! $account->exists) {
                    $values['uuid'] = Str::uuid();
                }

                $account->fill($values)->save();
                $count++;
            }

            if ($count > 0) {
                $this->command->info("Synced {$count} accounting accounts for client: {$client->name}");
            }
        }

        $this->command->info('AccountingAccountSeeder completed successfully.');
    }

    /**
     * Determina la naturaleza de la cuenta basada en su tipo
     */
    private function getNature(AccountType $accountType): string
    {
        return match ($accountType) {
            AccountType::ASSET => 'debit',
            AccountType::LIABILITY => 'credit',
            AccountType::EQUITY => 'credit',
            AccountType::INCOME => 'credit',
            AccountType::COST => 'debit',
            AccountType::EXPENSE => 'debit',
        };
    }

    /**
     * Determina si la cuenta requiere centro de costos
     */
    private function requiresCostCenter(AccountType $accountType): bool
    {
        return in_array($accountType, [AccountType::INCOME, AccountType::COST, AccountType::EXPENSE]);
    }

    /**
     * Determina si la cuenta se cierra al final del período
     */
    private function closesPeriod(AccountType $accountType): bool
    {
        return in_array($accountType, [AccountType::INCOME, AccountType::COST, AccountType::EXPENSE]);
    }

    /**
     * Get the chart of accounts structure from CAT-002 SAMI specification
     * Auto-generated from: Especificación Funcional Módulo Financiero SAMI - 2.xlsx
     * Total accounts: 153
     */
    private function getChartOfAccounts(): array
    {
        return [
            [
                'code' => '1',
                'name' => 'ACTIVOS',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => false,
            ],
            [
                'code' => '11',
                'name' => 'ACTIVOS CORRIENTES',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => false,
                'parent_code' => '1',
            ],
            [
                'code' => '1101',
                'name' => 'EFECTIVO Y BANCOS',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => false,
                'parent_code' => '11',
            ],
            [
                'code' => '110101',
                'name' => 'Caja General',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1101',
            ],
            [
                'code' => '110102',
                'name' => 'Caja Menuda',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1101',
            ],
            [
                'code' => '110103',
                'name' => 'Banco General',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1101',
            ],
            [
                'code' => '110104',
                'name' => 'Banco Bac',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1101',
            ],
            [
                'code' => '110105',
                'name' => 'Banco Nacional',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1101',
            ],
            [
                'code' => '1102',
                'name' => 'CUENTAS POR COBRAR',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => false,
                'parent_code' => '11',
            ],
            [
                'code' => '110201',
                'name' => 'Cuentas por Cobrar Pacientes',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1102',
            ],
            [
                'code' => '110202',
                'name' => 'Cuentas por Cobrar Aseguradoras',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1102',
            ],
            [
                'code' => '110203',
                'name' => 'Cuentas por Cobrar  Empresas y Convenios',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1102',
            ],
            [
                'code' => '110204',
                'name' => 'Cuentas por Cobrar Médicos',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1102',
            ],
            [
                'code' => '110205',
                'name' => 'Cuentas por Cobrar Accionistas',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1102',
            ],
            [
                'code' => '110206',
                'name' => 'Cuentas por Cobrar Empleados',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1102',
            ],
            [
                'code' => '110207',
                'name' => 'Otras Cuentas por Cobrar',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1102',
            ],
            [
                'code' => '110250',
                'name' => 'Provisión para Cuentas Incobrables',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1102',
            ],
            [
                'code' => '1103',
                'name' => 'INVENTARIOS',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => false,
                'parent_code' => '11',
            ],
            [
                'code' => '110301',
                'name' => 'Medicamentos',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1103',
            ],
            [
                'code' => '110302',
                'name' => 'Material Médico',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1103',
            ],
            [
                'code' => '110303',
                'name' => 'Material Quirúrgico',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1103',
            ],
            [
                'code' => '110304',
                'name' => 'Material Odontológico',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1103',
            ],
            [
                'code' => '110305',
                'name' => 'Material de Laboratorio',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1103',
            ],
            [
                'code' => '110306',
                'name' => 'Suministros',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1103',
            ],
            [
                'code' => '110307',
                'name' => 'Inventario en Tránsito',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1103',
            ],
            [
                'code' => '1104',
                'name' => 'GASTOS PAGADOS POR ADELANTADO',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => false,
                'parent_code' => '11',
            ],
            [
                'code' => '110401',
                'name' => 'Gastos Pagados por Adelantado',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1104',
            ],
            [
                'code' => '110402',
                'name' => 'Seguros Pagados por Adelantado',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1104',
            ],
            [
                'code' => '110403',
                'name' => 'Anticipos a Proveedores',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1104',
            ],
            [
                'code' => '110404',
                'name' => 'Anticipos a Empleados',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1104',
            ],
            [
                'code' => '110405',
                'name' => 'Imp.S/Renta Pag. por Adelantado',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1104',
            ],
            [
                'code' => '12',
                'name' => 'ACTIVOS NO CORRIENTES',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => false,
                'parent_code' => '1',
            ],
            [
                'code' => '1201',
                'name' => 'PROPIEDAD, PLANTA Y EQUIPO',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => false,
                'parent_code' => '12',
            ],
            [
                'code' => '120102',
                'name' => 'Edificios ',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1201',
            ],
            [
                'code' => '120103',
                'name' => 'Consultorios',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1201',
            ],
            [
                'code' => '120104',
                'name' => 'Equipos Médicos',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1201',
            ],
            [
                'code' => '120105',
                'name' => 'Equipos de Laboratorio',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1201',
            ],
            [
                'code' => '120106',
                'name' => 'Equipo Rodante',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1201',
            ],
            [
                'code' => '120107',
                'name' => 'Equipo de Cómputo',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1201',
            ],
            [
                'code' => '120108',
                'name' => 'Mobiliario y Enseres',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1201',
            ],
            [
                'code' => '120120',
                'name' => 'Mejoras a la propiedad arrendada',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1201',
            ],
            [
                'code' => '120130',
                'name' => 'Gastos de Organización',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1201',
            ],
            [
                'code' => '1202',
                'name' => 'DEPRECIACIÓN ACUMULADA',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => false,
                'parent_code' => '12',
            ],
            [
                'code' => '120202',
                'name' => 'Depreciación Acumulada Edificios',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1202',
            ],
            [
                'code' => '120203',
                'name' => 'Depreciación Acumulada Consultorios',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1202',
            ],
            [
                'code' => '120204',
                'name' => 'Depreciación Acumulada Equipos Médicos',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1202',
            ],
            [
                'code' => '120205',
                'name' => 'Depreciación Acumulada Equipos de Laboratorio',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1202',
            ],
            [
                'code' => '120206',
                'name' => 'Depreciación Acumulada Equipo Rodante',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1202',
            ],
            [
                'code' => '120207',
                'name' => 'Depreciación Acumulada Equipo de Cómputo',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1202',
            ],
            [
                'code' => '120208',
                'name' => 'Depreciación Acumulada Mobiliario y Enseres',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1202',
            ],
            [
                'code' => '120220',
                'name' => 'Amortización Mejoras a la propiedad arrendada',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1202',
            ],
            [
                'code' => '120230',
                'name' => 'Amortización Gastos de Organización',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1202',
            ],
            [
                'code' => '1203',
                'name' => 'OTROS ACTIVOS',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => false,
                'parent_code' => '12',
            ],
            [
                'code' => '120301',
                'name' => 'Depósitos en Garantía',
                'account_type' => AccountType::ASSET,
                'allows_transaction' => true,
                'parent_code' => '1203',
            ],
            [
                'code' => '2',
                'name' => 'PASIVOS',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => false,
            ],
            [
                'code' => '21',
                'name' => 'PASIVOS CORRIENTES',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => false,
                'parent_code' => '2',
            ],
            [
                'code' => '2101',
                'name' => 'CUENTAS POR PAGAR',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => false,
                'parent_code' => '21',
            ],
            [
                'code' => '210101',
                'name' => 'Cuentas por Pagar Proveedores Nacionales',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
                'parent_code' => '2101',
            ],
            [
                'code' => '210102',
                'name' => 'Cuentas por Pagar Accionistas',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
                'parent_code' => '2101',
            ],
            [
                'code' => '210103',
                'name' => 'Servicios Profesionales por Pagar',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
                'parent_code' => '2101',
            ],
            [
                'code' => '210104',
                'name' => 'Honorarios Médicos por Pagar',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
                'parent_code' => '2101',
            ],
            [
                'code' => '210105',
                'name' => 'Comisiones Médicas por Pagar',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
                'parent_code' => '2101',
            ],
            [
                'code' => '210106',
                'name' => 'Cuentas por Pagar Otros',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
                'parent_code' => '2101',
            ],
            [
                'code' => '2102',
                'name' => 'OBLIGACIONES FISCALES',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => false,
                'parent_code' => '21',
            ],
            [
                'code' => '210201',
                'name' => 'ITBMS por Pagar',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
                'parent_code' => '2102',
            ],
            [
                'code' => '210202',
                'name' => 'Retenciones por Pagar',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
                'parent_code' => '2102',
            ],
            [
                'code' => '210203',
                'name' => 'Impuesto sobre la Renta por Pagar',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
                'parent_code' => '2102',
            ],
            [
                'code' => '2103',
                'name' => 'OBLIGACIONES LABORALES',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => false,
                'parent_code' => '21',
            ],
            [
                'code' => '210301',
                'name' => 'Salarios por Pagar',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
                'parent_code' => '2103',
            ],
            [
                'code' => '210302',
                'name' => 'Retenciones CSS por Pagar',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
                'parent_code' => '2103',
            ],
            [
                'code' => '210303',
                'name' => 'Descuentos Empleados por Pagar',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
                'parent_code' => '2103',
            ],
            [
                'code' => '210304',
                'name' => 'Vacaciones por Pagar',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
                'parent_code' => '2103',
            ],
            [
                'code' => '210305',
                'name' => 'Décimo Tercer Mes por Pagar',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
                'parent_code' => '2103',
            ],
            [
                'code' => '210306',
                'name' => 'Prima de Antigüedad por Pagar',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
                'parent_code' => '2103',
            ],
            [
                'code' => '2104',
                'name' => 'ANTICIPOS DE CLIENTES',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => false,
                'parent_code' => '21',
            ],
            [
                'code' => '210401',
                'name' => 'Anticipos de Pacientes',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
                'parent_code' => '2104',
            ],
            [
                'code' => '210402',
                'name' => 'Anticipos de Empresas',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
                'parent_code' => '2104',
            ],
            [
                'code' => '210403',
                'name' => 'Anticipos de Aseguradoras',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
                'parent_code' => '2104',
            ],
            [
                'code' => '22',
                'name' => 'PASIVOS NO CORRIENTES',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => false,
                'parent_code' => '2',
            ],
            [
                'code' => '2201',
                'name' => 'PRÉSTAMOS Y FINANCIAMIENTOS',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => false,
                'parent_code' => '22',
            ],
            [
                'code' => '220101',
                'name' => 'Préstamos Bancarios por pagar a largo plazo',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
                'parent_code' => '2201',
            ],
            [
                'code' => '220102',
                'name' => 'Arrendamientos Financieros',
                'account_type' => AccountType::LIABILITY,
                'allows_transaction' => true,
                'parent_code' => '2201',
            ],
            [
                'code' => '3',
                'name' => 'PATRIMONIO',
                'account_type' => AccountType::EQUITY,
                'allows_transaction' => false,
            ],
            [
                'code' => '31',
                'name' => 'Capital Pagado',
                'account_type' => AccountType::EQUITY,
                'allows_transaction' => true,
                'parent_code' => '3',
            ],
            [
                'code' => '32',
                'name' => 'Aportes Adicionales',
                'account_type' => AccountType::EQUITY,
                'allows_transaction' => true,
                'parent_code' => '3',
            ],
            [
                'code' => '33',
                'name' => 'Utilidades del Período',
                'account_type' => AccountType::EQUITY,
                'allows_transaction' => true,
                'parent_code' => '3',
            ],
            [
                'code' => '34',
                'name' => 'Ajustes de Períodos Anteriores',
                'account_type' => AccountType::EQUITY,
                'allows_transaction' => true,
                'parent_code' => '3',
            ],
            [
                'code' => '35',
                'name' => 'Utilidades Retenidas',
                'account_type' => AccountType::EQUITY,
                'allows_transaction' => true,
                'parent_code' => '3',
            ],
            [
                'code' => '36',
                'name' => 'Impuesto Complementario',
                'account_type' => AccountType::EQUITY,
                'allows_transaction' => true,
                'parent_code' => '3',
            ],
            [
                'code' => '4',
                'name' => 'INGRESOS',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => false,
            ],
            [
                'code' => '41',
                'name' => 'SERVICIOS MÉDICOS',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => false,
                'parent_code' => '4',
            ],
            [
                'code' => '4101',
                'name' => 'Ingresos Consultas Medicina General',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => true,
                'parent_code' => '41',
            ],
            [
                'code' => '4102',
                'name' => 'Ingresos Consultas Especializadas',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => true,
                'parent_code' => '41',
            ],
            [
                'code' => '4103',
                'name' => 'Ingresos Procedimientos Médicos',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => true,
                'parent_code' => '41',
            ],
            [
                'code' => '4104',
                'name' => 'Ingresos Cirugías',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => true,
                'parent_code' => '41',
            ],
            [
                'code' => '4105',
                'name' => 'Ingresos Hospitalización',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => true,
                'parent_code' => '41',
            ],
            [
                'code' => '4106',
                'name' => 'Ingresos Urgencias',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => true,
                'parent_code' => '41',
            ],
            [
                'code' => '42',
                'name' => 'SERVICIOS COMPLEMENTARIOS',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => false,
                'parent_code' => '4',
            ],
            [
                'code' => '4201',
                'name' => 'Ingresos Laboratorio',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => true,
                'parent_code' => '42',
            ],
            [
                'code' => '4202',
                'name' => 'Ingresos Radiología',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => true,
                'parent_code' => '42',
            ],
            [
                'code' => '4203',
                'name' => 'Ingresos Ultrasonidos',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => true,
                'parent_code' => '42',
            ],
            [
                'code' => '4204',
                'name' => 'Ingresos Farmacia',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => true,
                'parent_code' => '42',
            ],
            [
                'code' => '4205',
                'name' => 'Ingresos Fisioterapia',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => true,
                'parent_code' => '42',
            ],
            [
                'code' => '43',
                'name' => 'ESTÉTICA Y BIENESTAR',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => false,
                'parent_code' => '4',
            ],
            [
                'code' => '4301',
                'name' => 'Ingresos Dermatología Estética',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => true,
                'parent_code' => '43',
            ],
            [
                'code' => '4302',
                'name' => 'Ingresos Botox',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => true,
                'parent_code' => '43',
            ],
            [
                'code' => '4303',
                'name' => 'Ingresos Tratamientos Faciales',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => true,
                'parent_code' => '43',
            ],
            [
                'code' => '4304',
                'name' => 'Ingresos Paquetes Estéticos',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => true,
                'parent_code' => '43',
            ],
            [
                'code' => '45',
                'name' => 'OTROS INGRESOS',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => false,
                'parent_code' => '4',
            ],
            [
                'code' => '4501',
                'name' => 'Ingresos Financieros',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => true,
                'parent_code' => '45',
            ],
            [
                'code' => '4502',
                'name' => 'Otros Ingresos',
                'account_type' => AccountType::INCOME,
                'allows_transaction' => true,
                'parent_code' => '45',
            ],
            [
                'code' => '5',
                'name' => 'COSTOS',
                'account_type' => AccountType::COST,
                'allows_transaction' => false,
            ],
            [
                'code' => '51',
                'name' => 'COSTOS DE VENTAS',
                'account_type' => AccountType::COST,
                'allows_transaction' => false,
                'parent_code' => '5',
            ],
            [
                'code' => '5101',
                'name' => 'Medicamentos Consumidos',
                'account_type' => AccountType::COST,
                'allows_transaction' => true,
                'parent_code' => '51',
            ],
            [
                'code' => '5105',
                'name' => 'Material Médico',
                'account_type' => AccountType::COST,
                'allows_transaction' => true,
                'parent_code' => '51',
            ],
            [
                'code' => '5110',
                'name' => 'Material Quirúrgico',
                'account_type' => AccountType::COST,
                'allows_transaction' => true,
                'parent_code' => '51',
            ],
            [
                'code' => '5115',
                'name' => 'Reactivos',
                'account_type' => AccountType::COST,
                'allows_transaction' => true,
                'parent_code' => '51',
            ],
            [
                'code' => '5120',
                'name' => 'Laboratorio',
                'account_type' => AccountType::COST,
                'allows_transaction' => true,
                'parent_code' => '51',
            ],
            [
                'code' => '5125',
                'name' => 'Costos Procedimientos',
                'account_type' => AccountType::COST,
                'allows_transaction' => true,
                'parent_code' => '51',
            ],
            [
                'code' => '5130',
                'name' => 'Costos Hospitalización',
                'account_type' => AccountType::COST,
                'allows_transaction' => true,
                'parent_code' => '51',
            ],
            [
                'code' => '5140',
                'name' => 'Honorarios Médicos',
                'account_type' => AccountType::COST,
                'allows_transaction' => true,
                'parent_code' => '51',
            ],
            [
                'code' => '51100',
                'name' => 'Ajuste de Inventarios',
                'account_type' => AccountType::COST,
                'allows_transaction' => true,
                'parent_code' => '51',
            ],
            [
                'code' => '51200',
                'name' => 'Devolución en Compras',
                'account_type' => AccountType::COST,
                'allows_transaction' => true,
                'parent_code' => '51',
            ],
            [
                'code' => '51300',
                'name' => 'Descuento en Compras',
                'account_type' => AccountType::COST,
                'allows_transaction' => true,
                'parent_code' => '51',
            ],
            [
                'code' => '51400',
                'name' => 'Otros Costos',
                'account_type' => AccountType::COST,
                'allows_transaction' => true,
                'parent_code' => '51',
            ],
            [
                'code' => '6',
                'name' => 'GASTOS',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => false,
            ],
            [
                'code' => '61',
                'name' => 'GASTOS GENERALES Y ADMINISTRATIVOS',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => false,
                'parent_code' => '6',
            ],
            [
                'code' => '6101',
                'name' => 'Salarios Administrativos',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '61',
            ],
            [
                'code' => '6102',
                'name' => 'Vacaciones',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '61',
            ],
            [
                'code' => '6103',
                'name' => 'XIII Mes',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '61',
            ],
            [
                'code' => '6104',
                'name' => 'Prima de Antigüedad',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '61',
            ],
            [
                'code' => '6110',
                'name' => 'Cuota Patronal CSS',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '61',
            ],
            [
                'code' => '6120',
                'name' => 'Honorarios Profesionales',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '61',
            ],
            [
                'code' => '6130',
                'name' => 'Alquileres',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '61',
            ],
            [
                'code' => '6140',
                'name' => 'Agua',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '61',
            ],
            [
                'code' => '6145',
                'name' => 'Electricidad',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '61',
            ],
            [
                'code' => '6150',
                'name' => 'Teléfono',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '61',
            ],
            [
                'code' => '6155',
                'name' => 'Viático y Transporte',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '61',
            ],
            [
                'code' => '6160',
                'name' => 'Papelería y Útiles de Oficina',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '61',
            ],
            [
                'code' => '6170',
                'name' => 'Reparaciones y Mantenimiento',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '61',
            ],
            [
                'code' => '6180',
                'name' => 'Seguros',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '61',
            ],
            [
                'code' => '6190',
                'name' => 'Publicidad',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '61',
            ],
            [
                'code' => '61100',
                'name' => 'Impuestos',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '61',
            ],
            [
                'code' => '61150',
                'name' => 'Aseo y Limpieza',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '61',
            ],
            [
                'code' => '61200',
                'name' => 'Mensajería',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '61',
            ],
            [
                'code' => '61250',
                'name' => 'Cuentas Incobrables',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '61',
            ],
            [
                'code' => '62',
                'name' => 'DEPRECIACION Y AMORTIZACION',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => false,
                'parent_code' => '6',
            ],
            [
                'code' => '6201',
                'name' => 'Gastos de Depreciación',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '62',
            ],
            [
                'code' => '6202',
                'name' => 'Gastos de Amortización',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '62',
            ],
            [
                'code' => '63',
                'name' => 'GASTOS FINANCIEROS',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => false,
                'parent_code' => '6',
            ],
            [
                'code' => '6301',
                'name' => 'Intereses Bancarios',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '63',
            ],
            [
                'code' => '6310',
                'name' => 'Comisiones Bancarias',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '63',
            ],
            [
                'code' => '6320',
                'name' => 'Diferencial Cambiario',
                'account_type' => AccountType::EXPENSE,
                'allows_transaction' => true,
                'parent_code' => '63',
            ],
        ];
    }
}
