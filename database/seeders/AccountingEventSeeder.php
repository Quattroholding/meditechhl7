<?php

namespace Database\Seeders;

use App\Models\Integration\AccountingEvent;
use Illuminate\Database\Seeder;

class AccountingEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accountingEvents = [
            // FACTURACIÓN (EVT-001 a EVT-004)
            [
                'code' => 'EVT-001',
                'name' => 'Factura de Contado',
                'description' => 'Registro de factura pagada al contado - DR: Caja/Banco, CR: Ingresos Médicos',
                'module' => 'billing',
            ],
            [
                'code' => 'EVT-002',
                'name' => 'Factura a Crédito',
                'description' => 'Registro de factura a crédito - DR: Cuentas por Cobrar, CR: Ingresos Médicos',
                'module' => 'billing',
            ],
            [
                'code' => 'EVT-003',
                'name' => 'Pago de Factura',
                'description' => 'Registro de cobro de factura pendiente - DR: Caja/Banco, CR: Cuentas por Cobrar',
                'module' => 'billing',
            ],
            [
                'code' => 'EVT-004',
                'name' => 'Devolución de Factura',
                'description' => 'Registro de devolución de factura - DR: Devoluciones, CR: Caja/Banco o CxC',
                'module' => 'billing',
            ],

            // COMPRAS (EVT-005 a EVT-008)
            [
                'code' => 'EVT-005',
                'name' => 'Compra a Proveedores',
                'description' => 'Registro de compra de inventario o servicios - DR: Inventario, CR: Cuentas por Pagar',
                'module' => 'purchases',
            ],
            [
                'code' => 'EVT-006',
                'name' => 'Pago a Proveedores',
                'description' => 'Registro de pago a proveedores - DR: Cuentas por Pagar, CR: Caja/Banco',
                'module' => 'purchases',
            ],
            [
                'code' => 'EVT-007',
                'name' => 'Devolución de Compra',
                'description' => 'Registro de devolución de compra - DR: Cuentas por Pagar, CR: Inventario',
                'module' => 'purchases',
            ],
            [
                'code' => 'EVT-008',
                'name' => 'Recepción de Mercadería',
                'description' => 'Registro de ingreso de inventario - DR: Inventario, CR: Cuenta Transitoria',
                'module' => 'inventory',
            ],

            // TESORERÍA (EVT-009 a EVT-012)
            [
                'code' => 'EVT-009',
                'name' => 'Cobro Manual',
                'description' => 'Registro de cobro manual en caja/banco - DR: Caja/Banco, CR: Ingresos',
                'module' => 'treasury',
            ],
            [
                'code' => 'EVT-010',
                'name' => 'Pago Manual',
                'description' => 'Registro de pago manual desde caja/banco - DR: Gastos, CR: Caja/Banco',
                'module' => 'treasury',
            ],
            [
                'code' => 'EVT-011',
                'name' => 'Transferencia Bancaria',
                'description' => 'Registro de transferencia entre cuentas bancarias - DR: Banco Destino, CR: Banco Origen',
                'module' => 'treasury',
            ],
            [
                'code' => 'EVT-012',
                'name' => 'Comisiones Bancarias',
                'description' => 'Registro de comisiones e intereses bancarios - DR: Gastos Financieros, CR: Banco',
                'module' => 'treasury',
            ],

            // NÓMINA (EVT-013 a EVT-016)
            [
                'code' => 'EVT-013',
                'name' => 'Planilla de Sueldos',
                'description' => 'Registro de devengación de sueldos - DR: Gastos de Personal, CR: Salarios por Pagar',
                'module' => 'payroll',
            ],
            [
                'code' => 'EVT-014',
                'name' => 'Retenciones',
                'description' => 'Registro de retenciones (CSS, Impuestos) - DR: Salarios por Pagar, CR: Retenciones por Pagar',
                'module' => 'payroll',
            ],
            [
                'code' => 'EVT-015',
                'name' => 'Pago de Planilla',
                'description' => 'Registro de pago de nómina - DR: Salarios por Pagar, CR: Caja/Banco',
                'module' => 'payroll',
            ],
            [
                'code' => 'EVT-016',
                'name' => 'Bono/Gratificación',
                'description' => 'Registro de bonificación adicional - DR: Gastos Bonificación, CR: Salarios por Pagar',
                'module' => 'payroll',
            ],

            // OTROS (EVT-017 a EVT-019)
            [
                'code' => 'EVT-017',
                'name' => 'Movimiento de Inventario',
                'description' => 'Registro de movimiento de inventario - DR/CR: Inventario, Costo de Ventas',
                'module' => 'inventory',
            ],
            [
                'code' => 'EVT-018',
                'name' => 'Ajuste Manual',
                'description' => 'Registro de asiento manual para correcciones - Flexible según necesidad',
                'module' => 'general',
            ],
            [
                'code' => 'EVT-019',
                'name' => 'Cancelación Incobrables',
                'description' => 'Registro de cancelación de cuentas incobrables - DR: Provisión CxC, CR: Cuentas por Cobrar',
                'module' => 'billing',
            ],

            // RECTIFICACIONES (EVT-R001 a EVT-R005)
            [
                'code' => 'EVT-R001',
                'name' => 'Reversión de Asiento',
                'description' => 'Reversión completa de un asiento anterior',
                'module' => 'general',
            ],
            [
                'code' => 'EVT-R002',
                'name' => 'Corrección de Error',
                'description' => 'Corrección de errores en asientos contables',
                'module' => 'general',
            ],
            [
                'code' => 'EVT-R003',
                'name' => 'Ajuste Período Anterior',
                'description' => 'Ajuste de registros de períodos anteriores',
                'module' => 'general',
            ],
            [
                'code' => 'EVT-R004',
                'name' => 'Depreciación',
                'description' => 'Registro automático de depreciación de activos',
                'module' => 'general',
            ],
            [
                'code' => 'EVT-R005',
                'name' => 'Amortización',
                'description' => 'Registro automático de amortización de intangibles',
                'module' => 'general',
            ],
        ];

        $createdCount = 0;

        foreach ($accountingEvents as $data) {
            // updateOrCreate usando code como llave
            $event = AccountingEvent::updateOrCreate(
                ['code' => $data['code']],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'module' => $data['module'],
                    'status' => 'active',
                ]
            );

            if ($event->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        if ($createdCount > 0) {
            $this->command->info("Created {$createdCount} accounting events.");
        } else {
            $this->command->info('All accounting events already exist.');
        }

        $this->command->info('AccountingEventSeeder completed successfully.');
    }
}
